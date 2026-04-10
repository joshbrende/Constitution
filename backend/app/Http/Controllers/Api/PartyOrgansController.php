<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PartyOrgan;
use Illuminate\Http\JsonResponse;

class PartyOrgansController extends Controller
{
    /**
     * List published party organs (order, name, slug, short_description).
     */
    public function index(): JsonResponse
    {
        $organs = PartyOrgan::published()
            ->ordered()
            ->get(['id', 'name', 'slug', 'short_description', 'order']);

        $data = $organs->map(fn (PartyOrgan $o) => [
            'id' => $o->id,
            'name' => $o->name,
            'slug' => $o->slug,
            'short_description' => $o->short_description,
            'order' => $o->order,
        ]);

        return response()->json(['data' => $data]);
    }

    /**
     * Single party organ with body (for detail view).
     */
    public function show(PartyOrgan $party_organ): JsonResponse
    {
        if (! $party_organ->is_published) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $party_organ->id,
                'name' => $party_organ->name,
                'slug' => $party_organ->slug,
                'short_description' => $party_organ->short_description,
                'body' => $party_organ->body,
                'order' => $party_organ->order,
            ],
        ]);
    }
}
