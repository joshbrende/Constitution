<?php

namespace App\Http\Controllers;

use App\Models\PartyOrgan;
use Illuminate\View\View;

class WebPartyOrgansController extends Controller
{
    /**
     * Party Organs list (dashboard "Party organs" tile).
     */
    public function index(): View
    {
        $organs = PartyOrgan::published()->ordered()->get();
        $canManage = auth()->user()?->can('admin.contentManage') ?? false;
        return view('sections.party-organs', compact('organs', 'canManage'));
    }

    /**
     * Single party organ (read view).
     */
    public function show(PartyOrgan $party_organ): View
    {
        if (! $party_organ->is_published) {
            abort(404);
        }
        return view('sections.party-organ', ['organ' => $party_organ]);
    }
}
