<?php

namespace App\Http\Controllers;

use App\Models\StaticPage;
use Illuminate\Http\Response;

class LegalPagesController extends Controller
{
    public function show(string $slug): Response
    {
        $page = StaticPage::published()->where('slug', $slug)->first();

        if (! $page) {
            abort(404);
        }

        return response()
            ->view('legal.page', ['page' => $page]);
    }
}

