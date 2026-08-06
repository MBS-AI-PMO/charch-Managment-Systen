<?php

use App\Services\SettingsRepository;

it('updates the brand primary colour', function () {
    $this->actingAs(makeAdmin(), 'admin')
        ->post(route('admin.settings.update'), [
            'brand' => [
                'name' => 'Test Church',
                'color' => [
                    'primary' => '#ff0044',
                    'secondary' => '#0099ff',
                ],
            ],
        ])->assertRedirect();

    $repo = app(SettingsRepository::class);
    $repo->flush();
    expect($repo->get('brand.color.primary'))->toBe('#ff0044')
        ->and($repo->get('brand.name'))->toBe('Test Church');
});

it('rejects an invalid colour format', function () {
    $this->actingAs(makeAdmin(), 'admin')
        ->from(route('admin.settings.index'))
        ->post(route('admin.settings.update'), [
            'brand' => [
                'color' => ['primary' => 'not-a-hex'],
            ],
        ])->assertSessionHasErrors('brand.color.primary');
});
