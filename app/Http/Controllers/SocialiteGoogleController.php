<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteGoogleController extends Controller
{
    public function __invoke()
    {
        $user = Socialite::driver('google')->user();

        $user = User::updateOrCreate([
            'email' => $user->email,
        ], [
            'name' => $user->name,
            'email' => $user->email,
            'google_id' => $user->id,
            'avatar' => $user->avatar,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
