<?php

if (! function_exists('array_merge_when')) {

    function array_merge_when(array $base, $merge, $condition = false)
    {
        if (!$condition) {
            return $base;
        }

        if ($merge instanceof Closure) {
            $merge = call_user_func($merge);
        }
        return array_merge($base, $merge);
    }
}
