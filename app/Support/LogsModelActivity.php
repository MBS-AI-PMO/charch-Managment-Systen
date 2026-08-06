<?php

namespace App\Support;

use App\Models\ActivityLog;

trait LogsModelActivity
{
    protected static function bootLogsModelActivity(): void
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::$event(function ($model) use ($event) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'subject_type' => static::class,
                    'subject_id' => $model->id ?? 0,
                    'action' => $event,
                    'changes' => $event === 'updated' ? $model->getChanges() : null,
                    'ip' => request()?->ip(),
                ]);
            });
        }
    }
}
