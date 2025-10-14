<?php
namespace App\Helper;

class Redirect
{
    public static function to(string $url): void
    {
        header("Location: $url");
        exit;
    }
}
