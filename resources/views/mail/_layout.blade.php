<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>{{ $brandName }}</title></head>
<body style="font-family: Inter, Arial, sans-serif; background:#f6f7f9; margin:0; padding:24px;">
    <div style="max-width:600px; margin:0 auto; background:white; border-radius:8px; overflow:hidden;">
        <div style="padding:20px; background:{{ $brandColor }}; color:white;">
            <h2 style="margin:0; font-family: Georgia, serif;">{{ $brandName }}</h2>
        </div>
        <div style="padding:24px; line-height:1.6; color:#222;">
            {{ $slot ?? '' }}
            @yield('body')
        </div>
        <div style="padding:16px 24px; background:#f6f7f9; color:#666; font-size:12px;">
            <p>{{ $address }}</p>
            <p><a href="{{ url('/member/profile').'#email-prefs' }}" style="color:{{ $brandColor }};">Manage email preferences</a></p>
        </div>
    </div>
</body>
</html>
