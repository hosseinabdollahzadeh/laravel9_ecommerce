<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\OTPSmsNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

//    login with otp
//    public function login(Request $request)
//    {
//        if ($request->method() == 'GET') {
//            return view('auth.login');
//        }
//        $request->validate([
//            'cellphone' => 'required|iran_mobile'
//        ]);
//
//        try {
//            $user = User::query()->where('cellphone', $request->cellphone)->first();
//            $OTPCode = mt_rand(100000, 999999);
//            $loginToken = Hash::make(Str::random());
//            if ($user) {
//                $user->update([
//                    'otp' => $OTPCode,
//                    'login_token' => $loginToken
//                ]);
//            } else {
//                $user = User::create([
//                    'cellphone' => $request->cellphone,
//                    'otp' => $OTPCode,
//                    'login_token' => $loginToken
//                ]);
//            }
//            $user->notify(new OTPSmsNotification($OTPCode));
//            return response(['login_token' => $loginToken], 200);
//        } catch (\Exception $exception) {
//            return response(['errors' => $exception->getMessage()], 422);
//        }
//    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }


    public function handleProviderCallback($provider)
    {
        try {
            $socialite_user = Socialite::driver($provider)->user();
        } catch (\Exception $ex) {
            return redirect()->route('login');
        }

        $user = User::where('email' , $socialite_user->getEmail())->first();
        if(!$user){
            $user = User::create([
                'name' => $socialite_user->getName(),
                'provider_name' => $provider,
                'avatar' => $socialite_user->getAvatar(),
                'email' => $socialite_user->getEmail(),
                'password' => Hash::make($socialite_user->getId()),
                'email_verified_at' => Carbon::now()
            ]);
        }

        auth()->login($user , $remember = true);
        alert()->success('ورود شما موفقیت آمیز بود.','تشکر')->persistent('حله');

        return redirect()->route('home.index');
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }
    public function checkOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'login_token' => 'required'
        ]);
        try {
            $user = User::query()->where('login_token', $request->login_token)->firstOrFail();
            if ($user->otp == $request->otp) {
                auth()->login($user, $remember = true);
                return response(['ورود با موفقیت انجام شد.'], 200);
            } else {
                return response(['errors' => ['otp' => ['کد تأییدیه نادرست است!']]], 422);
            }
        } catch (\Exception $exception) {
            return response(['errors' => $exception->getMessage()], 422);
        }
    }

    public function resendOtp(Request $request)
    {

        $request->validate([
            'login_token' => 'required'
        ]);

        try {
            $user = User::query()->where('login_token', $request->login_token)->firstOrFail();
            $OTPCode = mt_rand(100000, 999999);
            $loginToken = Hash::make(Str::random());
            $user->update([
                'otp' => $OTPCode,
                'login_token' => $loginToken
            ]);

            $user->notify(new OTPSmsNotification($OTPCode));
            return response(['login_token' => $loginToken], 200);
        } catch (\Exception $exception) {
            return response(['errors' => $exception->getMessage()], 422);
        }
    }
}
