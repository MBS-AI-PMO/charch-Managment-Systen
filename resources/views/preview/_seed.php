<?php
// Static seed data used by the /preview/* mock site. No DB queries.
// Loaded via @php include __DIR__.'/_seed.php' @endphp so variables persist in the host view's scope.

$siteNav = [
    ['label' => 'Home',       'url' => route('preview.home')],
    ['label' => 'About',      'url' => route('preview.about')],
    ['label' => 'Sermons',    'url' => route('preview.sermons')],
    ['label' => 'Events',     'url' => route('preview.events')],
    ['label' => 'Ministries', 'url' => route('preview.ministries')],
    ['label' => 'Blog',       'url' => route('preview.blog')],
    ['label' => 'Contact',    'url' => route('preview.contact')],
];

$footerInfo = [
    'address'  => '124 Maple Street, Springfield, IL 62704',
    'phone'    => '(217) 555-0123',
    'email'    => 'hello@gracecommunity.example',
    'services' => "Sundays · 9:00 AM Traditional\nSundays · 11:00 AM Contemporary\nWednesdays · 7:00 PM Prayer",
];

$speakers = [
    ['name' => 'Pastor David Reyes',   'role' => 'Senior Pastor',     'photo' => 'https://picsum.photos/seed/speaker-david/200/200'],
    ['name' => 'Pastor Maria Chen',    'role' => 'Associate Pastor',  'photo' => 'https://picsum.photos/seed/speaker-maria/200/200'],
    ['name' => 'Rev. Samuel Okafor',   'role' => 'Teaching Pastor',   'photo' => 'https://picsum.photos/seed/speaker-samuel/200/200'],
    ['name' => 'Pastor Anna Whitfield','role' => 'Worship Pastor',    'photo' => 'https://picsum.photos/seed/speaker-anna/200/200'],
];

$series = [
    ['title' => 'Rooted: Living Faith Daily', 'image' => 'https://picsum.photos/seed/series-rooted/600/400'],
    ['title' => 'The Sermon on the Mount',    'image' => 'https://picsum.photos/seed/series-sotm/600/400'],
    ['title' => 'Hope in the Wilderness',     'image' => 'https://picsum.photos/seed/series-hope/600/400'],
    ['title' => 'Letters to a Young Church',  'image' => 'https://picsum.photos/seed/series-letters/600/400'],
];

$sermons = [
    ['slug'=>'sample','title'=>'When the Vine Holds Fast','speaker'=>'Pastor David Reyes','series'=>'Rooted: Living Faith Daily','date'=>'May 25, 2026','scripture'=>'John 15:1–11','summary'=>'A reflection on abiding in Christ when life feels stripped back, and discovering the deep work the gardener is doing.','image'=>'https://picsum.photos/seed/sermon-vine/1200/675','duration'=>'38 min'],
    ['slug'=>'sample','title'=>'Blessed Are the Merciful','speaker'=>'Pastor Maria Chen','series'=>'The Sermon on the Mount','date'=>'May 18, 2026','scripture'=>'Matthew 5:7','summary'=>'Mercy is not weakness — it is the muscle of the kingdom. Exploring what it means to give what we ourselves have received.','image'=>'https://picsum.photos/seed/sermon-mercy/1200/675','duration'=>'34 min'],
    ['slug'=>'sample','title'=>'Manna in the Morning','speaker'=>'Rev. Samuel Okafor','series'=>'Hope in the Wilderness','date'=>'May 11, 2026','scripture'=>'Exodus 16','summary'=>'Daily bread for daily faith — why God taught Israel to gather only what they needed for today.','image'=>'https://picsum.photos/seed/sermon-manna/1200/675','duration'=>'41 min'],
    ['slug'=>'sample','title'=>'The Long Obedience','speaker'=>'Pastor David Reyes','series'=>'Rooted: Living Faith Daily','date'=>'May 4, 2026','scripture'=>'Psalm 84','summary'=>'A meditation on perseverance — how the saints we admire all walked the same road, one step at a time.','image'=>'https://picsum.photos/seed/sermon-obedience/1200/675','duration'=>'36 min'],
    ['slug'=>'sample','title'=>'Songs in the Night','speaker'=>'Pastor Anna Whitfield','series'=>'Hope in the Wilderness','date'=>'Apr 27, 2026','scripture'=>'Psalm 42','summary'=>'Worship as a defiant act of trust in the dark. Why we still sing when the answers have not arrived.','image'=>'https://picsum.photos/seed/sermon-songs/1200/675','duration'=>'32 min'],
    ['slug'=>'sample','title'=>'A Church on Mission','speaker'=>'Pastor Maria Chen','series'=>'Letters to a Young Church','date'=>'Apr 20, 2026','scripture'=>'1 Thessalonians 1','summary'=>'What made the Thessalonian church ring out across the region — and what it might look like today.','image'=>'https://picsum.photos/seed/sermon-mission/1200/675','duration'=>'39 min'],
];

