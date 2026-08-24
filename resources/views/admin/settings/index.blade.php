<x-admin.layout title="Site settings">
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" x-data="{tab:'branding'}" class="space-y-6">
        @csrf

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-serif">Site settings</h1>
                <p class="text-sm text-ink-muted mt-1">Brand, contact details, social links, footer, SEO, and mail config.</p>
            </div>
            <button type="submit" class="btn-primary text-sm">Save settings</button>
        </div>

        @if ($errors->any())
            <div class="px-4 py-3 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-sm">
                <p class="font-medium mb-1">Please fix these errors:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            {{-- Vertical tab nav --}}
            <aside class="card p-3 h-fit lg:sticky lg:top-24">
                <nav class="space-y-1">
                    @foreach(['branding' => 'Branding', 'home' => 'Home page', 'contact' => 'Contact', 'social' => 'Social', 'footer' => 'Footer', 'seo' => 'SEO', 'mail' => 'Mail', 'reminders' => 'Reminders'] as $key => $label)
                        <button type="button" @click="tab='{{ $key }}'" :class="tab==='{{ $key }}' ? 'bg-brand-primary/10 text-brand-primary' : 'text-ink-muted hover:bg-surface'" class="w-full text-left px-3 py-2 rounded text-sm transition">{{ $label }}</button>
                    @endforeach
                </nav>
            </aside>

            <div class="lg:col-span-3 space-y-5">
                {{-- Branding --}}
                <div x-show="tab==='branding'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Branding</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Church name</label>
                        <input type="text" name="brand[name]" class="input" value="{{ old('brand.name', settings('brand.name')) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Tagline</label>
                        <input type="text" name="brand[tagline]" class="input" value="{{ old('brand.tagline', settings('brand.tagline')) }}">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Primary colour</label>
                            <input type="text" name="brand[color][primary]" class="input font-mono text-sm" value="{{ old('brand.color.primary', settings('brand.color.primary')) }}" placeholder="#1d3a3a">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Secondary colour</label>
                            <input type="text" name="brand[color][secondary]" class="input font-mono text-sm" value="{{ old('brand.color.secondary', settings('brand.color.secondary')) }}" placeholder="#d8b76a">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Logo</label>
                        <img id="brand_logo_preview"
                             src="{{ ($logo = settings('brand.logo')) ? site_storage_url($logo) : '' }}"
                             alt=""
                             class="h-14 mb-2 rounded border border-[rgb(var(--border))] bg-white object-contain {{ $logo ? '' : 'hidden' }}">
                        <input type="hidden" id="brand_logo_path" name="brand[logo_path]" value="{{ $logo }}">
                        <div class="flex gap-2 items-start">
                            <input type="file" name="brand[logo]" accept="image/*" class="text-xs flex-1"
                                   onchange="previewBrandFile(this, 'brand_logo_preview', 'brand_logo_path')">
                            <button type="button"
                                    onclick="openMediaPicker((url) => { const img=document.getElementById('brand_logo_preview'); img.src=url; img.classList.remove('hidden'); document.getElementById('brand_logo_path').value=url.replace(/^.*\/storage\//,''); })"
                                    class="btn-ghost text-xs whitespace-nowrap">Library</button>
                        </div>
                        @error('brand.logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="text-xs text-ink-muted mt-1">PNG, JPG, WEBP or SVG. Max 4MB.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Favicon</label>
                        <img id="brand_favicon_preview"
                             src="{{ ($fav = settings('brand.favicon')) ? site_storage_url($fav) : '' }}"
                             alt=""
                             class="h-10 mb-2 rounded border border-[rgb(var(--border))] bg-white object-contain {{ $fav ? '' : 'hidden' }}">
                        <input type="hidden" id="brand_favicon_path" name="brand[favicon_path]" value="{{ $fav }}">
                        <div class="flex gap-2 items-start">
                            <input type="file" name="brand[favicon]" accept="image/*,.ico" class="text-xs flex-1"
                                   onchange="previewBrandFile(this, 'brand_favicon_preview', 'brand_favicon_path')">
                            <button type="button"
                                    onclick="openMediaPicker((url) => { const img=document.getElementById('brand_favicon_preview'); img.src=url; img.classList.remove('hidden'); document.getElementById('brand_favicon_path').value=url.replace(/^.*\/storage\//,''); })"
                                    class="btn-ghost text-xs whitespace-nowrap">Library</button>
                        </div>
                        @error('brand.favicon')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="text-xs text-ink-muted mt-1">PNG, JPG, ICO or WEBP. Max 2MB. Saved favicon shows on the whole site.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-[rgb(var(--border))]">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Donate button label</label>
                            <input type="text" name="donate[button_label]" class="input" value="{{ old('donate.button_label', settings('donate.button_label', 'Donate')) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Header visit button label</label>
                            <input type="text" name="header[visit_cta_label]" class="input" value="{{ old('header.visit_cta_label', settings('header.visit_cta_label', 'Plan your visit')) }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Header visit button URL</label>
                        <input type="text" name="header[visit_cta_url]" class="input font-mono text-sm" value="{{ old('header.visit_cta_url', settings('header.visit_cta_url', '/contact')) }}" placeholder="/contact">
                    </div>
                </div>

                {{-- Home page --}}
                <div x-show="tab==='home'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Home page welcome section</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Eyebrow</label>
                        <input type="text" name="home[welcome][eyebrow]" class="input" value="{{ old('home.welcome.eyebrow', settings('home.welcome.eyebrow')) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Heading</label>
                        <input type="text" name="home[welcome][heading]" class="input" value="{{ old('home.welcome.heading', settings('home.welcome.heading')) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Intro line</label>
                        <textarea name="home[welcome][lede]" rows="2" class="input">{{ old('home.welcome.lede', settings('home.welcome.lede')) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Quote overlay</label>
                        <input type="text" name="home[welcome][quote]" class="input" value="{{ old('home.welcome.quote', settings('home.welcome.quote')) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Welcome photo path</label>
                        <input type="text" name="home[welcome_image]" class="input font-mono text-sm" value="{{ old('home.welcome_image', settings('home.welcome_image')) }}" placeholder="uploads/photo.jpg">
                        <div class="flex gap-2 mt-2">
                            <button type="button"
                                    onclick="openMediaPicker((url) => { document.querySelector('[name=\'home[welcome_image]\']').value = url.replace(/^.*\/storage\//,''); })"
                                    class="btn-ghost text-xs">Pick from library</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">About page story image path</label>
                        <input type="text" name="about[story_image]" class="input font-mono text-sm" value="{{ old('about.story_image', settings('about.story_image')) }}" placeholder="uploads/pages/story.jpg">
                        <div class="flex gap-2 mt-2">
                            <button type="button"
                                    onclick="openMediaPicker((url) => { document.querySelector('[name=\'about[story_image]\']').value = url.replace(/^.*\/storage\//,''); })"
                                    class="btn-ghost text-xs">Pick from library</button>
                        </div>
                    </div>
                </div>

                {{-- Contact --}}
                <div x-show="tab==='contact'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Contact</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Address</label>
                        <textarea name="contact[address]" rows="2" class="input">{{ old('contact.address', settings('contact.address')) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Phone</label>
                            <input type="text" name="contact[phone]" class="input" value="{{ old('contact.phone', settings('contact.phone')) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Email</label>
                            <input type="email" name="contact[email]" class="input" value="{{ old('contact.email', settings('contact.email')) }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Service times</label>
                        <textarea name="contact[service_times]" rows="3" class="input" placeholder="Sundays 9:00 & 11:00 AM">{{ old('contact.service_times', settings('contact.service_times')) }}</textarea>
                    </div>
                </div>

                {{-- Social --}}
                <div x-show="tab==='social'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Social</h2>
                    @foreach(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'x' => 'X (Twitter)'] as $key => $label)
                        <div>
                            <label class="block text-sm font-medium mb-1.5">{{ $label }}</label>
                            <input type="url" name="social[{{ $key }}]" class="input" value="{{ old("social.$key", settings("social.$key")) }}" placeholder="https://…">
                        </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div x-show="tab==='footer'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Footer</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">About blurb</label>
                        <textarea name="footer[about]" rows="3" class="input">{{ old('footer.about', settings('footer.about')) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Copyright line</label>
                        <input type="text" name="footer[copyright]" class="input" value="{{ old('footer.copyright', settings('footer.copyright')) }}">
                    </div>
                </div>

                {{-- SEO --}}
                <div x-show="tab==='seo'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">SEO</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Default title suffix</label>
                        <input type="text" name="seo[default_title_suffix]" class="input" value="{{ old('seo.default_title_suffix', settings('seo.default_title_suffix')) }}" placeholder="— Assemblies of God">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Default meta description</label>
                        <textarea name="seo[default_description]" rows="3" class="input">{{ old('seo.default_description', settings('seo.default_description')) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Analytics script (raw)</label>
                        <textarea name="seo[analytics_script]" rows="5" class="input font-mono text-xs">{{ old('seo.analytics_script', settings('seo.analytics_script')) }}</textarea>
                        <p class="text-xs text-ink-muted mt-1">Pasted verbatim into the public <code class="text-xs">&lt;head&gt;</code>.</p>
                    </div>
                </div>

                {{-- Mail --}}
                <div x-show="tab==='mail'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Mail</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">From address</label>
                        <input type="email" name="mail[from_address]" class="input" value="{{ old('mail.from_address', settings('mail.from_address')) }}">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1.5">SMTP host</label>
                            <input type="text" name="mail[host]" class="input" value="{{ old('mail.host', settings('mail.host')) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Port</label>
                            <input type="number" name="mail[port]" class="input" value="{{ old('mail.port', settings('mail.port')) }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Encryption</label>
                        <select name="mail[encryption]" class="input w-44">
                            @foreach(['' => 'None', 'tls' => 'TLS', 'ssl' => 'SSL'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('mail.encryption', settings('mail.encryption')) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-xs text-ink-muted">SMTP credentials still live in <code class="text-xs">.env</code> for security.</p>
                </div>

                {{-- Reminders --}}
                <div x-show="tab==='reminders'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Reminders</h2>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="hidden" name="reminders[event_24h_enabled]" value="0">
                        <input type="checkbox" name="reminders[event_24h_enabled]" value="1" @checked(settings('reminders.event_24h_enabled', true))>
                        Event 24h reminders
                    </label>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="hidden" name="reminders[weekly_digest_enabled]" value="0">
                        <input type="checkbox" name="reminders[weekly_digest_enabled]" value="1" @checked(settings('reminders.weekly_digest_enabled', true))>
                        Weekly digest
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Weekly digest day</label>
                            <select name="reminders[weekly_digest_day]" class="input">
                                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $d)
                                    <option value="{{ $d }}" @selected(settings('reminders.weekly_digest_day') === $d)>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Weekly digest hour</label>
                            <select name="reminders[weekly_digest_hour]" class="input">
                                @for($h = 0; $h < 24; $h++)
                                    <option value="{{ $h }}" @selected((int) settings('reminders.weekly_digest_hour') === $h)>{{ sprintf('%02d:00', $h) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="hidden" name="reminders[admin_daily_digest_enabled]" value="0">
                        <input type="checkbox" name="reminders[admin_daily_digest_enabled]" value="1" @checked(settings('reminders.admin_daily_digest_enabled', false))>
                        Admin daily digest
                    </label>
                </div>
            </div>
        </div>
    </form>
    <script>
        function previewBrandFile(input, previewId, pathInputId) {
            const file = input.files && input.files[0];
            if (!file) return;
            const img = document.getElementById(previewId);
            const pathInput = document.getElementById(pathInputId);
            if (pathInput) pathInput.value = '';
            if (!img) return;
            const url = URL.createObjectURL(file);
            img.src = url;
            img.classList.remove('hidden');
        }
    </script>
</x-admin.layout>
