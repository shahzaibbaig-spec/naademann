<?php

namespace App\Http\Controllers\Web;

use App\Support\Search\CatalogSearch;
use Illuminate\Http\Request;

class SearchPageController extends WebController
{
    public function __invoke(Request $request, CatalogSearch $search)
    {
        $query = trim($request->string('q')->toString());
        $results = $search->search($query, 12);

        return view('pages.search', [
            'query' => $query,
            'tracks' => $results['tracks'],
            'artists' => $results['artists'],
            'albums' => $results['albums'],
            'genres' => $results['genres'],
            'playerQueue' => $this->serializeQueueTracks($results['tracks']),
            ...$this->interactionState($request),
        ]);
    }
}
