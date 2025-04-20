<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificationMail;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $email = $request->email;

        User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password)
        ]);

        Mail::to($email)->send(new CertificationMail($email));

        return view('authInduction', compact('email'));
    }

    public function verification(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at)
        {
            return redirect('login')->with('message', 'メールによる認証はお済みです');
        }

        User::where('email', $request->email)
            ->update(['email_verified_at' => CarbonImmutable::today()]);

        return redirect('login')->with('message', 'メールによる認証が完了しました');
    }

    public function resend(Request $request)
    {
        $email = $request->email;
        Mail::to($email)->send(new CertificationMail($email));

        return view('authInduction', compact('email'));
    }
}
