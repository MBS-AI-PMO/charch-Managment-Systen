<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SettingsRequest;
use App\Services\SettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(SettingsRequest $req, SettingsRepository $repo)
    {
        // Brand text/colour keys
        $textKeys = [
            'brand.name', 'brand.tagline', 'brand.color.primary', 'brand.color.secondary',
            'contact.address', 'contact.phone', 'contact.email', 'contact.service_times',
            'social.facebook', 'social.instagram', 'social.youtube', 'social.x',
            'footer.about', 'footer.copyright',
            'seo.default_title_suffix', 'seo.default_description', 'seo.analytics_script',
            'donate.button_label',
            'header.visit_cta_label', 'header.visit_cta_url',
            'home.welcome.eyebrow', 'home.welcome.heading', 'home.welcome.lede', 'home.welcome.quote', 'home.welcome_image',
            'about.story_image',
            'mail.from_address', 'mail.host', 'mail.port', 'mail.encryption',
            'reminders.event_24h_enabled', 'reminders.weekly_digest_enabled',
            'reminders.weekly_digest_day', 'reminders.weekly_digest_hour',
            'reminders.admin_daily_digest_enabled',
        ];

        foreach ($textKeys as $key) {
            $value = $this->dataGet($req, $key);
            if ($value !== null) {
                $repo->set($key, (string) $value);
            }
        }

        // File uploads (logo + favicon). Direct upload wins; otherwise a
        // media-library picker can supply a `brand[logo_path]` hidden input.
        $logoFile = $req->file('brand.logo');
        if ($logoFile instanceof UploadedFile && $logoFile->isValid()) {
            $path = $this->storeBrandUpload($logoFile, 'logo');
            $repo->set('brand.logo', $path);
        } elseif ($pickedLogo = data_get($req->all(), 'brand.logo_path')) {
            $repo->set('brand.logo', ltrim(str_replace('\\', '/', (string) $pickedLogo), '/'));
        }

        $faviconFile = $req->file('brand.favicon');
        if ($faviconFile instanceof UploadedFile && $faviconFile->isValid()) {
            $path = $this->storeBrandUpload($faviconFile, 'favicon');
            $repo->set('brand.favicon', $path);
        } elseif ($pickedFav = data_get($req->all(), 'brand.favicon_path')) {
            $repo->set('brand.favicon', ltrim(str_replace('\\', '/', (string) $pickedFav), '/'));
        }

        return back()->with('success', 'Settings saved. Logo and favicon updates should appear after a refresh.');
    }

    protected function storeBrandUpload(UploadedFile $file, string $basename): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'png';
        $filename = $basename.'-'.time().'.'.$ext;

        return $file->storeAs('branding', $filename, 'public');
    }

    protected function dataGet(Request $req, string $key)
    {
        // Use Laravel's dot-notation input accessor.
        return data_get($req->all(), $key);
    }
}
