<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Set API response language by Accept-Language header.
     * Unsupported language or no header keeps config('app.locale').
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->preferredLocale($request, config('app.available_locales', ['en', 'ru']));

        if ($locale) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    /**
     * First supported language from header, by q-weight. "ru-RU" matches "ru".
     * Not getPreferredLanguage(): it returns the first available locale when nothing matches.
     */
    private function preferredLocale(Request $request, array $available): ?string
    {
        foreach ($request->getLanguages() as $language) {
            $primary = strtolower(explode('_', $language)[0]);

            if (in_array($primary, $available, true)) {
                return $primary;
            }
        }

        return null;
    }
}
