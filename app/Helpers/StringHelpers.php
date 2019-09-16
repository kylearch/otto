<?php

if (!function_exists('money_cell')) {
    function money_cell($amount, $showCents = true, $showCurrency = false)
    {
        $cents    = $showCents ? 2 : 0;
        $currency = $showCurrency ? '' : '!';

        return money_format("%({$currency}.{$cents}n", $amount);
    }
}
