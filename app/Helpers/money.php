<?php

if (! function_exists('peso')) {
    function peso(int|float|string|null $amount): string
    {
        return '₱' . number_format((float) ($amount ?? 0), 2);
    }
}