<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\UpsertDepartmentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Models\Department;
use Symfony\Component\HttpFoundation\Response;

final class DepartmentController extends Controller
{
    public function index(): Response
    {
        $departments = Department::query()->get();

        return response()->json(
            data: [
                'departments' => $departments,
            ],
            status: Response::HTTP_OK
        );
    }

    public function store(StoreDepartmentRequest $request, UpsertDepartmentAction $upsertDepartmentAction): Response
    {
        $department = $upsertDepartmentAction->execute(
            department: new Department(),
            name: $request->string('name')->toString(),
            description: $request->string('description')->toString()
        );

        return response()->json(
            data: ['department' => $department],
            status: Response::HTTP_CREATED
        );
    }

    public function show(Department $department): Response
    {
        return response()->json(
            data: ['department' => $department],
            status: Response::HTTP_OK,
        );
    }

    public function update(StoreDepartmentRequest $request, Department $department, UpsertDepartmentAction $upsertDepartmentAction): Response
    {
        $upsertDepartmentAction->execute(
            $department,
            $request->string('name')->toString(),
            $request->string('description')->toString()
        );

        return response()->noContent();
    }

    public function destroy(Department $department): Response
    {
        $department->delete();

        return response()->noContent();
    }
}
