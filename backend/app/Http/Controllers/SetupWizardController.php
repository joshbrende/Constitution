<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\Setup\InstallationUrlBuilder;
use App\Services\Setup\SetupInstallService;
use App\Services\Setup\SetupProductionChecklist;
use App\Services\Setup\SetupProgressResolver;
use App\Services\Setup\SetupSystemChecker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SetupWizardController extends Controller
{
    public function __construct(
        protected SetupSystemChecker $checker,
        protected SetupInstallService $installService,
        protected SetupProgressResolver $progress,
        protected SetupProductionChecklist $productionChecklist
    ) {}

    public function welcome(): View|RedirectResponse
    {
        $resume = $this->progress->resumeRoute();

        if (in_array($resume, ['setup.finish', 'setup.seed', 'setup.platform'], true)) {
            return redirect()->route($resume);
        }

        return view('setup.welcome', ['step' => 1]);
    }

    public function checks(): View|RedirectResponse
    {
        if ($this->progress->resumeRoute() === 'setup.finish') {
            return redirect()->route('setup.finish');
        }

        $checks = $this->checker->run();
        $canContinue = $this->checker->allCriticalPassed($checks);
        $pendingMigrations = $this->checker->hasPendingMigrations();
        $needsDatabaseProvision = $this->checker->needsDatabaseProvision();

        return view('setup.checks', [
            'checks' => $checks,
            'canContinue' => $canContinue,
            'pendingMigrations' => $pendingMigrations,
            'needsDatabaseProvision' => $needsDatabaseProvision,
            'serverConfig' => $this->serverConfig(),
            'step' => 2,
        ]);
    }

    public function runMigrate(): RedirectResponse
    {
        try {
            $this->installService->provisionDatabase();
        } catch (\Throwable $e) {
            return back()->with('error', 'Database setup failed: '.$e->getMessage());
        }

        return redirect()->route('setup.checks')->with('success', 'Database created and migrations completed.');
    }

    public function continueFromChecks(Request $request): RedirectResponse
    {
        $checks = $this->checker->run();
        if (! $this->checker->allCriticalPassed($checks)) {
            return redirect()->route('setup.checks')->with('error', 'Fix the failed system checks before continuing.');
        }

        if ($this->checker->needsDatabaseProvision()) {
            try {
                $this->installService->provisionDatabase();
            } catch (\Throwable $e) {
                return redirect()->route('setup.checks')->with('error', 'Database setup failed: '.$e->getMessage());
            }
        }

        if ($this->checker->hasPendingMigrations()) {
            return redirect()->route('setup.checks')->with('error', 'Database setup did not complete. Check DB credentials and try again.');
        }

        session(['setup_checks_passed' => true]);

        if ($this->installService->systemAdminExists()) {
            session(['setup_admin_done' => true]);

            return redirect()->route('setup.platform');
        }

        return redirect()->route('setup.admin');
    }

    public function showAdmin(): View|RedirectResponse
    {
        if (! session('setup_checks_passed')) {
            return redirect()->route('setup.checks');
        }

        if ($this->installService->systemAdminExists()) {
            session(['setup_admin_done' => true]);

            return redirect()->route('setup.platform');
        }

        return view('setup.admin', ['step' => 3]);
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        if (! session('setup_checks_passed')) {
            return redirect()->route('setup.checks');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $this->installService->createSystemAdmin($data);
        Auth::login($user);
        session(['setup_admin_done' => true, 'setup_admin_id' => $user->id]);

        return redirect()->route('setup.platform')->with('success', 'Administrator account created.');
    }

    public function showPlatform(Request $request): View|RedirectResponse
    {
        if (! session('setup_checks_passed') || ! session('setup_admin_done')) {
            return redirect()->route('setup.checks');
        }

        $defaults = $this->platformDefaults();
        $savedUrl = (string) ($defaults['public_site_url'] ?? '');
        $installUrl = InstallationUrlBuilder::parse(
            $savedUrl,
            $request->getHost(),
            $request->isSecure()
        );

        return view('setup.platform', [
            'defaults' => $defaults,
            'installUrl' => $installUrl,
            'domainOptions' => InstallationUrlBuilder::domainOptions($savedUrl, $request),
            'serverConfig' => $this->serverConfig($defaults),
            'envRecommendations' => $this->envRecommendations($defaults),
            'step' => 4,
        ]);
    }

    public function storePlatform(Request $request): RedirectResponse
    {
        if (! session('setup_checks_passed') || ! session('setup_admin_done')) {
            return redirect()->route('setup.checks');
        }

        $data = $request->validate([
            'org_name' => ['required', 'string', 'max:120'],
            'support_email' => ['required', 'email', 'max:255'],
            'install_protocol' => ['required', Rule::in(['http', 'https'])],
            'install_domain' => ['required', 'string', 'max:253'],
            'install_directory' => ['nullable', 'string', 'max:120'],
            'legal_privacy_url' => ['required', 'url', 'max:255'],
            'legal_terms_url' => ['required', 'url', 'max:255'],
            'legal_cookies_url' => ['required', 'url', 'max:255'],
            'enable_dialogue' => ['nullable', 'boolean'],
            'require_national_id' => ['nullable', 'boolean'],
        ]);

        try {
            $publicSiteUrl = InstallationUrlBuilder::build(
                $data['install_protocol'],
                $data['install_domain'],
                $data['install_directory'] ?? ''
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['install_directory' => $e->getMessage()]);
        }

        SiteSetting::set('org_name', $data['org_name']);
        SiteSetting::set('support_email', $data['support_email']);
        SiteSetting::set('public_site_url', $publicSiteUrl);
        SiteSetting::set('legal_privacy_url', $data['legal_privacy_url']);
        SiteSetting::set('legal_terms_url', $data['legal_terms_url']);
        SiteSetting::set('legal_cookies_url', $data['legal_cookies_url']);
        SiteSetting::set('enable_dialogue', $request->boolean('enable_dialogue', true));
        SiteSetting::set('require_national_id', $request->boolean('require_national_id', true));

        session(['setup_platform_done' => true]);

        return redirect()->route('setup.seed');
    }

    public function showSeed(): View|RedirectResponse
    {
        if (! session('setup_platform_done')) {
            return redirect()->route('setup.platform');
        }

        return view('setup.seed', ['step' => 5]);
    }

    public function runSeed(Request $request): RedirectResponse
    {
        if (! session('setup_platform_done')) {
            return redirect()->route('setup.platform');
        }

        $request->validate([
            'seed_mobile_test_user' => ['nullable', 'boolean'],
        ]);

        try {
            $this->installService->seedPlatformContent($request->boolean('seed_mobile_test_user'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Seeding failed: '.$e->getMessage());
        }

        session(['setup_seed_done' => true]);

        return redirect()->route('setup.finish');
    }

    public function finish(): View|RedirectResponse
    {
        if (! session('setup_seed_done')) {
            return redirect()->route('setup.seed');
        }

        return view('setup.finish', [
            'step' => 6,
            'defaults' => $this->platformDefaults(),
            'serverConfig' => $this->serverConfig(),
            'envRecommendations' => $this->envRecommendations(),
            'productionChecklist' => $this->productionChecklist->items($this->platformDefaults()),
        ]);
    }

    public function complete(): RedirectResponse
    {
        if (! session('setup_seed_done')) {
            return redirect()->route('setup.seed');
        }

        SiteSetting::set('installed_at', now()->toIso8601String());

        $adminId = session('setup_admin_id');
        if ($adminId && ($user = Auth::getProvider()->retrieveById($adminId))) {
            Auth::login($user);
        }

        session()->forget([
            'setup_checks_passed',
            'setup_admin_done',
            'setup_admin_id',
            'setup_platform_done',
            'setup_seed_done',
        ]);

        return redirect()->route('dashboard')->with('success', 'Installation complete. Welcome to the admin dashboard.');
    }

    /**
     * @return array<string, mixed>
     */
    private function platformDefaults(): array
    {
        return [
            'org_name' => (string) SiteSetting::get('org_name', 'ZANUPF'),
            'support_email' => (string) SiteSetting::get('support_email', 'support@zanupf.org.zw'),
            'public_site_url' => (string) SiteSetting::get('public_site_url', ''),
            'legal_privacy_url' => (string) SiteSetting::get('legal_privacy_url', url('/privacy-policy')),
            'legal_terms_url' => (string) SiteSetting::get('legal_terms_url', url('/terms-of-use')),
            'legal_cookies_url' => (string) SiteSetting::get('legal_cookies_url', url('/cookies')),
            'enable_dialogue' => (bool) SiteSetting::get('enable_dialogue', true),
            'require_national_id' => (bool) SiteSetting::get('require_national_id', true),
        ];
    }

    /**
     * Recommended .env values for the hosting team (wizard does not write .env).
     *
     * @param  array<string, mixed>|null  $defaults
     * @return array{APP_NAME: string, APP_URL: string, APP_ENV: string, APP_DEBUG: string}
     */
    private function envRecommendations(?array $defaults = null): array
    {
        $defaults ??= $this->platformDefaults();
        $appUrl = trim((string) ($defaults['public_site_url'] ?? ''));

        return [
            'APP_NAME' => (string) ($defaults['org_name'] ?? 'ZANUPF'),
            'APP_URL' => $appUrl !== '' ? $appUrl : 'https://www.zanupf.org.zw',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $defaults
     * @return array<string, mixed>
     */
    private function serverConfig(?array $defaults = null): array
    {
        $recommended = $this->envRecommendations($defaults);

        return [
            'current' => [
                'APP_NAME' => (string) config('app.name', ''),
                'APP_URL' => (string) config('app.url', ''),
                'APP_ENV' => (string) config('app.env', ''),
                'APP_DEBUG' => (bool) config('app.debug', false),
            ],
            'recommended' => [
                'APP_NAME' => $recommended['APP_NAME'],
                'APP_URL' => $recommended['APP_URL'],
                'APP_ENV' => $recommended['APP_ENV'],
                'APP_DEBUG' => $recommended['APP_DEBUG'] === 'true',
            ],
        ];
    }
}
