<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
   public function homePage()
    {
        return view('pages.home');
    }
    

}