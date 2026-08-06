# Member Onboarding

Short reference for the member lifecycle, capabilities, and admin controls introduced in Phase 2.

## Member lifecycle

1. **Visitor** lands on the public site and clicks **Register** (header CTA → `/register`).
2. Visitor submits the registration form (name, email, password).
3. App creates a `users` record, assigns the **Member** role, and sends a **verification email** (signed URL, 60-minute TTL).
4. Member clicks the link → `verification.verify` route validates the signature, stamps `email_verified_at`, and redirects to **`/member`** (dashboard).
5. Unverified users are bounced from `/member/*` to the "please verify" notice with a resend button.

## What members can do

| Area | Path | Notes |
| --- | --- | --- |
| Dashboard | `/member` | Welcome card, recent activity |
| Profile | `/member/profile` | Edit name, phone, address; change password |
| Prayer Requests | `/member/prayer-requests` | Submit own (public / private / anonymous), browse community board, toggle "I'm praying" |
| Knock for Help | `/member/care` | Pastoral care request → emails staff inbox + appears in admin triage |
| Community Feed | `/member/feed` | Read admin posts, react (heart / amen / pray) |
| Give | `/donate` (public) | Bank-transfer instructions; no payment provider in Phase 2 |

## Admin manages members at

- **Admin → Members** (`/admin/users`) — list, filter, edit, deactivate, assign roles.
- Sub-admin roles (Prayer Organizer, Event Organizer) are assigned from the same screen.

## Account deactivation

To revoke member access without deleting the record:

- **Soft revoke (force re-verify):** set `email_verified_at = null` via Admin → Members → edit, or:
  ```powershell
  D:/XAMPP/php/php.exe artisan tinker --execute='\App\Models\User::where("email","x@y.com")->update(["email_verified_at"=>null]);'
  ```
- **Hard revoke (remove portal access):** remove the **Member** role via Spatie:
  ```powershell
  D:/XAMPP/php/php.exe artisan tinker --execute='\App\Models\User::where("email","x@y.com")->first()->removeRole("Member");'
  ```

## Pause registration site-wide

Flip the `member.registration_enabled` setting to `0`:

- **UI:** Admin → Settings → Member section → toggle **Registration enabled** off.
- **CLI:**
  ```powershell
  D:/XAMPP/php/php.exe artisan tinker --execute='\App\Models\Setting::set("member.registration_enabled","0");'
  ```

When disabled, `/register` returns a 403 with a "Registration is currently closed" page; existing members can still sign in.

## RSVP and check-in

Phase 3 added event RSVPs, attendance check-in, and reminder emails for members.

- **RSVP on the event page:** Open any event at `/events/{slug}`. Signed-in members see an RSVP card with a guest selector (0-5) and an optional note. Saving redirects back to the event with a "You're going" badge; the same badge appears next to that event on `/events`. RSVPs can be edited or cancelled from the same card.
- **Reminder email preferences:** Visit `/member/profile`. The Email Preferences card toggles the event 24-hour reminder and the weekly digest. Changes save immediately; admins cannot override member opt-out.
- **Self check-in at the door:** When an organizer opens attendance for an event, a "Check in" item appears in the member header during the event window. Visit `/member/check-in`, type the 4-digit code from the door sign, and submit. The thanks page confirms the event and time. Submitting the same code twice is a no-op.
- **Walk-ins handled by organizers:** Members without an account, or anyone who lost the code, can simply tell the organizer at the door. The organizer marks the walk-in from the admin attendance grid - no member action needed.
