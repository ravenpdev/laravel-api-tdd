<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\UpsertDepartmentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class DepartmentController extends Controller
{
    public function index(Request $request): Response
    {
        $departments = Department::query()
            ->when($request->has('filter'), function (Builder $query) use ($request) {
                $search = $request->string('filter')->toString();

                return $query->where('name', 'like', "%$search%")
                    ->orWhereLike('description', "%$search%");
            })
            ->when($request->has('sort'), function (Builder $query) use ($request) {
                $sort = $request->string('sort')->toString();
                $operand = mb_substr($sort, 0, 1);
                $column = mb_substr($sort, 1);

                if ($operand === '-') {
                    return $query->orderByDesc($column);
                }

                return $query->orderBy($column);
            })
            ->simplePaginate(perPage: $request->has('perPage') ? $request->integer('perPage') : 10);

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
