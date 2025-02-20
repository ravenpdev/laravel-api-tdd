<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read string $employee_id
 * @property-read int $minutes
 * @property-read CarbonImmutable $started_at
 * @property-read CarbonImmutable $stopped_at
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 */
final class Timelog extends Model
{
    /** @use HasFactory<\Database\Factories\TimelogFactory> */
    use HasFactory, HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
