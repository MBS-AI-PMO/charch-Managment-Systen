<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('rejects a php file uploaded to the media library', function () {
    Storage::fake('public');

    $admin = makeAdmin();

    $evil = UploadedFile::fake()->createWithContent('evil.php', '<?php echo "pwned";');

    $resp = $this->actingAs($admin, 'admin')
        ->post(route('admin.media.store'), [
            'file' => $evil,
        ]);

    expect($resp->getStatusCode())->toBe(422);

    Storage::disk('public')->assertDirectoryEmpty('uploads');
});
