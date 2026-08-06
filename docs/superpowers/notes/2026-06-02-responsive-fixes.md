# Responsive QA + Dropdown Audit — 2026-06-02 (Phase 2 M1)

## Dropdowns fixed (added `aria-expanded`, `aria-haspopup`, `role="menu"`, `@keydown.escape.window`, `bg-white`)

| File | Dropdown |
|---|---|
| `resources/views/components/admin/topbar.blade.php` | User avatar menu |
| `resources/views/components/site/header.blade.php` | Mobile drawer trigger + dialog |
| `resources/views/preview/admin/pages/edit.blade.php` | "+ Add section" menu |
| `resources/views/components/member/header.blade.php` | (already wired correctly when created) |

## Tables wrapped in `overflow-x-auto` (fix mobile horizontal scroll)

All 10 admin CRUD index tables:
- `admin/users/index`
- `admin/pages/index`
- `admin/blog/posts/index`
- `admin/blog/categories/index`
- `admin/sermons/index`
- `admin/sermons/series/index`
- `admin/sermons/speakers/index`
- `admin/events/index`
- `admin/ministries/index`
- `admin/messages/index`

## Member preview built (M1-T01..T04)

- 3 layout components (`components/member/{layout,header,footer}.blade.php`)
- 13 preview routes registered under `preview.member.*`
- 6 root pages + 5 nested pages (prayer + care) + feed page = 13 view files

## Notes for sign-off review

- All 13 member preview routes return 200 (`/preview/member/`, `/profile`, `/register`, `/verify`, `/login`, `/donate`, `/prayer-requests`, `/prayer-requests/create`, `/prayer-requests/sample`, `/care`, `/care/create`, `/care/thanks`, `/feed`)
- Dropdown attributes verified by grep; each `x-data="{open` block now has Escape + ARIA support
- `npm run build` rebuilt CSS+JS (CKEditor still in the chunk-size warning, expected)
