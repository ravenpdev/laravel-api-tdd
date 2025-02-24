<?php

declare(strict_types=1);

namespace App\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;

final class SortBy
{
    /**
     * Create a new class instance.
     */
    public function __construct(public readonly string $keyword)
    {
        //
    }

    public function handle(Builder $queryBuilder, Closure $next)
    {
        if (empty($this->keyword)) {
            return $next($queryBuilder);
        }

        if ($this->keyword[0] === '-') {
            return $next($queryBuilder->orderByDesc(mb_substr($this->keyword, 1)));
        }

        if ($this->keyword[0] === '+') {
            return $next($queryBuilder->orderBy(mb_substr($this->keyword, 1)));
        }

        return $next($queryBuilder->orderBy($this->keyword));
    }
}
