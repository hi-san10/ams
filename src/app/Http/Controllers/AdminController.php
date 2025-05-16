<?php

namespace App\Http\Controllers;

use App\Models\Rest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getLogin(Request $request)
    {
        return view('admin_login');
    }
}
