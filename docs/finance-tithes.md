# Finance: Tithes

Short reference for Site Admins recording tithes and managing funds.

## Where to find it

`/admin/tithes` (sidebar: **Finance > Tithes**). Only roles with the `manage-tithes` permission see the link; by default that is Site Admin.

The page has two areas:

- **Tithes index** - searchable, filterable list of recorded gifts with running totals.
- **Funds tab** - manage the destinations a gift can be assigned to.

## Record a gift

Click **Record gift**. The form has:

- **Giver** - typeahead against existing members by name or email. Picking a member links `user_id`. If the giver isn't a member, leave the typeahead empty and type into the free-text **Giver name** field below. Both fields can be blank for an anonymous gift.
- **Fund** - dropdown of active funds.
- **Amount** - entered in the displayed currency; stored internally as cents.
- **Date received**, **Method** (cash, bank transfer, cheque, other), optional **Reference** (slip number, bank ref) and **Note**.

Saving stamps `recorded_by` with your user id and writes an activity-log entry. The record appears at the top of the index immediately.

## Funds: deactivate vs delete

Open **Funds** from the Tithes page header.

- **Deactivate** (toggle `is_active = false`) hides the fund from the record-gift dropdown but preserves all historical gifts attached to it. Use this for funds that have ended (e.g. a closed building campaign).
- **Delete** is only allowed for funds with zero tithes attached. The button is disabled otherwise. Once a fund has been used, deactivate it instead - deleting would orphan history.

Default funds (General Offering, Missions Fund, Building Fund) are seeded on install and cannot be deleted until their gifts are reassigned.

## Donate page is still bank-transfer only

`/donate` on the public site has not changed - it shows bank transfer instructions and a "we'll record it" note. There is no payment gateway in Phase 3. When a gift lands in the church bank account (or the offering plate), an admin records it manually through this workflow. The Tithes index is the single source of truth for finance.

## Export CSV

The Tithes index has an **Export CSV** button. It respects the current filters (date range, fund, method, member). The file is named `tithes-{from}-{to}.csv` and includes giver, fund, amount, date, method, reference, and recorder - the columns finance volunteers usually want for spreadsheets or accounting tools.
