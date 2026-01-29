<?php

// redirectTo calculează URL-ul absolut bazat pe folderul curent și face redirect.
function redirectTo($relativePath)
{
    $base = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    if ($base === '/' || $base === '\\' || $base === '.') {
        $base = '';
    }

    // dacă suntem în rădăcină nu adăugăm dublu slash
    $target = $base === '' ? '/' . ltrim($relativePath, '/') : $base . '/' . ltrim($relativePath, '/');

    header("Location: " . $target);
    exit;
}
