<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use App\Rules\SafeUrlRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeBannersController extends Controller
{
    public function index(): View
    {
        $banners = HomeBanner::orderBy('sort_order')->orderByDesc('id')->get();

        return view('admin.home-banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.home-banners.form', ['banner' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'home_banners');
        $data = $this->validateBanner($request);
        $data['created_by_user_id'] = $request->user()?->id;
        HomeBanner::create($data);

        return redirect()->route('admin.home-banners.index')
            ->with('success', 'Banner created.');
    }

    public function edit(HomeBanner $home_banner): View
    {
        return view('admin.home-banners.form', ['banner' => $home_banner]);
    }

    public function update(Request $request, HomeBanner $home_banner): RedirectResponse
    {
        $this->authorize('admin.section', 'home_banners');
        $data = $this->validateBanner($request);
        $home_banner->update($data);

        return redirect()->route('admin.home-banners.index')
            ->with('success', 'Banner updated.');
    }

    public function destroy(HomeBanner $home_banner): RedirectResponse
    {
        $this->authorize('admin.section', 'home_banners');
        $home_banner->delete();

        return redirect()->route('admin.home-banners.index')
            ->with('success', 'Banner deleted.');
    }

    private function validateBanner(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:500', new SafeUrlRule],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:500', new SafeUrlRule],
            'cta_type' => ['nullable', 'in:internal,external'],
            'cta_tab' => ['nullable', 'string', 'max:50'],
            'cta_screen' => ['nullable', 'string', 'max:80'],
            'cta_params_json' => ['nullable', 'string', 'max:2000'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_published'] = $request->boolean('is_published', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $paramsJson = $data['cta_params_json'] ?? null;
        unset($data['cta_params_json']);
        $data['cta_params'] = null;
        if ($paramsJson !== null && trim($paramsJson) !== '') {
            $decoded = json_decode($paramsJson, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['cta_params'] = $decoded;
            }
        }

        return $data;
    }
}

