<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteGoogleController extends Controller
{
    public function __invoke()
    {
        $socialiteUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate([
            'email' => $socialiteUser->email,
        ], [
            'name' => $socialiteUser->name,
            'email' => $socialiteUser->email,
            'google_id' => $socialiteUser->id,
            'avatar' => $socialiteUser->avatar,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
