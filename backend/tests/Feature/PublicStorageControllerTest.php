<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicStorageControllerTest extends TestCase
{
    public function test_it_serves_existing_public_storage_files(): void
    {
        Storage::disk('public')->put('uploads/test-suite/cover.jpg', 'cover-bytes');

        $response = $this->get('/storage/uploads/test-suite/cover.jpg');

        $response->assertOk();
        $response->assertHeader('cache-control', 'max-age=31536000, public');
    }

    public function test_it_returns_404_for_missing_public_storage_files(): void
    {
        $response = $this->get('/storage/uploads/test-suite/missing.mp3');

        $response->assertNotFound();
    }
}
