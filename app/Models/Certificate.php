<?php

namespace App\Models;

use App\Support\CertificateTemplates;
use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    use LogsModelActivity;

    protected $guarded = [];

    protected $casts = [
        'issued_on' => 'date',
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(CertificateSend::class)->latest('sent_at');
    }

    public function templateLabel(): string
    {
        return CertificateTemplates::label($this->template);
    }

    public function templateView(): string
    {
        return CertificateTemplates::view($this->template);
    }

    public function isSent(): bool
    {
        return $this->user_id !== null && $this->sent_at !== null;
    }

    /**
     * Assign (or re-assign) this certificate to a member and append history.
     */
    public function assignTo(User $member, ?User $admin = null): void
    {
        $this->update([
            'user_id' => $member->id,
            'sent_at' => now(),
        ]);

        $this->sends()->create([
            'user_id' => $member->id,
            'sent_by' => $admin?->id,
            'sent_at' => now(),
        ]);
    }
}
