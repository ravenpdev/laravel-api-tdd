<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\StorePaycheckAction;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Paycheck;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\Response;

final class PaycheckController extends Controller
{
    public function index(Request $request, Employee $employee): Response
    {

        // app(Pipeline::class)
        //     ->send();

        return response()->json(
            data: [
                'paychecks' => $employee->paychecks,
            ],
        )->setStatusCode(Response::HTTP_OK);
    }

    public function show(Employee $employee, Paycheck $paycheck): Response
    {
        return response()->json([
            'paycheck' => $paycheck,
        ])->setStatusCode(Response::HTTP_OK);
    }

    public function store(StorePaycheckAction $storePaycheckAction): Response
    {
        $storePaycheckAction->execute();

        return response()->noContent();
    }
}
