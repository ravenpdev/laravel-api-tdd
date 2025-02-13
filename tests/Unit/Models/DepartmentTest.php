<?php

declare(strict_types=1);

use App\Models\Department;

test('to array', function () {
    $deparment = Department::factory()->create();

    expect(array_keys($deparment->toArray()))->toEqualCanonicalizing([
        'id',
        'name',
        'description',
        'created_at',
        'updated_at',
    ]);
});
