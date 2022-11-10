<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function initGoogleLogin(){
        return Socialite::driver('google')->redirect();
    }
    public function googleLoginCallback(){
        $user = Socialite::driver('google')->stateless()->user();

        $this->loginUserManually($user);
    }

    public function initFacebookLogin(){
        return Socialite::driver('facebook')->redirect();
    }
    public function facebookLoginCallback(){
        $user = Socialite::driver('facebook')->stateless()->user();
        $this->loginUserManually($user);
    }

    public function initTwitterLogin(){
        return Socialite::driver('twitter')->redirect();
    }
    public function twitterLoginCallback(){
        $user = Socialite::driver('twitter')->stateless()->user();
        $this->loginUserManually($user);
    }

    public function loginUserManually($user){
        $existingUser = User::query()->firstOrCreate([
            'email' => $user->getEmail()
        ],[
            'name' => $user->getName(),
            'password' => Hash::make(Str::random(10))
        ]);

        Auth::login($existingUser, true);



        return route('file.index');
    }
}
