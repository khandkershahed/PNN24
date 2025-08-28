<?php

use App\Models\Setting;
use App\Models\Language;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\Cast\String_;

/** format news tags */

function formatTags(array $tags): String
{
    return implode(',', $tags);
}

/** get selected language from session */
// function getLangauge(): string
// {
//     if (session()->has('language')) {
//         return session('language');
//     } else {
//         try {
//             $language = Language::where('default', 1)->first();
//             setLanguage($language->lang);
//             return $language->lang;
//         } catch (\Throwable $th) {
//             $fallbackLang = 'en';
//             setLanguage($fallbackLang);
//             return $fallbackLang;
//         }
//     }
// }

function getLangauge(): string
{
    if (session()->has('language')) {
        return session('language');
    }

    try {
        $language = \App\Models\Language::where('default', 1)->first();

        if ($language && $language->lang) {
            setLanguage($language->lang);
            return $language->lang;
        }
    } catch (\Throwable $th) {
        // Optional: log the exception here
    }

    // Final fallback
    $fallback = 'en';
    setLanguage($fallback);
    return $fallback;
}


/** set language code in session */
function setLanguage(string $code): void
{
    session(['language' => $code]);
}

/** Truncate text */

function truncate(string $text, int $limit = 45): String
{
    return Str::limit($text, $limit, '...');
}

/** Convert a number in K format */

function convertToKFormat(int $number): String
{
    if ($number < 1000) {
        return $number;
    } elseif ($number < 1000000) {
        return round($number / 1000, 1) . 'K';
    } else {
        return round($number / 1000000, 1) . 'M';
    }
}

/** Make Sidebar Active */

function setSidebarActive(array $routes): ?string
{
    foreach ($routes as $route) {
        if (request()->routeIs($route)) {
            return 'active';
        }
    }
    return '';
}

/** get Setting */

function getSetting($key)
{
    $data = Setting::where('key', $key)->first();
    return $data->value;
}

/** check permission */

function canAccess(array $permissions)
{

    $permission = auth()->guard('admin')->user()->hasAnyPermission($permissions);
    $superAdmin = auth()->guard('admin')->user()->hasRole('Super Admin');

    if ($permission || $superAdmin) {
        return true;
    } else {
        return false;
    }
}

/** get admin role */

function getRole()
{
    $role = auth()->guard('admin')->user()->getRoleNames();
    return $role->first();
}

/** check user permission */

function checkPermission(string $permission)
{
    return auth()->guard('admin')->user()->hasPermissionTo($permission);
}

function convertToFullBanglaDate($englishDate)
{
    $bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    $enDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    $months = [
        'January' => 'জানুয়ারি',
        'February' => 'ফেব্রুয়ারি',
        'March' => 'মার্চ',
        'April' => 'এপ্রিল',
        'May' => 'মে',
        'June' => 'জুন',
        'July' => 'জুলাই',
        'August' => 'আগস্ট',
        'September' => 'সেপ্টেম্বর',
        'October' => 'অক্টোবর',
        'November' => 'নভেম্বর',
        'December' => 'ডিসেম্বর',
    ];

    // Convert the input date
    $timestamp = strtotime($englishDate);
    $day = date('d', $timestamp);          // e.g., 28
    $month = date('F', $timestamp);        // e.g., August
    $year = date('Y', $timestamp);         // e.g., 2025

    // Translate to Bangla
    $banglaDay = str_replace($enDigits, $bnDigits, $day);
    $banglaYear = str_replace($enDigits, $bnDigits, $year);
    $banglaMonth = $months[$month] ?? $month;

    return $banglaDay . ' ' . $banglaMonth . ', ' . $banglaYear;
}
