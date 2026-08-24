<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use App\Support\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChurchBranch extends Model
{
    use HasFactory, LogsModelActivity, Sluggable;

    protected $slugSource = 'name';

    protected $guarded = [];

    protected $casts = [
        'is_published' => 'bool',
        'sort_order' => 'integer',
    ];

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function displayPhone(): ?string
    {
        return trim((string) ($this->phone ?: settings('contact.phone'))) ?: null;
    }

    public function displayEmail(): ?string
    {
        return trim((string) ($this->email ?: settings('contact.email'))) ?: null;
    }

    /**
     * @return list<string>
     */
    public function lineItems(?string $field): array
    {
        if ($field === null || trim($field) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $field) ?: [])));
    }

    /**
     * @return list<string>
     */
    public function aboutParagraphs(): array
    {
        if ($this->about === null || trim($this->about) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split("/\n\s*\n/", $this->about) ?: [])));
    }

    /**
     * @return list<string>
     */
    public function expectItems(): array
    {
        return $this->lineItems($this->expect);
    }

    /**
     * @return list<string>
     */
    public function ministryItems(): array
    {
        return $this->lineItems($this->ministries);
    }
}
