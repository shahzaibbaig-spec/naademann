#!/usr/bin/env python3
from __future__ import annotations

import argparse
import sqlite3
from collections import defaultdict
from datetime import datetime, timezone
from pathlib import Path


def quote_mysql(identifier: str) -> str:
    return f"`{identifier.replace('`', '``')}`"


def quote_sqlite(identifier: str) -> str:
    return f'"{identifier.replace("\"", "\"\"")}"'


def escape_sql_string(value: str) -> str:
    escaped = (
        value.replace("\\", "\\\\")
        .replace("\0", "\\0")
        .replace("\n", "\\n")
        .replace("\r", "\\r")
        .replace("\x1a", "\\Z")
        .replace("'", "''")
    )
    return f"'{escaped}'"


def render_literal(value: object) -> str:
    if value is None:
        return "NULL"

    if isinstance(value, bool):
        return "1" if value else "0"

    if isinstance(value, (int, float)):
        return str(value)

    if isinstance(value, bytes):
        return f"0x{value.hex()}"

    return escape_sql_string(str(value))


def mysql_type(column_name: str, declared_type: str, is_primary_key: bool) -> str:
    declared = declared_type.lower().strip()

    if declared.startswith("tinyint"):
        return "TINYINT(1)"

    if "int" in declared:
        if is_primary_key or column_name.endswith("_id"):
            return "BIGINT UNSIGNED"
        return "INT"

    if declared.startswith("varchar") or declared in {"varchar", "string"}:
        return "VARCHAR(255)"

    if declared.startswith("char"):
        return "VARCHAR(255)"

    if declared == "datetime":
        return "DATETIME"

    if declared == "date":
        return "DATE"

    if declared == "text":
        return "LONGTEXT"

    if declared.startswith("decimal"):
        return declared.upper()

    if declared:
        return declared.upper()

    if is_primary_key or column_name.endswith("_id"):
        return "BIGINT UNSIGNED"

    return "LONGTEXT"


def render_default(default_value: object, rendered_type: str) -> str:
    if default_value is None:
        return ""

    if rendered_type.endswith("TEXT"):
        return ""

    raw = str(default_value).strip()
    upper = raw.upper()

    if upper in {"CURRENT_TIMESTAMP", "CURRENT_TIMESTAMP()"}:
        return " DEFAULT CURRENT_TIMESTAMP"

    if upper == "NULL":
        return " DEFAULT NULL"

    is_numeric_type = rendered_type.startswith(("TINYINT", "INT", "BIGINT", "DECIMAL", "DOUBLE", "FLOAT"))

    if raw.startswith("'") and raw.endswith("'"):
        inner = raw[1:-1].replace("''", "'")
        if is_numeric_type:
            return f" DEFAULT {inner}"
        return f" DEFAULT {escape_sql_string(inner)}"

    if is_numeric_type:
        return f" DEFAULT {raw}"

    return f" DEFAULT {escape_sql_string(raw)}"


def trim_identifier(name: str, suffix: str = "") -> str:
    limit = 64
    if len(name) <= limit:
        return name

    reserved = len(suffix)
    head = max(limit - reserved, 1)
    return f"{name[:head]}{suffix}"


def fetch_tables(connection: sqlite3.Connection) -> list[dict[str, object]]:
    tables: list[dict[str, object]] = []
    rows = connection.execute(
        """
        SELECT name, sql
        FROM sqlite_master
        WHERE type = 'table'
          AND name NOT LIKE 'sqlite_%'
        ORDER BY name
        """
    ).fetchall()

    for table_name, create_sql in rows:
        columns = []
        for column in connection.execute(f"PRAGMA table_info({quote_sqlite(table_name)})").fetchall():
            columns.append(
                {
                    "cid": column[0],
                    "name": column[1],
                    "type": column[2] or "",
                    "notnull": bool(column[3]),
                    "default": column[4],
                    "pk": int(column[5]),
                }
            )

        indexes = []
        for index in connection.execute(f"PRAGMA index_list({quote_sqlite(table_name)})").fetchall():
            index_name = index[1]
            if index_name.startswith("sqlite_autoindex_"):
                continue

            index_columns = [
                entry[2]
                for entry in connection.execute(f"PRAGMA index_info({quote_sqlite(index_name)})").fetchall()
            ]

            indexes.append(
                {
                    "name": index_name,
                    "unique": bool(index[2]),
                    "columns": index_columns,
                }
            )

        fk_groups: dict[int, list[tuple]] = defaultdict(list)
        for fk in connection.execute(f"PRAGMA foreign_key_list({quote_sqlite(table_name)})").fetchall():
            fk_groups[int(fk[0])].append(fk)

        foreign_keys = []
        for fk_id in sorted(fk_groups):
            entries = sorted(fk_groups[fk_id], key=lambda item: int(item[1]))
            from_columns = [entry[3] for entry in entries]
            to_columns = [entry[4] for entry in entries]
            referenced_table = entries[0][2]
            constraint_name = trim_identifier(
                f"{table_name}_{'_'.join(from_columns)}_foreign",
                suffix=f"_{fk_id}",
            )
            foreign_keys.append(
                {
                    "name": constraint_name,
                    "from_columns": from_columns,
                    "to_table": referenced_table,
                    "to_columns": to_columns,
                    "on_update": entries[0][5],
                    "on_delete": entries[0][6],
                }
            )

        tables.append(
            {
                "name": table_name,
                "create_sql": create_sql or "",
                "columns": columns,
                "indexes": indexes,
                "foreign_keys": foreign_keys,
            }
        )

    return tables


