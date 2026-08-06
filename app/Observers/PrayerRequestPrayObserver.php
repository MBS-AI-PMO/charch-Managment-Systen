<?php

namespace App\Observers;

use App\Models\PrayerRequestPray;

class PrayerRequestPrayObserver
{
    public function created(PrayerRequestPray $p): void
    {
        $p->prayerRequest()->increment('pray_count');
    }

    public function deleted(PrayerRequestPray $p): void
    {
        $p->prayerRequest()->decrement('pray_count');
    }
}
