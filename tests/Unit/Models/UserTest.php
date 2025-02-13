<?php

declare(strict_types=1);

use App\Models\User;

test('array', function () {
    $user = User::factory()->create();

    expect(array_keys($user->toArray()))->toEqualCanonicalizing([
        'id',
        'name',
        'email',
        'email_verified_at',
        'created_at',
        'updated_at',
    ]);
});
