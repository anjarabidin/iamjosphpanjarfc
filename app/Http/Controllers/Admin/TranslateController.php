<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
        ]);
        $data = GoogleTranslate::trans($request->text, 'en');
        return response()->json($data);
    }

    public function translatePost(Request $request)
    {
        $data = GoogleTranslate::trans($request->text, 'en');
        return response()->json($data);
    }
}