$events = [
    ['slug'=>'sample','title'=>'Community Block Party','date'=>'Sat, Jun 14, 2026','day'=>'14','month'=>'Jun','time'=>'4:00 PM – 8:00 PM','location'=>'Church Lawn & Maple Street','image'=>'https://picsum.photos/seed/event-block/1200/675','summary'=>'Free food, live music, kids games, and a chance to meet the neighborhood. Bring a friend.','tag'=>'Community','status'=>'upcoming'],
    ['slug'=>'sample','title'=>'Mens Breakfast & Bible Study','date'=>'Sat, Jun 21, 2026','day'=>'21','month'=>'Jun','time'=>'7:30 AM – 9:00 AM','location'=>'Fellowship Hall','image'=>'https://picsum.photos/seed/event-mens/1200/675','summary'=>'Pancakes, coffee, and an honest conversation about leading well in everyday life.','tag'=>'Mens','status'=>'upcoming'],
    ['slug'=>'sample','title'=>'Vacation Bible School','date'=>'Jul 7 – Jul 11, 2026','day'=>'07','month'=>'Jul','time'=>'9:00 AM – 12:00 PM','location'=>'Main Campus','image'=>'https://picsum.photos/seed/event-vbs/1200/675','summary'=>'A week-long adventure for kids K–5. Songs, snacks, stories, and a whole lot of joy.','tag'=>'Kids','status'=>'upcoming'],
    ['slug'=>'sample','title'=>'Womens Retreat: Quiet Waters','date'=>'Aug 15 – Aug 17, 2026','day'=>'15','month'=>'Aug','time'=>'Weekend','location'=>'Pine Lake Lodge','image'=>'https://picsum.photos/seed/event-retreat/1200/675','summary'=>'A weekend away to rest, listen, and reconnect — with God and with one another.','tag'=>'Womens','status'=>'upcoming'],
    ['slug'=>'sample','title'=>'Spring Worship Night','date'=>'Apr 12, 2026','day'=>'12','month'=>'Apr','time'=>'7:00 PM','location'=>'Sanctuary','image'=>'https://picsum.photos/seed/event-worship/1200/675','summary'=>'An evening of extended worship, prayer, and stillness — open to the whole community.','tag'=>'Worship','status'=>'past'],
    ['slug'=>'sample','title'=>'Easter Sunday Services','date'=>'Apr 5, 2026','day'=>'05','month'=>'Apr','time'=>'8 AM · 10 AM · Noon','location'=>'Sanctuary','image'=>'https://picsum.photos/seed/event-easter/1200/675','summary'=>'He is risen. A celebration in song, scripture, and community.','tag'=>'Worship','status'=>'past'],
];

$ministries = [
    ['slug'=>'sample','name'=>'Kids @ Grace','tagline'=>'Faith, fun, and safe space for children K–5.','image'=>'https://picsum.photos/seed/min-kids/800/600','leader'=>['name'=>'Hannah Brooks','role'=>'Childrens Director','email'=>'kids@gracecommunity.example','photo'=>'https://picsum.photos/seed/leader-hannah/300/300'],'body'=>'Every Sunday morning, our team welcomes kids into age-appropriate classrooms where they meet Jesus through stories, songs, and small groups. Background-checked volunteers, a secure check-in system, and lots of joy.'],
    ['slug'=>'sample','name'=>'Student Ministry','tagline'=>'Middle and high schoolers chasing real faith together.','image'=>'https://picsum.photos/seed/min-students/800/600','leader'=>['name'=>'Daniel Park','role'=>'Student Pastor','email'=>'students@gracecommunity.example','photo'=>'https://picsum.photos/seed/leader-daniel/300/300'],'body'=>'Wednesday nights are loud, fun, and rooted in scripture. Small groups by grade and gender, monthly hangouts, and a yearly summer trip you wont forget.'],
    ['slug'=>'sample','name'=>'Worship & Arts','tagline'=>'Singers, musicians, designers — all welcome.','image'=>'https://picsum.photos/seed/min-worship/800/600','leader'=>['name'=>'Pastor Anna Whitfield','role'=>'Worship Pastor','email'=>'worship@gracecommunity.example','photo'=>'https://picsum.photos/seed/leader-anna/300/300'],'body'=>'We believe worship is for everyone, and we love helping people use their gifts on Sundays and beyond. Auditions are casual — start with a coffee with the team.'],
    ['slug'=>'sample','name'=>'Community Outreach','tagline'=>'Loving Springfield, one neighbor at a time.','image'=>'https://picsum.photos/seed/min-outreach/800/600','leader'=>['name'=>'Marcus Hill','role'=>'Outreach Director','email'=>'outreach@gracecommunity.example','photo'=>'https://picsum.photos/seed/leader-marcus/300/300'],'body'=>'From food pantry shifts to school-supply drives to neighborhood cleanups, we partner with local organizations to put faith into action.'],
    ['slug'=>'sample','name'=>'Small Groups','tagline'=>'Life is better in a circle than a row.','image'=>'https://picsum.photos/seed/min-groups/800/600','leader'=>['name'=>'Elena Marquez','role'=>'Groups Pastor','email'=>'groups@gracecommunity.example','photo'=>'https://picsum.photos/seed/leader-elena/300/300'],'body'=>'Groups meet weekly in homes across the city. Open seasons begin in September and January — find one near you.'],
    ['slug'=>'sample','name'=>'Prayer Ministry','tagline'=>'Quietly holding the church together.','image'=>'https://picsum.photos/seed/min-prayer/800/600','leader'=>['name'=>'Joyce Tan','role'=>'Prayer Team Lead','email'=>'prayer@gracecommunity.example','photo'=>'https://picsum.photos/seed/leader-joyce/300/300'],'body'=>'Submit a confidential prayer request, or join the team that prays through every request weekly. No experience required — just availability.'],
];

