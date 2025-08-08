<?php

use Dotenv\Dotenv;

if (!function_exists('loadEnv')) {
    function loadEnv(?string $path): void
    {
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
    }
}

loadEnv(__DIR__);
