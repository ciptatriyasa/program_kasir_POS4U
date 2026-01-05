<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('rupiah_format')) {
    /**
     * Memformat angka menjadi format mata uang Rupiah.
     * Contoh: 1500000.50 -> Rp 1.500.000
     */
    function rupiah_format($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}