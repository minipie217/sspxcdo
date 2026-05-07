<?php


if (! function_exists('peso')) {
    function peso($amount): string
    {
        return '₱' . number_format((float) $amount, 2);
    }
}