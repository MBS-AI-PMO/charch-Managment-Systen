@php
$brand = function_exists('settings') ? settings('brand.name', 'Assemblies of God') : 'Assemblies of God';
$tagline = function_exists('settings') ? settings('brand.tagline', 'Rawalpindi') : 'Rawalpindi';
@endphp
<footer class="bg-ink text-white">
    <div class="max-w-container mx-auto px-4 py-8 md:py-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">
        <div>
            <h3 class="font-serif text-lg mb-0.5">{{ $brand }}</h3>
            @if($tagline)
                <p class="text-white/60 text-xs mb-2">{{ $tagline }}</p>
            @endif
            <p class="text-white/70">A welcoming community for everyone.</p>
        </div>
        <div>
            <h4 class="font-semibold mb-2">Quick links</h4>
            <ul class="space-y-1.5 text-white/70">
                <li><a href="{{ route('preview.member.dashboard') }}" class="hover:text-white">Dashboard</a></li>
                <li><a href="{{ route('preview.member.prayer') }}" class="hover:text-white">Prayer</a></li>
                <li><a href="{{ route('preview.member.donate') }}" class="hover:text-white">Donate</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold mb-2">Support</h4>
            <p class="text-white/70">Need help? Reach out via Knock for Help or email us.</p>
        </div>
    </div>
    <div class="border-t border-white/10 text-center text-white/40 text-xs py-4">&copy; {{ date('Y') }} {{ $brand }}{{ $tagline ? ', '.$tagline : '' }}</div>
</footer>
