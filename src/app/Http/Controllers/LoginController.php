<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificationMail;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function getLogin()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function store(RegisterRequest $request)
    {
        $email = $request->email;

        User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password)
        ]);

        Mail::to($email)->send(new CertificationMail($email));

        return view('auth_induction', compact('email'));
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

        return view('auth_induction', compact('email'));
    }

    public function postLogin(LoginRequest $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (is_null($user))
        {
            return back()->with('message', 'ログイン情報が登録されていません');
        }

        if (!Hash::check($request->password, $user->password))
        {
            return back()->with('message', 'パスワードが違います');

        } elseif (is_null($user->email_verified_at)) {
            return view('auth_induction', compact('email'));
        }

        $credentials = ([
            'email' => $email,
            'password' => $request->password
        ]);

        Auth::attempt($credentials);
        $request->session()->regenerate();

        return redirect('attendances.attendance');

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('attendances.attendance');
    }
}
