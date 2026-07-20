<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class LangController extends Controller
{
    public function __construct()
    {
        if (!isset($_COOKIE['section_id']) && !isset($_COOKIE['address_name'])) {
            \Redirect::to('set-location')->send();
        }
    }

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

    public function change(Request $request)
    {
        $locale = $this->normalizeLocale($request->lang);
        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }
}
