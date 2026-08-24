@php
    $initials = collect(explode(' ', $user->name))
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->take(2)
        ->join('');
@endphp
<x-member.layout title="My profile">
    <div class="member-shell space-y-8">
        <div>
            <h1 class="font-serif text-3xl md:text-4xl">My profile</h1>
            <p class="text-ink-muted mt-2 text-sm">Keep your contact info and password up to date.</p>
        </div>

        @if ($errors->any())
            <div class="px-4 py-3 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-sm">
                <p class="font-medium mb-1">Please correct the following:</p>
                <ul class="list-disc pl-5 text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            <div class="xl:col-span-7 card p-6 md:p-8">
                <h2 class="font-serif text-xl mb-1">Account details</h2>
                <p class="text-sm text-ink-muted mb-6">This information helps us stay in touch with you.</p>

                <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div
                        class="flex items-center gap-4"
                        x-data="{
                            preview: @js($user->avatarUrl()),
                            removing: false,
                            pick(event) {
                                const file = event.target.files && event.target.files[0];
                                if (!file) return;
                                this.removing = false;
                                this.preview = URL.createObjectURL(file);
                            },
                            clear() {
                                this.preview = null;
                                this.removing = true;
                                if (this.$refs.avatarInput) this.$refs.avatarInput.value = '';
                            }
                        }"
                    >
                        <div class="w-20 h-20 rounded-full overflow-hidden shrink-0 bg-brand-primary/10 text-brand-primary flex items-center justify-center font-serif text-2xl border border-[rgb(var(--border))]">
                            <img x-show="preview" x-cloak :src="preview" alt="" class="w-full h-full object-cover">
                            <span x-show="!preview">{{ $initials ?: 'M' }}</span>
                        </div>
                        <div>
                            <input
                                id="avatar"
                                type="file"
                                name="avatar"
                                x-ref="avatarInput"
                                accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif"
                                class="hidden"
                                @change="pick"
                            >
                            <input type="hidden" name="remove_avatar" :value="removing ? '1' : '0'">
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="btn-ghost text-sm" @click="$refs.avatarInput.click()">
                                    Change photo
                                </button>
                                <button type="button" class="btn-ghost text-sm" x-show="preview" x-cloak @click="clear()">
                                    Remove
                                </button>
                            </div>
                            <p class="text-xs text-ink-muted mt-1">JPG, PNG or WebP. Max 5 MB. Then click Save changes.</p>
                            @error('avatar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium mb-1">Full name</label>
                            <input id="name" name="name" class="input w-full" type="text"
                                   value="{{ old('name', $user->name) }}" required />
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium mb-1">Email</label>
                            <input id="email" name="email" class="input w-full" type="email"
                                   value="{{ old('email', $user->email) }}" required />
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-1">Phone</label>
                            <input id="phone" name="phone" class="input w-full" type="tel"
                                   value="{{ old('phone', $user->phone) }}" />
                            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium mb-1">Short bio</label>
                        <textarea id="bio" name="bio" class="input w-full" rows="3" maxlength="280"
                                  placeholder="Tell the community a little about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        <p class="text-xs text-ink-muted mt-1">Up to 280 characters.</p>
                        @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn-primary text-sm">Save changes</button>
                        <a href="{{ route('member.dashboard') }}" class="btn-ghost text-sm">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="xl:col-span-5 space-y-6">
                <div class="card p-6 md:p-8">
                    <h2 class="font-serif text-xl mb-1">Change password</h2>
                    <p class="text-sm text-ink-muted mb-6">Use a strong, unique password.</p>
                    <form method="POST" action="{{ route('member.profile.password') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-sm font-medium mb-1">Current password</label>
                            <input id="current_password" name="current_password" class="input w-full" type="password"
                                   autocomplete="current-password" required />
                            @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium mb-1">New password</label>
                                <input id="password" name="password" class="input w-full" type="password"
                                       autocomplete="new-password" required />
                                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm password</label>
                                <input id="password_confirmation" name="password_confirmation" class="input w-full" type="password"
                                       autocomplete="new-password" required />
                            </div>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="btn-primary text-sm">Update password</button>
                        </div>
                    </form>
                </div>

                <div id="email-prefs" class="card p-6 md:p-8">
                    <h2 class="font-serif text-xl mb-1">Email preferences</h2>
                    <p class="text-sm text-ink-muted mb-6">Choose which church emails you'd like to receive.</p>
                    <form method="POST" action="{{ route('member.profile.update') }}" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_prefs_only" value="1">

                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="email_reminder_event_24h" value="1" @checked($user->email_reminder_event_24h)>
                            Event reminders (24h before)
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="email_weekly_digest" value="1" @checked($user->email_weekly_digest)>
                            Weekly digest
                        </label>

                        <div class="pt-2">
                            <button type="submit" class="btn-primary text-sm">Save preferences</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-member.layout>
