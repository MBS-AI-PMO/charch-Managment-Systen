# Organizer Attendance

Short reference for Event Organizers (and Site Admins) running attendance for an event.

## Find the event

1. Sign in to `/admin` and open **Events** in the sidebar (`/admin/events`).
2. The index lists upcoming events first. Use the search field for older events.
3. Click the event title to open its show page.

## Open the Attendance tab

Each event show page has tabs along the top: **Overview**, **RSVPs**, **Attendance**. The Attendance tab is the work surface for the day-of.

It contains three areas:

- **Check-in panel** with the 4-digit code and an "Open / Close check-in" toggle.
- **Roster grid** of everyone who RSVP'd, with a checkbox per row.
- **Walk-ins panel** for adding people who showed up without an account.

## Open check-in (generates the code)

Click **Open check-in**. The system stamps `attendance_open = true` and generates a random 4-digit `checkin_code` if one isn't already set. The panel displays the code in large type. Tap **Close check-in** when the event ends; the code stays on the record for the audit trail.

While check-in is open and the event is within its window (-4h to +12h from start), members see a "Check in" item in their member header and can self-check-in at `/member/check-in`.

## Display the code at the door

Print or project the code, or write it on a whiteboard. The code is intentionally short (4 digits) because attendees type it on phones. Don't share it before the window opens - the window check rejects early submissions.

## Mark walk-ins

In the walk-ins panel, type a name and (optional) guest count, then click **Add walk-in**. The row appears in the roster immediately with method `organizer` and `recorded_by` set to you. No `user_id` is attached, so the walk-in shows by name only and won't appear in that person's member history.

To mark an RSVP'd member who didn't self-check-in, toggle their checkbox in the roster. Toggling off removes the attendance record.

## Export CSV

Click **Export CSV** at the top of the Attendance tab. The download is named `attendance-{event-slug}-{date}.csv` and includes name, email (if linked), guest count, checked-in-at, method, and recorder. Use it for printed sign-in sheets, follow-up emails, or accounting reconciliation.
