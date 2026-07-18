<?php

namespace App\Http\Middleware;

use Closure;
use App;

class LanguageManager
{
    /**
     * Normaliza o slug do painel (pt_br / pt_BR / pt-br) para a pasta resources/lang/pt_br.
     */
    private function normalizeLocale(?string $locale): string
    {
        if ($locale === null || $locale === '') {
            return config('app.locale', 'pt_br');
        }

        $normalized = strtolower(str_replace('-', '_', $locale));

        if ($normalized === 'pt_br' || $normalized === 'ptbr' || $normalized === 'pt') {
            return 'pt_br';
        }

        return $locale;
    }

    public function handle($request, Closure $next)
    {
        if (session()->has('locale')) {
            App::setLocale($this->normalizeLocale(session()->get('locale')));
        } else {
            App::setLocale($this->normalizeLocale(config('app.locale', 'pt_br')));
        }

        return $next($request);
    }
}
