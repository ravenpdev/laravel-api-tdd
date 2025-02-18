<?php

declare(strict_types=1);

namespace App\Models;

use App\enums\PaymentTypes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read string $department_id
 * @property-read string $first_name
 * @property-read string $last_name
 * @property-read string $last_name
 * @property-read string $job_title
 * @property-read string $payment_type
 * @property-read int $salary
 * @property-read int $hourly_rate
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 */
final class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory, HasUlids;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    protected function casts(): array
    {
        return [
            'payment_type' => PaymentTypes::class,
        ];
    }
}
