<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\ProfilePasswordRequest;
use App\Http\Requests\Member\ProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('member.profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileRequest $request)
    {
        // M8: email-preferences sub-form posts here with _prefs_only=1.
        if ($request->boolean('_prefs_only')) {
            $request->user()->update([
                'email_reminder_event_24h' => $request->boolean('email_reminder_event_24h'),
                'email_weekly_digest' => $request->boolean('email_weekly_digest'),
            ]);

            return back()->with('success', 'Email preferences updated successfully.');
        }

        $data = $request->validated();
        unset($data['avatar'], $data['remove_avatar']);
        $user = $request->user();

        if ($data['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            $user->replaceAvatar($request->file('avatar'));
        } elseif ($request->boolean('remove_avatar')) {
            $user->clearAvatar();
        }

        $user->fill($data)->save();

        if (is_null($user->email_verified_at)) {
            $user->sendEmailVerificationNotification();

            return redirect()
                ->route('verification.notice')
                ->with('success', 'Profile saved successfully. Please verify your new email.');
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(ProfilePasswordRequest $request)
    {
        $request->user()->update([
            'password' => $request->string('password'),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
