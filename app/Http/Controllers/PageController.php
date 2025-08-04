<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function meetings()
    {
        return view('public.meetings');
    }
    
    public function localGuide()
    {
        // In the future, we will fetch blog posts here
        return view('public.local_guide');
    }

    public function contact()
    {
        return view('public.contact');
    }
}