def render_create_table(table: dict[str, object]) -> str:
    columns: list[dict[str, object]] = table["columns"]  # type: ignore[assignment]
    create_sql: str = table["create_sql"]  # type: ignore[assignment]
    primary_key_columns = [
        column for column in sorted(columns, key=lambda item: item["pk"]) if column["pk"]
    ]
    has_autoincrement = "AUTOINCREMENT" in create_sql.upper()

    lines = []
    for column in columns:
        is_primary_key = bool(column["pk"])
        rendered_type = mysql_type(str(column["name"]), str(column["type"]), is_primary_key)
        nullability = "NOT NULL" if is_primary_key or bool(column["notnull"]) else "NULL"
        default = render_default(column["default"], rendered_type)
        auto_increment = " AUTO_INCREMENT" if has_autoincrement and is_primary_key and len(primary_key_columns) == 1 else ""
        lines.append(
            f"  {quote_mysql(str(column['name']))} {rendered_type} {nullability}{default}{auto_increment}"
        )

    if primary_key_columns:
        pk = ", ".join(quote_mysql(str(column["name"])) for column in primary_key_columns)
        lines.append(f"  PRIMARY KEY ({pk})")

    body = ",\n".join(lines)
    return (
        f"DROP TABLE IF EXISTS {quote_mysql(str(table['name']))};\n"
        f"CREATE TABLE {quote_mysql(str(table['name']))} (\n{body}\n)"
        " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n"
    )


def render_insert_statements(connection: sqlite3.Connection, table: dict[str, object], batch_size: int = 250) -> str:
    table_name = str(table["name"])
    columns: list[dict[str, object]] = table["columns"]  # type: ignore[assignment]
    column_names = [str(column["name"]) for column in columns]
    pk_columns = [str(column["name"]) for column in sorted(columns, key=lambda item: item["pk"]) if column["pk"]]

    select_sql = f"SELECT * FROM {quote_sqlite(table_name)}"
    if pk_columns:
        order_by = ", ".join(quote_sqlite(column_name) for column_name in pk_columns)
        select_sql += f" ORDER BY {order_by}"

    rows = connection.execute(select_sql).fetchall()
    if not rows:
        return ""

    rendered_columns = ", ".join(quote_mysql(column_name) for column_name in column_names)
    inserts: list[str] = []

    for offset in range(0, len(rows), batch_size):
        batch = rows[offset : offset + batch_size]
        values = []
        for row in batch:
            literals = ", ".join(render_literal(value) for value in row)
            values.append(f"({literals})")
        values_sql = ",\n".join(values)
        inserts.append(
            f"INSERT INTO {quote_mysql(table_name)} ({rendered_columns}) VALUES\n{values_sql};"
        )

    return "\n".join(inserts) + "\n"


def render_indexes(table: dict[str, object]) -> str:
    statements = []
    for index in table["indexes"]:  # type: ignore[index]
        columns = ", ".join(quote_mysql(column) for column in index["columns"])
        index_name = quote_mysql(str(index["name"]))
        table_name = quote_mysql(str(table["name"]))
        if index["unique"]:
            statements.append(f"ALTER TABLE {table_name} ADD UNIQUE KEY {index_name} ({columns});")
        else:
            statements.append(f"ALTER TABLE {table_name} ADD KEY {index_name} ({columns});")

    if not statements:
        return ""

    return "\n".join(statements) + "\n"


