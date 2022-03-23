<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Repositories\PostRepository;
use App\Http\Repositories\RecruitRepository;
use App\Http\Repositories\TimeCardRepository;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return view('home.index');
    }
}
