<?php

namespace App\Http\Controllers\Web\Ajax;

use App\Http\Controllers\Web\WebController;
use App\Support\Search\CatalogSearch;
use Illuminate\Http\Request;

class SearchController extends WebController
{
    public function __invoke(Request $request, CatalogSearch $search)
    {
        $query = trim((string) $request->query('q'));
        $results = $search->search($query, 6);
        $tracks = $results['tracks'];
        $artists = $results['artists'];
        $albums = $results['albums'];
        $genres = $results['genres'];

        return response()->json([
            'query' => $results['query'],
            'tracks' => $tracks->map(fn ($song) => [
                'id' => $song->id,
                'title' => $song->title,
                'slug' => $song->slug,
                'cover_image_url' => $song->cover_image_url,
                'artist' => $song->artist?->name,
                'artist_slug' => $song->artist?->slug,
                'album' => $song->album?->title,
                'album_slug' => $song->album?->slug,
                'audio_url' => $song->audio_url,
                'duration' => $song->duration,
                'genre' => $song->genre,
                'href' => route('listen', ['track' => $song->slug]),
            ]),
            'artists' => $artists->map(fn ($artist) => [
                'id' => $artist->id,
                'name' => $artist->name,
                'slug' => $artist->slug,
                'genre' => $artist->genre,
                'image_url' => $artist->image_url,
                'href' => route('artists.show', $artist),
            ]),
            'albums' => $albums->map(fn ($album) => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'genre' => $album->genre,
                'cover_image_url' => $album->cover_image_url,
                'artist' => $album->artist?->name,
                'href' => route('listen', ['album' => $album->slug]),
            ]),
            'genres' => $genres->map(fn ($genre) => [
                'id' => $genre->id,
                'name' => $genre->name,
                'slug' => $genre->slug,
                'description' => $genre->description,
                'image_url' => $genre->image_url,
                'href' => route('listen', ['genre' => $genre->name]),
            ]),
        ]);
    }
}
