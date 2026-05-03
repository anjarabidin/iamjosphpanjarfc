<?php

namespace App\Traits;

trait RequestLocale
{
    protected function shouldUseEnglish(): bool
    {
        if (app()->runningInConsole()) return false;

        $req = request();

        // Hanya aktif untuk route /api/* (atau request yang benar-benar API)
        if (! $req->is('api/*')) {
            return false;
        }

        $lang = $req->query('lang')
            ?? $req->header('X-Lang')
            ?? $req->header('Accept-Language');

        $lang = strtolower((string) $lang);
        return $lang === 'en' || str_starts_with($lang, 'en-');
    }

    protected function localizeAttr(string $base, $fallback, array $attributes)
    {
        if ($this->shouldUseEnglish()) {
            $en = $attributes[$base . '_en'] ?? null;
            if ($en !== null && $en !== '') return $en;
        }
        return $fallback;
    }
}
