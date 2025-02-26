<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Pipes\SortBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

it('should sort by id in ascending roder', function () {
    Employee::factory()
        ->count(50)
        ->create();
    // ->fresh();

    $employees = app(Pipeline::class)
        ->send(Employee::query())
        ->through([
            new SortBy(keyword: 'id'),
        ])
        ->then(function (Builder $builder) {
            return $builder->get();
        });

    expect($employees[0])->id->toBe(Employee::query()->first()->id);
});
