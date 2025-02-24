<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Concerns\PaymentType;
use App\ValueObjects\Money;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read string $id
 * @property-read string $department_id
 * @property-read string $first_name
 * @property-read string $last_name
 * @property-read string $job_title
 * @property-read PaymentType $payment_type
 * @property-read int $salary
 * @property-read int $hourly_rate
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 */
final class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'departmentId' => $this->department_id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'jobTitle' => $this->job_title,
            'paymentType' => [
                'type' => $this->payment_type->type(),
                'amount' => Money::from($this->payment_type->amount())->toArray(),
            ],
            'salary' => $this->salary,
            'hourlyRate' => $this->hourly_rate,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
