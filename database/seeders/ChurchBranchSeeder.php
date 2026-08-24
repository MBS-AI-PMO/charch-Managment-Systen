<?php

namespace Database\Seeders;

use App\Models\ChurchBranch;
use Illuminate\Database\Seeder;

class ChurchBranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'slug' => 'aog-church-zafar-town',
                'name' => 'AOG Church Zafar Town',
                'city' => 'Zafar Town, Lahore',
                'role' => 'Main campus',
                'address' => 'Street 4, Zafar Town, Lahore',
                'services' => "Sunday Worship: 10:00 AM – 12:00 PM\nBible Study: Wednesday, 6:30 PM – 8:00 PM\nPrayer Meeting: Friday, 7:00 PM",
                'note' => 'Our main congregation in Zafar Town — gathered for worship, teaching, and family discipleship.',
                'pastor' => 'Pastor Aamir',
                'language' => 'Urdu and English',
                'hero_sub' => 'A welcoming Assemblies of God family in Zafar Town.',
                'about' => "AOG Church Zafar Town is our main campus — a place to belong, grow, and serve. Sunday gatherings are centred on Scripture, worship, and prayer, with space for families, youth, and first-time guests.\n\nWhether you have walked with Jesus for years or are simply looking for a church nearby, you are invited to come as you are. After the service our team will greet you, help with children’s check-in, and point you to a next step.\n\nThrough the week we meet for Bible study, prayer, and home groups so faith is not only a Sunday event but a shared life in Zafar Town.",
                'expect' => "A warm welcome at the door and help finding a seat\nWorship, Scripture reading, and a clear Bible message\nChildren’s ministry during the main Sunday service\nTea and conversation after the gathering",
                'ministries' => "Sunday worship and children’s ministry\nYouth and young adults\nHome groups and midweek Bible study\nPrayer, visitation, and community care\nWorship team and serving opportunities",
                'families' => 'Families are welcome in the sanctuary. Children can stay with parents or join age-appropriate classes during the Sunday message. A nursing-room space is available on request.',
                'visit' => 'Arrive about 10–15 minutes early so we can welcome you, help with parking, and show you to the sanctuary and children’s area. Street parking is available around Street 4.',
                'getting_here' => 'We meet on Street 4 in Zafar Town, Lahore. If you are visiting for the first time, call or message the church office and we will share the easiest landmark and directions.',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'slug' => 'aog-church-razzaq-town',
                'name' => 'AOG Church Razzaq Town',
                'city' => 'Razzaq Town',
                'role' => 'Branch',
                'address' => 'Near Main Bazaar, Razzaq Town',
                'services' => "Sunday Service: 11:00 AM – 1:00 PM\nPrayer Meeting: Thursday, 6:00 PM – 7:30 PM\nYouth Gathering: Saturday, 5:00 PM",
                'note' => 'A growing branch congregation in Razzaq Town, connected to the same church family.',
                'pastor' => 'Pastor Team',
                'language' => 'Urdu',
                'hero_sub' => 'A local Assemblies of God fellowship for families in Razzaq Town.',
                'about' => "AOG Church Razzaq Town is our branch congregation — a neighbourhood church where people gather to worship, pray, and walk with one another through the week. Services are simple, warm, and open to every generation.\n\nThis campus belongs to the same Assemblies of God family as Zafar Town. You will find the same heart for Jesus, Scripture, and hospitality, in a smaller local setting near Main Bazaar.\n\nJoin us on Sunday, stay for tea afterwards, and meet the people who call this church home. We would love to pray with you and help you get connected.",
                'expect' => "A smaller gathering where guests are noticed and welcomed\nWorship and a Bible message you can apply at home\nPrayer for families, healing, and everyday needs\nFellowship after the service",
                'ministries' => "Sunday worship and fellowship\nWomen’s prayer circle\nYouth gathering on Saturday\nOutreach and family visitation\nMidweek prayer meeting",
                'families' => 'Children are welcome in the service. Parents may sit together as a family. Youth meet on Saturday so young people have a place of their own during the week.',
                'visit' => 'Look for the welcome team at the entrance near Main Bazaar. They will help you find a seat and introduce you to the congregation. If you need help locating the building, call us before you come.',
                'getting_here' => 'We gather near Main Bazaar, Razzaq Town. Ask for AOG Church Razzaq Town — neighbours nearby can point you to the meeting place.',
                'sort_order' => 2,
                'is_published' => true,
            ],
        ];

        foreach ($branches as $row) {
            ChurchBranch::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
