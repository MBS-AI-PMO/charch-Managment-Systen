<?php

$adminNavLinks = [
    ['route' => 'admin.dashboard',            'label' => 'Dashboard',        'icon' => 'home',      'permission' => null],
    ['route' => 'admin.pages.index',          'label' => 'Pages',            'icon' => 'doc',       'permission' => 'manage-pages'],
    ['route' => 'admin.churches.index',       'label' => 'Church Branches',  'icon' => 'church',    'permission' => 'manage-pages'],
    ['route' => 'admin.hero-slides.index',    'label' => 'Hero Slides',      'icon' => 'slides',    'permission' => 'manage-pages'],
    ['route' => 'admin.blog.posts.index',     'label' => 'News',             'icon' => 'pen',       'permission' => 'manage-blog'],
    ['route' => 'admin.sermons.index',        'label' => 'Sermons',          'icon' => 'mic',       'permission' => 'manage-sermons'],
    ['route' => 'admin.events.index',         'label' => 'Events',           'icon' => 'calendar',  'permission' => 'manage-events'],
    ['route' => 'admin.ministries.index',     'label' => 'Ministries',       'icon' => 'users',     'permission' => 'manage-ministries'],
    ['route' => 'admin.media.index',          'label' => 'Media',            'icon' => 'image',     'permission' => 'manage-media'],
    ['route' => 'admin.gallery.index',        'label' => 'Gallery',          'icon' => 'gallery',   'permission' => 'manage-media'],
    ['route' => 'admin.menus.index',          'label' => 'Menus',            'icon' => 'list',      'permission' => 'manage-menus'],
    ['route' => 'admin.certificates.index',   'label' => 'Certificates',     'icon' => 'award',     'permission' => 'manage-certificates'],
    ['route' => 'admin.prayer-requests.index','label' => 'Prayer Requests',  'icon' => 'pray',      'permission' => 'manage-prayer-requests'],
    ['route' => 'admin.care.index',           'label' => 'Knock for Help',   'icon' => 'lifebuoy',  'permission' => 'manage-knock-help'],
    ['route' => 'admin.feed.index',           'label' => 'Community Feed',   'icon' => 'megaphone', 'permission' => 'manage-community-feed'],
    ['route' => 'admin.attendance.index',     'label' => 'Attendance',       'icon' => 'check',     'permission' => 'manage-events'],
    ['route' => 'admin.reminders.stub',       'label' => 'Reminders',        'icon' => 'bell',      'permission' => 'manage-events', 'badge' => 'Phase 3'],
    ['route' => 'admin.tithes.index',         'label' => 'Tithes',           'icon' => 'coins',     'permission' => 'manage-tithes'],
    ['route' => 'admin.tithes.funds.index',   'label' => 'Funds',            'icon' => 'wallet',    'permission' => 'manage-tithes'],
    ['route' => 'admin.reports.index',        'label' => 'Reports',          'icon' => 'chart',     'permission' => 'view-reports'],
    ['route' => 'admin.messages.index',       'label' => 'Messages',         'icon' => 'mail',      'permission' => 'manage-messages'],
    ['route' => 'admin.users.index',          'label' => 'Members',          'icon' => 'user',      'permission' => 'manage-users'],
    ['route' => 'admin.roles.index',          'label' => 'Roles',            'icon' => 'shield',    'permission' => 'manage-roles'],
    ['route' => 'admin.settings.index',       'label' => 'Settings',         'icon' => 'cog',       'permission' => 'manage-settings'],
];

