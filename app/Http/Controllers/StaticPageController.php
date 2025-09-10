<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function home()
    {
        return view('static.home');
    }

    public function about()
    {
        return view('static.about');
    }

    public function contact()
    {
        return view('static.contact');
    }

    public function workoutOnline()
    {
        // Recupera tutti i corsi dal catalogo
        $courses = Course::where('is_active', true)->get();
        
        return view('static.workout-online', compact('courses'));
    }
}
