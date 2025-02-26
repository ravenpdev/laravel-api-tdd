<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Pipes\FilterBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

it('should filter', function () {
    Employee::factory(count: 10)->create();
    Employee::factory()
        ->count(2)
        ->sequence(
            [
                'first_name' => 'kristine',
                'last_name' => 'paragas',
            ],
            [
                'first_name' => 'raven',
                'last_name' => 'paragas',
            ]
        )
        ->create();

    $filtered = app(Pipeline::class)
        ->send(Employee::class)
        ->through([
            new FilterBy(fields: ['first_name', 'last_name'], filters: ['last_name' => 'paragas']),
        ])->then(function (Builder $builder) {
            return $builder->get();
        });

    expect($filtered)->toHaveCount(2);
});