$adminNavIcons = [
    'home'      => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/>',
    'doc'       => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/>',
    'church'    => '<path d="M12 3 4 9v12h16V9Z"/><path d="M9 21V12h6v9"/><path d="M12 3v4"/>',
    'slides'    => '<rect x="3" y="5" width="18" height="12" rx="2"/><path d="M7 9h10M7 13h6"/>',
    'pen'       => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/>',
    'mic'       => '<rect x="9" y="3" width="6" height="12" rx="3"/><path d="M5 11a7 7 0 0 0 14 0"/><path d="M12 18v3"/>',
    'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
    'users'     => '<circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.6-3.4 3.4-5.5 6.5-5.5s5.9 2.1 6.5 5.5"/><circle cx="17" cy="9" r="2.6"/><path d="M15.5 14.5c2.5.2 4.5 2 5 4.5"/>',
    'image'     => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5L5 21"/>',
    'gallery'   => '<rect x="3" y="5" width="13" height="11" rx="1.5"/><rect x="8" y="8" width="13" height="11" rx="1.5"/><circle cx="9.5" cy="10" r="1.2"/><path d="m16 14-2.2-2.2L11 15"/>',
    'list'      => '<path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1.2"/><circle cx="4" cy="12" r="1.2"/><circle cx="4" cy="18" r="1.2"/>',
    'award'     => '<circle cx="12" cy="8" r="5"/><path d="M8.5 12.5 7 21l5-2.5L17 21l-1.5-8.5"/><path d="M12 3v2"/>',
    'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    'user'      => '<circle cx="12" cy="8" r="3.5"/><path d="M4 21c.7-4 3.9-6.5 8-6.5s7.3 2.5 8 6.5"/>',
    'shield'    => '<path d="M12 3 4 6v6c0 4.5 3.4 8.4 8 9 4.6-.6 8-4.5 8-9V6Z"/><path d="m9 12 2.2 2.2L15 10.5"/>',
    'cog'       => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 0 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>',
    'pray'      => '<path d="M12 3v6M9 9h6M9 21l3-3 3 3M5 21l3-5 4 1M19 21l-3-5-4 1"/>',
    'lifebuoy'  => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><path d="M5.6 5.6l3.4 3.4M15 15l3.4 3.4M5.6 18.4L9 15M15 9l3.4-3.4"/>',
    'megaphone' => '<path d="M3 11v2a2 2 0 0 0 2 2h1l4 4V5L6 9H5a2 2 0 0 0-2 2zM18 8a4 4 0 0 1 0 8"/>',
    'check'     => '<path d="m5 12 5 5L20 7"/>',
    'bell'      => '<path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
    'coins'     => '<ellipse cx="9" cy="7" rx="6" ry="2.5"/><path d="M3 7v4c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V7"/><path d="M3 11v4c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-4"/><ellipse cx="16" cy="14" rx="5" ry="2"/><path d="M11 14v3c0 1.1 2.2 2 5 2s5-.9 5-2v-3"/>',
    'wallet'    => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18"/><circle cx="17" cy="15" r="1.2"/>',
    'chart'     => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/>',
];

$adminNavCurrent = request()->route()?->getName() ?? '';

$adminNavIsActive = function (string $route) use ($adminNavCurrent, $adminNavLinks) {
    $stem = preg_replace('/\.index$/', '', $route);
    $matches = $adminNavCurrent === $route
        || $adminNavCurrent === $stem
        || str_starts_with($adminNavCurrent, $stem.'.');

    if (! $matches) {
        return false;
    }

    foreach ($adminNavLinks as $other) {
        $otherRoute = $other['route'] ?? '';
        if ($otherRoute === $route) {
            continue;
        }

        $otherStem = preg_replace('/\.index$/', '', $otherRoute);
        if (! str_starts_with($otherStem, $stem.'.')) {
            continue;
        }

        if (
            $adminNavCurrent === $otherRoute
            || $adminNavCurrent === $otherStem
            || str_starts_with($adminNavCurrent, $otherStem.'.')
        ) {
            return false;
        }
    }

    return true;
};

$adminVisibleLinks = collect($adminNavLinks)->filter(
    fn ($l) => $l['permission'] === null || auth('admin')->user()?->can($l['permission'])
)->values();
