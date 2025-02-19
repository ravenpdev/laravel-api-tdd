<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PaycheckController extends Controller
{
    public function index(Request $request, Employee $employee): Response
    {
        return response()->json(
            data: [
                'paychecks' => $employee->paychecks,
            ],
        )->setStatusCode(Response::HTTP_OK);
    }
}
