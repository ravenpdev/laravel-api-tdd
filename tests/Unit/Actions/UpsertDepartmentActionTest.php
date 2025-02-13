<?php

declare(strict_types=1);

use App\Actions\UpsertDepartmentAction;
use App\Models\Department;

it('should create a department', function (string $name, string $description) {
    $department = new Department();
    $action = app(UpsertDepartmentAction::class);

    $department = $action->execute($department, $name, $description);

    expect($department->name)->toBe($name)
        ->and($department->description)->toBe($description);
})->with([
    [
        'name' => 'Development', 'description' => 'Software Developement',
    ],
]);

it('should update a department', function (string $name, string $description) {
    $department = Department::factory()->create();
    $action = app(UpsertDepartmentAction::class);

    $updatedDepartment = $action->execute($department, $name, $description);

    expect($updatedDepartment->id)->toBe($department->id)
        ->and($updatedDepartment->name)->toBe($name)
        ->and($updatedDepartment->description)->toBe($description);
})->with([
    [
        'name' => 'Development', 'description' => 'Software development department updated!',
    ],
]);
