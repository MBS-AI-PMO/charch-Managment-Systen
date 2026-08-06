<x-admin.layout title="Site settings">
    <div x-data="{tab:'branding'}" class="space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-serif">Site settings</h1>
                <p class="text-sm text-ink-muted mt-1">Brand, contact, social, footer, SEO and mail configuration.</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="btn-ghost text-sm">Revert</button>
                <button class="btn-primary text-sm">Save all changes</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Vertical tabs --}}
            <aside class="lg:col-span-3">
                <nav class="card p-2 text-sm space-y-0.5">
                    @foreach([
                        ['key'=>'branding','label'=>'Branding'],
                        ['key'=>'contact','label'=>'Contact'],
                        ['key'=>'social','label'=>'Social'],
                        ['key'=>'footer','label'=>'Footer'],
                        ['key'=>'seo','label'=>'SEO'],
                        ['key'=>'mail','label'=>'Mail'],
                    ] as $t)
                        <button @click="tab='{{ $t['key'] }}'" :class="tab==='{{ $t['key'] }}' ? 'bg-brand-primary/10 text-brand-primary font-medium' : 'text-ink hover:bg-surface'" class="w-full text-left px-3 py-2 rounded-md transition">{{ $t['label'] }}</button>
                    @endforeach
                </nav>
            </aside>

            <div class="lg:col-span-9 space-y-6">
                {{-- Branding --}}
                <div x-show="tab==='branding'" class="card p-5 space-y-5">
                    <h2 class="text-base font-serif">Branding</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Church name</label>
                            <input type="text" class="input" value="Grace Community Church">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Tagline</label>
                            <input type="text" class="input" value="A place to belong">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Logo</label>
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-16 rounded-lg bg-brand-primary text-white font-serif text-2xl flex items-center justify-center">G</div>
                                <button class="btn-ghost text-xs">Upload</button>
                                <button class="text-xs text-ink-muted hover:text-brand-primary">Remove</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Favicon</label>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-md bg-brand-primary text-white text-xs flex items-center justify-center">G</div>
                                <button class="btn-ghost text-xs">Upload</button>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Primary color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" value="#7A1F2B" class="w-12 h-10 rounded border border-[rgb(var(--border))] cursor-pointer">
                                <input type="text" class="input flex-1 font-mono text-sm" value="#7A1F2B">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Secondary color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" value="#C9A961" class="w-12 h-10 rounded border border-[rgb(var(--border))] cursor-pointer">
                                <input type="text" class="input flex-1 font-mono text-sm" value="#C9A961">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact --}}
                <div x-show="tab==='contact'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Contact</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Address</label>
                        <input type="text" class="input" value="123 Faith St, Springfield, USA">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Phone</label>
                            <input type="tel" class="input" value="(555) 123-4567">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Email</label>
                            <input type="email" class="input" value="hello@church.local">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Service times</label>
                        <textarea rows="3" class="input">Sunday 9:00 AM & 11:00 AM
Wednesday 7:00 PM</textarea>
                    </div>
                </div>

                {{-- Social --}}
                <div x-show="tab==='social'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Social links</h2>
                    @foreach([['Facebook','facebook.com/grace'], ['Instagram','instagram.com/grace'], ['YouTube','youtube.com/@grace'], ['X (Twitter)','x.com/grace']] as $s)
                        <div>
                            <label class="block text-sm font-medium mb-1.5">{{ $s[0] }}</label>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-ink-muted">https://</span>
                                <input type="text" class="input flex-1" value="{{ $s[1] }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div x-show="tab==='footer'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Footer</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">About blurb</label>
                        <textarea rows="3" class="input">A welcoming community for everyone.</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Copyright text</label>
                        <input type="text" class="input" value="© Grace Community Church. All rights reserved.">
                    </div>
                </div>

                {{-- SEO --}}
                <div x-show="tab==='seo'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">SEO</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Default title suffix</label>
                        <input type="text" class="input" value=" | Grace Community Church">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Default meta description</label>
                        <textarea rows="3" class="input">Welcome to Grace Community Church.</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Analytics / pixel scripts (raw HTML)</label>
                        <textarea rows="5" class="input font-mono text-xs" placeholder="&lt;!-- Google Analytics, Meta Pixel, etc. --&gt;"></textarea>
                        <p class="text-xs text-ink-muted mt-1">Injected just before <code class="text-xs">&lt;/head&gt;</code> on every page.</p>
                    </div>
                </div>

                {{-- Mail --}}
                <div x-show="tab==='mail'" x-cloak class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Outgoing mail</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Driver</label>
                            <select class="input"><option>SMTP</option><option>Mailgun</option><option>Postmark</option><option>SES</option></select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">From address</label>
                            <input type="email" class="input" value="noreply@grace.local">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">SMTP host</label>
                            <input type="text" class="input" value="smtp.mailtrap.io">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Port</label>
                            <input type="text" class="input" value="2525">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Encryption</label>
                            <select class="input"><option>tls</option><option>ssl</option><option>none</option></select>
                        </div>
                    </div>
                    <div class="pt-2">
                        <button class="btn-ghost text-xs">Send test email</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>