def render_foreign_keys(table: dict[str, object]) -> str:
    statements = []
    for foreign_key in table["foreign_keys"]:  # type: ignore[index]
        from_columns = ", ".join(quote_mysql(column) for column in foreign_key["from_columns"])
        to_columns = ", ".join(quote_mysql(column) for column in foreign_key["to_columns"])
        statement = (
            f"ALTER TABLE {quote_mysql(str(table['name']))} "
            f"ADD CONSTRAINT {quote_mysql(str(foreign_key['name']))} "
            f"FOREIGN KEY ({from_columns}) "
            f"REFERENCES {quote_mysql(str(foreign_key['to_table']))} ({to_columns}) "
            f"ON DELETE {str(foreign_key['on_delete']).upper()} "
            f"ON UPDATE {str(foreign_key['on_update']).upper()};"
        )
        statements.append(statement)

    if not statements:
        return ""

    return "\n".join(statements) + "\n"


def render_auto_increment(connection: sqlite3.Connection, table: dict[str, object]) -> str:
    create_sql = str(table["create_sql"])
    if "AUTOINCREMENT" not in create_sql.upper():
        return ""

    primary_key_columns = [
        column for column in sorted(table["columns"], key=lambda item: item["pk"]) if column["pk"]  # type: ignore[index]
    ]
    if len(primary_key_columns) != 1:
        return ""

    pk_name = str(primary_key_columns[0]["name"])
    table_name = str(table["name"])
    next_value = connection.execute(
        f"SELECT COALESCE(MAX({quote_sqlite(pk_name)}), 0) + 1 FROM {quote_sqlite(table_name)}"
    ).fetchone()[0]
    return f"ALTER TABLE {quote_mysql(table_name)} AUTO_INCREMENT = {int(next_value)};\n"


def build_export(connection: sqlite3.Connection) -> str:
    tables = fetch_tables(connection)
    generated_at = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC")
    sections = [
        "-- MariaDB import dump generated from SQLite",
        f"-- Generated at: {generated_at}",
        "",
        "SET NAMES utf8mb4;",
        "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';",
        "SET FOREIGN_KEY_CHECKS = 0;",
        "",
    ]

    for table in tables:
        sections.append(f"-- Table structure for {table['name']}")
        sections.append(render_create_table(table))

    for table in tables:
        insert_sql = render_insert_statements(connection, table)
        if insert_sql:
            sections.append(f"-- Data for {table['name']}")
            sections.append(insert_sql)

    for table in tables:
        index_sql = render_indexes(table)
        if index_sql:
            sections.append(f"-- Indexes for {table['name']}")
            sections.append(index_sql)

    for table in tables:
        fk_sql = render_foreign_keys(table)
        if fk_sql:
            sections.append(f"-- Foreign keys for {table['name']}")
            sections.append(fk_sql)

    for table in tables:
        auto_increment_sql = render_auto_increment(connection, table)
        if auto_increment_sql:
            sections.append(f"-- Auto increment for {table['name']}")
            sections.append(auto_increment_sql)

    sections.extend(
        [
            "SET FOREIGN_KEY_CHECKS = 1;",
            "",
        ]
    )

    return "\n".join(sections)


def parse_args() -> argparse.Namespace:
    script_dir = Path(__file__).resolve().parent
    default_input = script_dir.parent.parent / "backend" / "database" / "database.sqlite"
    default_output = script_dir / "naad_e_mann_mariadb.sql"

    parser = argparse.ArgumentParser(
        description="Export the local Laravel SQLite database as MariaDB-compatible SQL."
    )
    parser.add_argument("--input", type=Path, default=default_input, help="Path to the SQLite database file.")
    parser.add_argument("--output", type=Path, default=default_output, help="Path for the MariaDB SQL dump.")
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    input_path = args.input.resolve()
    output_path = args.output.resolve()

    if not input_path.exists():
        raise SystemExit(f"SQLite database not found: {input_path}")

    output_path.parent.mkdir(parents=True, exist_ok=True)

    connection = sqlite3.connect(str(input_path))
    try:
        sql_dump = build_export(connection)
    finally:
        connection.close()

    output_path.write_text(sql_dump, encoding="utf-8", newline="\n")
    print(f"MariaDB SQL export created at: {output_path}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
