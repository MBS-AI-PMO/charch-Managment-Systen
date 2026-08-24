<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

it('updates the profile name, phone and bio', function () {
    $u = makeMember(['name' => 'Old Name', 'email' => 'm@example.test']);

    $this->actingAs($u, 'web')
        ->put(route('member.profile.update'), [
            'name' => 'New Name',
            'email' => 'm@example.test',
            'phone' => '+1 555 0101',
            'bio' => 'Loves the choir.',
        ])->assertRedirect();

    $fresh = $u->fresh();
    expect($fresh->name)->toBe('New Name')
        ->and($fresh->phone)->toBe('+1 555 0101')
        ->and($fresh->bio)->toBe('Loves the choir.');
});

it('clears email_verified_at when the email changes and redirects to the verification notice', function () {
    $u = makeMember(['email' => 'old@example.test']);

    $resp = $this->actingAs($u, 'web')
        ->put(route('member.profile.update'), [
            'name' => $u->name,
            'email' => 'new@example.test',
        ]);

    $resp->assertRedirect(route('verification.notice'));

    $fresh = $u->fresh();
    expect($fresh->email)->toBe('new@example.test')
        ->and($fresh->email_verified_at)->toBeNull();
});

it('changes the password when current_password is correct', function () {
    $u = makeMember(['password' => 'Old-Pass-Long-1!']);

    $this->actingAs($u, 'web')
        ->put(route('member.profile.password'), [
            'current_password' => 'Old-Pass-Long-1!',
            'password' => 'New-Strong-Pass-9!',
            'password_confirmation' => 'New-Strong-Pass-9!',
        ])->assertRedirect();

    expect(Hash::check('New-Strong-Pass-9!', $u->fresh()->password))->toBeTrue();
});

it('rejects a password change when current_password is wrong', function () {
    $u = makeMember(['password' => 'Old-Pass-Long-1!']);

    $this->actingAs($u, 'web')
        ->put(route('member.profile.password'), [
            'current_password' => 'wrong-pass!',
            'password' => 'New-Strong-Pass-9!',
            'password_confirmation' => 'New-Strong-Pass-9!',
        ])->assertSessionHasErrors('current_password');

    expect(Hash::check('Old-Pass-Long-1!', $u->fresh()->password))->toBeTrue();
});

it('stores a profile photo on the public disk', function () {
    Storage::fake('public');
    $u = makeMember(['email' => 'm@example.test']);
    $photo = UploadedFile::fake()->image('me.jpg', 800, 600);

    $this->actingAs($u, 'web')
        ->put(route('member.profile.update'), [
            'name' => $u->name,
            'email' => $u->email,
            'avatar' => $photo,
        ])->assertRedirect();

    $fresh = $u->fresh();
    expect($fresh->avatar_path)->not->toBeNull()
        ->and($fresh->avatar_path)->toStartWith('uploads/avatars/');

    Storage::disk('public')->assertExists($fresh->avatar_path);
});

it('removes a profile photo when requested', function () {
    Storage::fake('public');
    Storage::disk('public')->put('uploads/avatars/old.jpg', 'fake-bytes');
    $u = makeMember([
        'email' => 'm@example.test',
        'avatar_path' => 'uploads/avatars/old.jpg',
    ]);

    $this->actingAs($u, 'web')
        ->put(route('member.profile.update'), [
            'name' => $u->name,
            'email' => $u->email,
            'remove_avatar' => '1',
        ])->assertRedirect();

    expect($u->fresh()->avatar_path)->toBeNull();
    Storage::disk('public')->assertMissing('uploads/avatars/old.jpg');
});

it('rejects a non-image file as a profile photo', function () {
    Storage::fake('public');
    $u = makeMember(['email' => 'm@example.test']);
    $evil = UploadedFile::fake()->createWithContent('evil.php', '<?php echo "pwned";');

    $this->actingAs($u, 'web')
        ->from(route('member.profile.edit'))
        ->put(route('member.profile.update'), [
            'name' => $u->name,
            'email' => $u->email,
            'avatar' => $evil,
        ])->assertSessionHasErrors('avatar');

    expect($u->fresh()->avatar_path)->toBeNull();
});
