<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\UpsertDepartmentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Pipes\FilterBy;
use App\Pipes\SortBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\Response;

final class DepartmentController extends Controller
{
    public function index(Request $request): Response
    {
        $departments = app(Pipeline::class)
            ->send(Department::query())
            ->through([
                new FilterBy(
                    fields: ['name', 'description'],
                    filters: $request->array('filter')
                ),
                new SortBy(keyword: $request->string('sort')->toString()),
            ])
            ->then(function (Builder $builder) use ($request) {
                return $builder->simplePaginate(
                    perPage: $request->integer('page.size', 10),
                    page: $request->integer('page.number', 1)
                );
            });

        return response()->json(
            data: [
                'departments' => DepartmentResource::collection($departments),
                'meta' => [
                    'perPage' => $departments->perPage(),
                    'currentPage' => $departments->currentPage(),
                    'nextPageUrl' => $departments->nextPageUrl(),
                    'prevPageUrl' => $departments->previousPageUrl(),
                ],
            ],
            status: Response::HTTP_OK
        );
    }

    public function store(UpsertDepartmentRequest $request, UpsertDepartmentAction $upsertDepartmentAction): Response
    {
        $department = $upsertDepartmentAction->execute(
            department: new Department(),
            name: $request->string('name')->toString(),
            description: $request->string('description')->toString()
        );

        return response()->json(
            data: ['department' => new DepartmentResource($department)],
            status: Response::HTTP_CREATED
        );
    }

    public function show(Department $department): Response
    {
        return response()->json(
            data: ['department' => new DepartmentResource($department)],
            status: Response::HTTP_OK,
        );
    }

    public function update(UpsertDepartmentRequest $request, Department $department, UpsertDepartmentAction $upsertDepartmentAction): Response
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
