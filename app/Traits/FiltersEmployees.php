<?php

namespace App\Traits;

trait FiltersEmployees
{
    public function scopeFilterEmployees($query, array $filtered)
    {
        if (empty($filtered)) {
            return $query;
        }

        return $query->whereIn('user_id', $filtered);
    }
}
