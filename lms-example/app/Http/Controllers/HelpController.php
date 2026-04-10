<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Display the help page based on user role.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user && $user->isAdmin()) {
            return view('help.admin');
        }
        
        if ($user && $user->canEditCourses()) {
            return view('help.facilitator');
        }
        
        return view('help.index');
    }
    
    /**
     * Display admin help page.
     */
    public function admin()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }
        return view('help.admin');
    }
    
    /**
     * Display facilitator help page.
     */
    public function facilitator()
    {
        if (!auth()->check() || !auth()->user()->canEditCourses()) {
            abort(403);
        }
        return view('help.facilitator');
    }
}
