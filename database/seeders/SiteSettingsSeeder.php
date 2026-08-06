<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'brand.name', 'value' => 'Assemblies of God', 'type' => 'text', 'group' => 'brand'],
            ['key' => 'brand.tagline', 'value' => 'Rawalpindi', 'type' => 'text', 'group' => 'brand'],
            ['key' => 'brand.logo', 'value' => null, 'type' => 'image', 'group' => 'brand'],
            ['key' => 'brand.favicon', 'value' => null, 'type' => 'image', 'group' => 'brand'],
            ['key' => 'brand.color.primary', 'value' => '#7A1F2B', 'type' => 'color', 'group' => 'brand'],
            ['key' => 'brand.color.secondary', 'value' => '#C9A961', 'type' => 'color', 'group' => 'brand'],
            ['key' => 'contact.address', 'value' => '123 Faith St, Springfield, USA', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact.phone', 'value' => '(555) 123-4567', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact.email', 'value' => 'hello@church.local', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact.service_times', 'value' => "Sunday 9:00 AM & 11:00 AM\nWednesday 7:00 PM", 'type' => 'text', 'group' => 'contact'],
            ['key' => 'social.facebook', 'value' => '', 'type' => 'text', 'group' => 'social'],
            ['key' => 'social.instagram', 'value' => '', 'type' => 'text', 'group' => 'social'],
            ['key' => 'social.youtube', 'value' => '', 'type' => 'text', 'group' => 'social'],
            ['key' => 'social.x', 'value' => '', 'type' => 'text', 'group' => 'social'],
            ['key' => 'footer.about', 'value' => 'A welcoming community for everyone.', 'type' => 'text', 'group' => 'footer'],
            ['key' => 'seo.default_title_suffix', 'value' => ' | Assemblies of God Rawalpindi', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'seo.default_description', 'value' => 'Welcome to Assemblies of God, Rawalpindi.', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'seo.analytics_script', 'value' => '', 'type' => 'html', 'group' => 'seo'],
            // Phase 2 additions
            ['key' => 'member.registration_enabled', 'value' => '1', 'type' => 'text', 'group' => 'member'],
            ['key' => 'member.welcome_message', 'value' => 'Welcome to the Assemblies of God Rawalpindi family.', 'type' => 'text', 'group' => 'member'],
            ['key' => 'member.broadcast_on_feed_default', 'value' => '0', 'type' => 'text', 'group' => 'member'],
            ['key' => 'donate.button_label', 'value' => 'Give', 'type' => 'text', 'group' => 'donate'],
            ['key' => 'donate.show_in_member_nav', 'value' => '1', 'type' => 'text', 'group' => 'donate'],
            // Phase 3 additions
            ['key' => 'finance.currency_symbol', 'value' => '$', 'type' => 'text', 'group' => 'finance'],
            ['key' => 'reminders.event_24h_enabled', 'value' => '1', 'type' => 'text', 'group' => 'reminders'],
            ['key' => 'reminders.weekly_digest_enabled', 'value' => '1', 'type' => 'text', 'group' => 'reminders'],
            ['key' => 'reminders.weekly_digest_day', 'value' => 'Saturday', 'type' => 'text', 'group' => 'reminders'],
            ['key' => 'reminders.weekly_digest_hour', 'value' => '18', 'type' => 'text', 'group' => 'reminders'],
            ['key' => 'reminders.admin_daily_digest_enabled', 'value' => '0', 'type' => 'text', 'group' => 'reminders'],
        ];

        foreach ($defaults as $row) {
            SiteSetting::updateOrCreate(['key' => $row['key']], $row);
        }
    }
}
