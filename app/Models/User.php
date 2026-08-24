<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'avatar_path',
        'two_factor_enabled',
        'password_change_required',
        'last_login_at',
        'phone',
        'bio',
        'email_reminder_event_24h',
        'email_weekly_digest',
        'email_admin_daily_digest',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'password_change_required' => 'boolean',
            'last_login_at' => 'datetime',
            'email_reminder_event_24h' => 'boolean',
            'email_weekly_digest' => 'boolean',
            'email_admin_daily_digest' => 'boolean',
        ];
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? site_storage_url($this->avatar_path) : null;
    }

    public function replaceAvatar(UploadedFile $file): void
    {
        $this->deleteStoredAvatar();
        $this->avatar_path = $file->store('uploads/avatars', 'public');
    }

    public function clearAvatar(): void
    {
        $this->deleteStoredAvatar();
        $this->avatar_path = null;
    }

    protected function deleteStoredAvatar(): void
    {
        $path = ltrim((string) $this->avatar_path, '/');
        if ($path === '' || ! str_starts_with($path, 'uploads/avatars/')) {
            return;
        }
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
