<?php

namespace App\Support;

use InvalidArgumentException;

class CertificateTemplates
{
    /**
     * Register certificate Blade templates here.
     * Add a new Blade under resources/views/certificates/templates/
     * then add one entry below — that is all.
     *
     * @return array<string, array{label: string, view: string, description?: string}>
     */
    public static function all(): array
    {
        return [
            'appreciation' => [
                'label' => 'Certificate of Appreciation',
                'view' => 'certificates.templates.appreciation',
                'description' => 'Honour someone for service and dedication.',
            ],
            'completion' => [
                'label' => 'Certificate of Completion',
                'view' => 'certificates.templates.completion',
                'description' => 'Mark the completion of a course or programme.',
            ],
            'participation' => [
                'label' => 'Certificate of Participation',
                'view' => 'certificates.templates.participation',
                'description' => 'Recognise participation in an event or ministry.',
            ],
        ];
    }

    public static function keys(): array
    {
        return array_keys(self::all());
    }

    public static function options(): array
    {
        return collect(self::all())
            ->mapWithKeys(fn ($meta, $key) => [$key => $meta['label']])
            ->all();
    }

    public static function label(string $key): string
    {
        return self::all()[$key]['label'] ?? ucfirst(str_replace('-', ' ', $key));
    }

    public static function view(string $key): string
    {
        $meta = self::all()[$key] ?? null;
        if (! $meta) {
            throw new InvalidArgumentException("Unknown certificate template [{$key}].");
        }

        return $meta['view'];
    }

    public static function exists(string $key): bool
    {
        return array_key_exists($key, self::all());
    }
}
