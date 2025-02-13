<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Department;

final class UpsertDepartmentAction
{
    public function execute(Department $department, string $name, string $description): Department
    {
        $department = $department->query()->updateOrCreate(
            attributes: [
                'id' => $department->id,
            ],
            values: [
                'name' => $name,
                'description' => $description,
            ]
        );

        return $department;
    }
}
