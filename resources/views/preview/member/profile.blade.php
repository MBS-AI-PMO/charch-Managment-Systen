<x-member.layout title="My profile">
    <div class="max-w-3xl mx-auto px-4 py-10 space-y-8">
        <div>
            <h1 class="font-serif text-3xl md:text-4xl">My profile</h1>
            <p class="text-ink-muted mt-2 text-sm">Keep your contact info and password up to date.</p>
        </div>

        <div class="card p-6 md:p-8">
            <h2 class="font-serif text-xl mb-1">Account details</h2>
            <p class="text-sm text-ink-muted mb-6">This information helps us stay in touch with you.</p>

            <form class="space-y-5">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center font-serif text-2xl">SM</div>
                    <div>
                        <button type="button" class="btn-ghost text-sm">Change photo</button>
                        <p class="text-xs text-ink-muted mt-1">PNG or JPG, up to 2MB.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Full name</label>
                        <input class="input w-full" type="text" value="Sarah Member" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input class="input w-full" type="email" value="sarah@example.com" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input class="input w-full" type="tel" value="+44 7700 900123" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Short bio</label>
                    <textarea class="input w-full" rows="3" maxlength="280" placeholder="Tell the community a little about yourself...">Mum of two. Coffee lover. Serving in welcome team.</textarea>
                    <p class="text-xs text-ink-muted mt-1">Up to 280 characters.</p>
                </div>

                <div class="flex flex-wrap gap-2 pt-2">
                    <button type="button" class="btn-primary text-sm">Save changes</button>
                    <button type="button" class="btn-ghost text-sm">Cancel</button>
                </div>
            </form>
        </div>

        <div class="card p-6 md:p-8">
            <h2 class="font-serif text-xl mb-1">Change password</h2>
            <p class="text-sm text-ink-muted mb-6">Use a strong, unique password.</p>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Current password</label>
                    <input class="input w-full" type="password" autocomplete="current-password" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">New password</label>
                        <input class="input w-full" type="password" autocomplete="new-password" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Confirm password</label>
                        <input class="input w-full" type="password" autocomplete="new-password" />
                    </div>
                </div>
                <div class="pt-2">
                    <button type="button" class="btn-primary text-sm">Update password</button>
                </div>
            </form>
        </div>
    </div>
</x-member.layout>