$posts = [
    ['slug'=>'sample','title'=>'Five Habits That Quietly Change Everything','excerpt'=>'They wont go viral and they wont feel heroic. But after a year of practicing them, you wont recognize yourself.','author'=>'Pastor David Reyes','date'=>'May 22, 2026','category'=>'Discipleship','image'=>'https://picsum.photos/seed/post-habits/1200/675','authorPhoto'=>'https://picsum.photos/seed/speaker-david/200/200'],
    ['slug'=>'sample','title'=>'What We Mean When We Say "Community"','excerpt'=>'Its one of the most overused words in church life. Heres what we actually mean by it at Grace.','author'=>'Pastor Maria Chen','date'=>'May 15, 2026','category'=>'Church Life','image'=>'https://picsum.photos/seed/post-community/1200/675','authorPhoto'=>'https://picsum.photos/seed/speaker-maria/200/200'],
    ['slug'=>'sample','title'=>'A Letter to Anyone Coming Back After a Long Time Away','excerpt'=>'You are not as far from God as you feel. Some thoughts for those wondering whether to walk through the door again.','author'=>'Rev. Samuel Okafor','date'=>'May 8, 2026','category'=>'Pastoral','image'=>'https://picsum.photos/seed/post-letter/1200/675','authorPhoto'=>'https://picsum.photos/seed/speaker-samuel/200/200'],
    ['slug'=>'sample','title'=>'Why We Still Read the Old Testament','excerpt'=>'Genealogies, sacrifices, kingdoms — and somehow, the heartbeat of the gospel running through all of it.','author'=>'Pastor David Reyes','date'=>'Apr 30, 2026','category'=>'Bible','image'=>'https://picsum.photos/seed/post-ot/1200/675','authorPhoto'=>'https://picsum.photos/seed/speaker-david/200/200'],
    ['slug'=>'sample','title'=>'How to Read the Bible With a Friend','excerpt'=>'A simple, repeatable rhythm for two people who want to take scripture seriously without making it weird.','author'=>'Pastor Maria Chen','date'=>'Apr 23, 2026','category'=>'Discipleship','image'=>'https://picsum.photos/seed/post-twos/1200/675','authorPhoto'=>'https://picsum.photos/seed/speaker-maria/200/200'],
];

$blogCategories = ['All', 'Discipleship', 'Church Life', 'Pastoral', 'Bible', 'Mission'];

$beliefs = [
    ['icon'=>'book','title'=>'Scripture','body'=>'We hold the Bible to be the inspired, trustworthy word of God — sufficient for faith and life.'],
    ['icon'=>'heart','title'=>'Grace','body'=>'Salvation is the free gift of God through Jesus Christ, received by faith, not earned by works.'],
    ['icon'=>'users','title'=>'Community','body'=>'We are made for one another. The local church is Gods primary plan for shaping our lives.'],
    ['icon'=>'globe','title'=>'Mission','body'=>'We are sent — into our city, our schools, our workplaces — to make Christ known in word and deed.'],
    ['icon'=>'cross','title'=>'The Gospel','body'=>'Jesus lived, died, rose again, and is coming back. The gospel is the news that changes everything.'],
    ['icon'=>'dove','title'=>'The Spirit','body'=>'The Holy Spirit indwells believers, empowers the church, and gives gifts for the common good.'],
];

$leaders = [
    ['name'=>'Pastor David Reyes',    'role'=>'Senior Pastor',    'photo'=>'https://picsum.photos/seed/leader-david/400/500'],
    ['name'=>'Pastor Maria Chen',     'role'=>'Associate Pastor', 'photo'=>'https://picsum.photos/seed/leader-maria/400/500'],
    ['name'=>'Rev. Samuel Okafor',    'role'=>'Teaching Pastor',  'photo'=>'https://picsum.photos/seed/leader-samuel/400/500'],
    ['name'=>'Pastor Anna Whitfield', 'role'=>'Worship Pastor',   'photo'=>'https://picsum.photos/seed/leader-anna2/400/500'],
];
