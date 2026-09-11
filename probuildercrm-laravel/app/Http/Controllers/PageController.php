<?php

namespace App\Http\Controllers;

use App\Models\Faq;

class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function features()
    {
        return view('features');
    }

    public function about()
    {
        return view('about');
    }

    public function faq()
    {
        $faqs = Faq::orderBy('sort_order')->get();

        return view('faq', compact('faqs'));
    }

    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    public function termsOfService()
    {
        return view('terms-of-service');
    }

    public function dataSecurity()
    {
        return view('data-security');
    }
}
