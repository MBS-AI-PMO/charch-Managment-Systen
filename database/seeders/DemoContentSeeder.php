<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Event;
use App\Models\Ministry;
use App\Models\Page;
use App\Models\Sermon;
use App\Models\SermonSeries;
use App\Models\SermonSpeaker;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', env('ADMIN_EMAIL', 'admin@church.local'))->first();
        $authorId = $author?->id;

        $this->seedPages();
        $this->seedBlog($authorId);
        $this->seedSermons();
        $this->seedEvents();
        $this->seedMinistries();
        $this->seedFeedPosts($authorId);
        $this->seedPrayerRequests();
    }

    private function seedFeedPosts(?int $authorId): void
    {
        if (! $authorId) {
            return;
        }
        $posts = [
            [
                'title' => 'Welcome to the new member portal',
                'body' => '<p>We are so glad to have you here. Take a moment to explore — submit a prayer request, knock for help if you need pastoral care, or just say hi.</p>',
                'pinned' => true,
                'published_at' => now()->subHours(2),
            ],
            [
                'title' => 'Sunday gathering: a special evening',
                'body' => '<p>Join us for an extended worship time at 6pm this Sunday. We will have communion together and time for personal prayer.</p>',
                'pinned' => false,
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Volunteer call: kids ministry',
                'body' => '<p>We need 2 more volunteers for Sunday mornings. If you have a heart for kids, please reach out.</p>',
                'pinned' => false,
                'published_at' => now()->subDays(3),
            ],
        ];
        foreach ($posts as $p) {
            \App\Models\FeedPost::create($p + ['author_id' => $authorId]);
        }
    }

    private function seedPrayerRequests(): void
    {
        $member = \App\Models\User::where('is_admin', false)->first()
            ?? \App\Models\User::where('is_admin', true)->first();
        if (! $member) {
            return;
        }
        $samples = [
            ['title' => 'Wisdom for a big decision', 'body' => 'Praying for clarity on a job offer this week.', 'is_public' => true, 'status' => 'praying'],
            ['title' => 'Healing for my mother', 'body' => 'She has been unwell for a few weeks. Asking for peace and healing.', 'is_public' => true, 'is_anonymous' => true, 'status' => 'pending'],
            ['title' => 'New beginnings', 'body' => 'Just moved to the area — pray we settle in well.', 'is_public' => true, 'status' => 'answered'],
            ['title' => 'Family situation', 'body' => 'Private prayer needed for a family relationship.', 'is_public' => false, 'status' => 'pending'],
            ['title' => 'Mission trip prep', 'body' => 'Praying for our team heading out next month.', 'is_public' => true, 'status' => 'praying'],
        ];
        foreach ($samples as $s) {
            \App\Models\PrayerRequest::create(array_merge([
                'user_id' => $member->id,
                'name' => $member->name,
                'pray_count' => rand(0, 12),
            ], $s));
        }
    }

    private function seedPages(): void
    {
        $now = now();

        $pages = [
            [
                'slug' => 'home',
                'title' => 'Welcome Home',
                'hero_heading' => 'A Place To Belong',
                'hero_subheading' => 'Join our community as we worship, grow, and serve together.',
                'hero_image_path' => 'seed/home-hero.jpg',
                'body' => 'Grace Community Church is a vibrant, welcoming community of believers dedicated to loving God and loving people. Whether you are exploring faith for the first time or have walked with Christ for decades, you will find a place here. Our Sunday services are filled with heartfelt worship, biblical teaching, and authentic relationships. Throughout the week, our small groups, ministries, and outreach programs offer countless opportunities to grow and connect. We believe church should feel like family — and we cannot wait to meet you.',
                'meta_title' => 'Grace Community Church | Home',
                'meta_description' => 'A welcoming Christian community in Springfield. Sunday services, kids ministry, small groups, and outreach.',
            ],
            [
                'slug' => 'about-us',
                'title' => 'About Us',
                'hero_heading' => 'Our Story',
                'hero_subheading' => 'Rooted in Scripture, growing in grace.',
                'hero_image_path' => 'seed/about-hero.jpg',
                'body' => 'Grace Community Church was founded in 1987 by a small group of families who believed our town needed a church focused on authentic worship and discipleship. Nearly forty years later, we are still that same family — just much larger. We hold to historic Christian doctrine, expository Bible teaching, and a commitment to serving our neighbors. Our leadership team is comprised of pastors and elders who have devoted their lives to shepherding this congregation. We exist to make disciples of Jesus Christ who love God, love one another, and love the world He died to save.',
                'meta_title' => 'About Grace Community Church',
                'meta_description' => 'Learn about our history, beliefs, and leadership team.',
            ],
            [
                'slug' => 'contact-us',
                'title' => 'Contact Us',
                'hero_heading' => 'Get In Touch',
                'hero_subheading' => 'We would love to hear from you.',
                'hero_image_path' => 'seed/contact-hero.jpg',
                'body' => 'Have a question, prayer request, or want to plan a visit? Our team is here to help. You can reach us by phone, email, or by visiting us during office hours Monday through Friday from 9 AM to 4 PM. If this is your first time, let us know — we will have someone greet you personally on Sunday and answer any questions about our community. We do our best to respond to all messages within one business day.',
                'meta_title' => 'Contact Grace Community Church',
                'meta_description' => 'Phone, email, address, and service times.',
            ],
            [
                'slug' => 'ministries',
                'title' => 'Our Ministries',
                'hero_heading' => 'Find Your Place To Serve',
                'hero_subheading' => 'Every member, a minister.',
                'hero_image_path' => 'seed/ministries-hero.jpg',
                'body' => 'God has gifted every believer to contribute to the work of His church. Our ministries exist to mobilize those gifts and bless our congregation, community, and world. From kids and students, to worship, missions, and care — there is a place for you to grow and serve. Explore the ministries below and reach out to a leader to take your next step.',
                'meta_title' => 'Ministries at Grace Community Church',
                'meta_description' => 'Kids, students, worship, outreach, and more.',
            ],
            [
                'slug' => 'sermons',
                'title' => 'Sermons',
                'hero_heading' => 'Recent Messages',
                'hero_subheading' => 'Stream, download, or share our weekly teaching.',
                'hero_image_path' => 'seed/sermons-hero.jpg',
                'body' => 'Our weekly messages are rooted in Scripture and aimed at the heart. Browse the latest sermon series, individual messages, or search by speaker. Audio and video are available for every message, and most include downloadable notes.',
                'meta_title' => 'Sermons | Grace Community Church',
                'meta_description' => 'Listen to or watch recent sermons.',
            ],
            [
                'slug' => 'events',
                'title' => 'Events',
                'hero_heading' => 'Upcoming Events',
                'hero_subheading' => 'There is always something happening.',
                'hero_image_path' => 'seed/events-hero.jpg',
                'body' => 'From community potlucks to retreats, classes, and outreach opportunities — our calendar is full of ways to connect, grow, and serve. Browse upcoming events below and register for the ones that interest you.',
                'meta_title' => 'Events | Grace Community Church',
                'meta_description' => 'Upcoming gatherings, classes, and outreach.',
            ],
            [
                'slug' => 'donate',
                'title' => 'Give',
                'hero_heading' => 'Support our mission',
                'hero_subheading' => 'Your generosity keeps the lights on, the doors open, and the ministry going.',
                'hero_image_path' => 'seed/donate-hero.jpg',
                'body' => '<p>Thank you for considering a gift to support our work. We rely on the generosity of our community to keep everything we do possible.</p><h2>Bank transfer</h2><p><strong>Account name:</strong> Grace Community Church<br><strong>Account number:</strong> 12345678<br><strong>Sort code:</strong> 12-34-56<br><strong>Reference:</strong> Please use your name as the reference.</p><h2>In person</h2><p>You can give cash or cheque in person on a Sunday — drop your envelope in the basket at the door.</p><h2>Questions?</h2><p>For questions about giving, gift aid, or planned giving, please <a href="/contact">get in touch</a>.</p>',
                'meta_title' => 'Give | Grace Community Church',
                'meta_description' => 'Support the mission of Grace Community Church via bank transfer or in person.',
            ],
            [
                'slug' => 'blog',
                'title' => 'News',
                'hero_heading' => 'News & updates',
                'hero_subheading' => 'Stories and updates from our church family.',
                'hero_image_path' => 'seed/blog-hero.jpg',
                'body' => 'This is where we share church news, ministry updates, and stories from the life of our congregation.',
                'meta_title' => 'News | Assemblies of God',
                'meta_description' => 'Church news, updates, and stories.',
            ],
            [
                'slug' => 'our-churches',
                'title' => 'Our Churches',
                'hero_heading' => 'Our Churches',
                'hero_subheading' => 'One family across every campus and fellowship.',
                'hero_image_path' => 'seed/about-hero.jpg',
                'body' => '<p>Assemblies of God is a family of churches and fellowships. Explore our campuses below, check service times, and find a place to belong near you.</p>',
                'meta_title' => 'Our Churches | Assemblies of God',
                'meta_description' => 'Find Assemblies of God church campuses, branches, and fellowships near you.',
            ],
            [
                'slug' => 'qa',
                'title' => 'Q&A',
                'hero_heading' => 'What people asked — and how we answered.',
                'hero_subheading' => 'A public history of questions from our community. Only names, questions, and answers are shown.',
                'hero_image_path' => 'seed/about-hero.jpg',
                'body' => '',
                'meta_title' => 'Q&A | Assemblies of God',
                'meta_description' => 'Public questions and answers from our church community.',
            ],
            [
                'slug' => 'gallery',
                'title' => 'Gallery',
                'hero_heading' => 'Gallery',
                'hero_subheading' => 'Moments from the life of our church family.',
                'hero_image_path' => 'seed/about-hero.jpg',
                'body' => '',
                'meta_title' => 'Gallery | Assemblies of God',
                'meta_description' => 'Photos from worship, events, and community life.',
            ],
        ];

        foreach ($pages as $p) {
            Page::updateOrCreate(
                ['slug' => $p['slug']],
                $p + ['is_published' => true, 'published_at' => $now]
            );
        }
    }

    private function seedBlog(?int $authorId): void
    {
        $now = now();

        $categories = [
            ['slug' => 'devotionals', 'name' => 'Devotionals'],
            ['slug' => 'church-life', 'name' => 'Church Life'],
            ['slug' => 'theology', 'name' => 'Theology'],
        ];

        $catMap = [];
        foreach ($categories as $c) {
            $cat = BlogCategory::updateOrCreate(['slug' => $c['slug']], $c);
            $catMap[$c['slug']] = $cat->id;
        }

        $posts = [
            [
                'slug' => 'finding-rest-in-a-restless-world',
                'title' => 'Finding Rest In A Restless World',
                'excerpt' => 'Jesus offers a rest that runs deeper than weekends and vacations.',
                'body' => 'We live in a culture addicted to noise, hurry, and productivity. Yet the soul was made for rhythm — for work and for rest, for striving and for stillness. In Matthew 11, Jesus calls the weary to come to Him and find rest. This is not merely the rest of an afternoon nap. It is the rest of a soul finally home in the presence of God. Sabbath rhythms, prayer, silence, and Scripture — these are not optional extras but ordinary means of grace. This week, consider one rhythm you can reclaim.',
                'category_id' => $catMap['devotionals'],
            ],
            [
                'slug' => 'why-we-baptize',
                'title' => 'Why We Baptize',
                'excerpt' => 'A look at the meaning and method of Christian baptism.',
                'body' => 'Baptism is one of the two ordinances Jesus gave His church. It is a public proclamation of an inward reality — that the believer has been united with Christ in His death, burial, and resurrection. We baptize new believers by immersion, following the New Testament pattern. It is not what saves us, but it is the first step of obedience after putting our faith in Jesus. If you have trusted Christ and have not been baptized, we would love to talk with you about next steps. Our next baptism Sunday is coming up soon.',
                'category_id' => $catMap['theology'],
            ],
            [
                'slug' => 'welcoming-our-new-kids-director',
                'title' => 'Welcoming Our New Kids Director',
                'excerpt' => 'Meet Jamie, our new Director of Childrens Ministry.',
                'body' => 'After a season of prayer and a thorough search, we are thrilled to introduce Jamie Patterson as our new Director of Childrens Ministry. Jamie comes to us with twelve years of experience leading childrens programs at growing churches, and a heart for helping kids encounter Jesus in age-appropriate, engaging ways. Jamie and family will be joining us officially next month. Please join us in praying for their transition and stop by the Kids Wing to say hello on their first Sunday.',
                'category_id' => $catMap['church-life'],
            ],
            [
                'slug' => 'how-to-read-the-old-testament',
                'title' => 'How To Read The Old Testament',
                'excerpt' => 'Practical tips for getting more out of those harder books.',
                'body' => 'The Old Testament can feel intimidating — strange laws, long genealogies, ancient battles. But every page points to Jesus. Start with a good study Bible. Read narrative books like Genesis and Exodus before jumping into Leviticus. Pay attention to recurring themes: covenant, kingdom, exile, promise. Remember that the goal is not information but transformation. Above all, pray as you read — asking the Spirit who inspired Scripture to illuminate it for you today.',
                'category_id' => $catMap['devotionals'],
            ],
            [
                'slug' => 'serving-our-neighbors-this-fall',
                'title' => 'Serving Our Neighbors This Fall',
                'excerpt' => 'Six new outreach opportunities open up this season.',
                'body' => 'Our outreach team has been hard at work building partnerships with local schools, shelters, and food banks. This fall, we are launching six new ways to serve our neighbors: a back-to-school supply drive, weekly tutoring at Lincoln Elementary, monthly meals at the downtown shelter, a coat drive in November, Thanksgiving baskets for forty families, and Christmas gifts for kids in foster care. Sign-ups open this Sunday in the lobby. There is a way for everyone to get involved.',
                'category_id' => $catMap['church-life'],
            ],
            [
                'slug' => 'the-doctrine-of-grace',
                'title' => 'The Doctrine Of Grace',
                'excerpt' => 'Grace is not a license to sin but the power to obey.',
                'body' => 'Grace is one of the most beautiful and misunderstood words in the Christian vocabulary. The apostle Paul defined it as unmerited favor — Gods kindness given freely to those who could never earn it. But grace is also more than a transaction. It is a present-tense power. The same grace that saves us also teaches us to renounce ungodliness and live self-controlled, upright, godly lives. We are not saved by grace and then sustained by effort; we are saved and sustained by grace from beginning to end.',
                'category_id' => $catMap['theology'],
            ],
        ];

        foreach ($posts as $p) {
            BlogPost::updateOrCreate(
                ['slug' => $p['slug']],
                $p + [
                    'author_id' => $authorId,
                    'featured_image_path' => 'seed/blog/'.$p['slug'].'.jpg',
                    'is_published' => true,
                    'published_at' => $now,
                ]
            );
        }
    }

    private function seedSermons(): void
    {
        $now = now();

        $series = [
            [
                'slug' => 'the-good-shepherd',
                'name' => 'The Good Shepherd',
                'description' => 'A four-week journey through John 10 and Psalm 23.',
                'cover_image_path' => 'seed/series/good-shepherd.jpg',
            ],
            [
                'slug' => 'kingdom-come',
                'name' => 'Kingdom Come',
                'description' => 'Six messages on the parables of the Kingdom.',
                'cover_image_path' => 'seed/series/kingdom-come.jpg',
            ],
        ];

        $seriesMap = [];
        foreach ($series as $s) {
            $row = SermonSeries::updateOrCreate(['slug' => $s['slug']], $s);
            $seriesMap[$s['slug']] = $row->id;
        }

        $speakers = [
            [
                'slug' => 'pastor-david-kim',
                'name' => 'Pastor David Kim',
                'role' => 'Lead Pastor',
                'bio' => 'David has served at Grace for eleven years. He and his wife Esther have three children.',
                'photo_path' => 'seed/speakers/david-kim.jpg',
            ],
            [
                'slug' => 'pastor-sarah-novak',
                'name' => 'Pastor Sarah Novak',
                'role' => 'Teaching Pastor',
                'bio' => 'Sarah holds a Master of Divinity and teaches with clarity and warmth.',
                'photo_path' => 'seed/speakers/sarah-novak.jpg',
            ],
            [
                'slug' => 'pastor-marcus-bell',
                'name' => 'Pastor Marcus Bell',
                'role' => 'Associate Pastor',
                'bio' => 'Marcus oversees our small groups and care ministries.',
                'photo_path' => 'seed/speakers/marcus-bell.jpg',
            ],
        ];

        $speakerMap = [];
        foreach ($speakers as $sp) {
            $row = SermonSpeaker::updateOrCreate(['slug' => $sp['slug']], $sp);
            $speakerMap[$sp['slug']] = $row->id;
        }

        $sermons = [
            ['slug' => 'i-am-the-good-shepherd', 'title' => 'I Am The Good Shepherd', 'series' => 'the-good-shepherd', 'speaker' => 'pastor-david-kim', 'scripture' => 'John 10:11-18', 'days_ago' => 28],
            ['slug' => 'the-lord-is-my-shepherd', 'title' => 'The Lord Is My Shepherd', 'series' => 'the-good-shepherd', 'speaker' => 'pastor-david-kim', 'scripture' => 'Psalm 23:1-3', 'days_ago' => 21],
            ['slug' => 'through-the-valley', 'title' => 'Through The Valley', 'series' => 'the-good-shepherd', 'speaker' => 'pastor-sarah-novak', 'scripture' => 'Psalm 23:4', 'days_ago' => 14],
            ['slug' => 'a-table-prepared', 'title' => 'A Table Prepared', 'series' => 'the-good-shepherd', 'speaker' => 'pastor-david-kim', 'scripture' => 'Psalm 23:5-6', 'days_ago' => 7],
            ['slug' => 'the-sower-and-the-soils', 'title' => 'The Sower And The Soils', 'series' => 'kingdom-come', 'speaker' => 'pastor-david-kim', 'scripture' => 'Matthew 13:1-23', 'days_ago' => 84],
            ['slug' => 'mustard-seed-faith', 'title' => 'Mustard Seed Faith', 'series' => 'kingdom-come', 'speaker' => 'pastor-marcus-bell', 'scripture' => 'Matthew 13:31-32', 'days_ago' => 77],
            ['slug' => 'the-hidden-treasure', 'title' => 'The Hidden Treasure', 'series' => 'kingdom-come', 'speaker' => 'pastor-sarah-novak', 'scripture' => 'Matthew 13:44', 'days_ago' => 70],
            ['slug' => 'the-pearl-of-great-price', 'title' => 'The Pearl Of Great Price', 'series' => 'kingdom-come', 'speaker' => 'pastor-david-kim', 'scripture' => 'Matthew 13:45-46', 'days_ago' => 63],
        ];

        foreach ($sermons as $s) {
            Sermon::updateOrCreate(
                ['slug' => $s['slug']],
                [
                    'title' => $s['title'],
                    'summary' => 'A message exploring '.$s['scripture'].' and its implications for our daily walk with Christ.',
                    'body' => 'In this message we walk slowly through the passage, listening for what the Spirit might be saying to our church in this season. We consider the original context, the literary structure, and the redemptive arc that points to Christ. Then we apply the text to our own lives — how we work, how we love, how we suffer, how we hope. May the Lord use this word to deepen our faith and equip us for every good work.',
                    'series_id' => $seriesMap[$s['series']],
                    'speaker_id' => $speakerMap[$s['speaker']],
                    'scripture_reference' => $s['scripture'],
                    'preached_on' => now()->subDays($s['days_ago'])->toDateString(),
                    'audio_url' => 'https://example.com/audio/'.$s['slug'].'.mp3',
                    'video_url' => 'https://youtube.com/watch?v='.substr(md5($s['slug']), 0, 11),
                    'thumbnail_path' => 'seed/sermons/'.$s['slug'].'.jpg',
                    'downloads_enabled' => true,
                    'is_published' => true,
                ]
            );
        }
    }

    private function seedEvents(): void
    {
        $events = [
            [
                'slug' => 'fall-family-picnic',
                'title' => 'Fall Family Picnic',
                'description' => 'Bring a side dish and your favorite outdoor game. Burgers, hot dogs, and drinks provided. Bounce houses for the kids, lawn games for everyone, and worship around the campfire as the sun sets.',
                'location' => 'Riverside Park Pavilion 3',
                'starts_at' => now()->addDays(10)->setTime(12, 0),
                'ends_at' => now()->addDays(10)->setTime(17, 0),
                'is_featured' => true,
            ],
            [
                'slug' => 'mens-breakfast',
                'title' => 'Mens Breakfast',
                'description' => 'Pancakes, sausage, strong coffee, and a short message from one of our pastors. All men welcome, bring a friend.',
                'location' => 'Fellowship Hall',
                'starts_at' => now()->addDays(20)->setTime(8, 0),
                'ends_at' => now()->addDays(20)->setTime(10, 0),
                'is_featured' => false,
            ],
            [
                'slug' => 'womens-retreat',
                'title' => 'Womens Retreat',
                'description' => 'Two days of teaching, worship, and rest at a beautiful lakeside conference center. Registration includes lodging, meals, and all sessions.',
                'location' => 'Lakeview Conference Center',
                'starts_at' => now()->addDays(45)->setTime(17, 0),
                'ends_at' => now()->addDays(47)->setTime(15, 0),
                'is_featured' => true,
            ],
            [
                'slug' => 'summer-vbs-2025',
                'title' => 'Summer VBS 2025',
                'description' => 'A week of music, games, crafts, and Bible teaching for kids entering K-5th grade. Volunteers and registrations both welcome.',
                'location' => 'Main Campus',
                'starts_at' => now()->subDays(60)->setTime(9, 0),
                'ends_at' => now()->subDays(56)->setTime(12, 0),
                'is_featured' => false,
            ],
            [
                'slug' => 'spring-baptism-sunday',
                'title' => 'Spring Baptism Sunday',
                'description' => 'A celebration service for those taking the next step of baptism. Family and friends warmly invited to attend.',
                'location' => 'Main Auditorium',
                'starts_at' => now()->subDays(120)->setTime(10, 30),
                'ends_at' => now()->subDays(120)->setTime(12, 0),
                'is_featured' => false,
            ],
        ];

        foreach ($events as $e) {
            Event::updateOrCreate(
                ['slug' => $e['slug']],
                $e + [
                    'cover_image_path' => 'seed/events/'.$e['slug'].'.jpg',
                    'registration_url' => 'https://example.com/register/'.$e['slug'],
                    'is_published' => true,
                ]
            );
        }
    }

    private function seedMinistries(): void
    {
        $ministries = [
            [
                'slug' => 'kids-ministry',
                'name' => 'Kids Ministry',
                'summary' => 'Birth through 5th grade. Safe, fun, and grounded in Scripture.',
                'body' => 'Our Kids Ministry exists to partner with parents in raising the next generation to know and love Jesus. Every Sunday during both services, our trained volunteers lead engaging, age-appropriate teaching, worship, and small groups. Our facilities are secure, our background checks are thorough, and our team is genuinely delighted to spend time with your kids.',
                'leader_name' => 'Jamie Patterson',
                'contact_email' => 'kids@church.local',
                'sort_order' => 1,
            ],
            [
                'slug' => 'student-ministry',
                'name' => 'Student Ministry',
                'summary' => 'Middle and high school students growing in faith and friendship.',
                'body' => 'Our students gather every Wednesday night for worship, teaching, small groups, and a whole lot of pizza. Summer camp, mission trips, and weekend retreats round out a year of meaningful community for 6th through 12th graders.',
                'leader_name' => 'Chris Alvarez',
                'contact_email' => 'students@church.local',
                'sort_order' => 2,
            ],
            [
                'slug' => 'worship-arts',
                'name' => 'Worship Arts',
                'summary' => 'Vocalists, instrumentalists, and tech artists serving Sunday gatherings.',
                'body' => 'If you have a gift for music, audio, video, or stage design, we would love to invite you to audition. We rehearse on Thursday evenings and serve on a rotating Sunday schedule. All members commit to weekly devotion, monthly training, and personal accountability.',
                'leader_name' => 'Tasha Wilkins',
                'contact_email' => 'worship@church.local',
                'sort_order' => 3,
            ],
            [
                'slug' => 'small-groups',
                'name' => 'Small Groups',
                'summary' => 'Life-on-life community throughout the week.',
                'body' => 'Sunday is for gathering; the rest of the week is for connecting. Our small groups meet in homes across the region and provide the relational backbone of our church. Whether you are new to faith or a long-time follower of Jesus, there is a group for you.',
                'leader_name' => 'Pastor Marcus Bell',
                'contact_email' => 'groups@church.local',
                'sort_order' => 4,
            ],
            [
                'slug' => 'outreach-missions',
                'name' => 'Outreach & Missions',
                'summary' => 'Local service and global partnerships.',
                'body' => 'We believe the church exists for the sake of the world. Our outreach team coordinates monthly service days, seasonal initiatives, and ongoing partnerships with local non-profits. Globally, we support six missionary families and two long-term church-planting partnerships.',
                'leader_name' => 'Hannah Rivera',
                'contact_email' => 'outreach@church.local',
                'sort_order' => 5,
            ],
            [
                'slug' => 'care-prayer',
                'name' => 'Care & Prayer',
                'summary' => 'Walking with one another through joy and sorrow.',
                'body' => 'Our Care team responds to needs in the body — meals after surgery, visits during hospitalization, counseling referrals, financial assistance, and grief support. Our Prayer team meets weekly and covers requests submitted by our congregation. No one walks alone here.',
                'leader_name' => 'Linda Brooks',
                'contact_email' => 'care@church.local',
                'sort_order' => 6,
            ],
        ];

        foreach ($ministries as $m) {
            Ministry::updateOrCreate(
                ['slug' => $m['slug']],
                $m + [
                    'cover_image_path' => 'seed/ministries/'.$m['slug'].'.jpg',
                    'is_published' => true,
                ]
            );
        }
    }
}
