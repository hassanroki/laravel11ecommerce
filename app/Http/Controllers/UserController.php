<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // Index
    public function index(){
        return view('user.index');
    }
}
