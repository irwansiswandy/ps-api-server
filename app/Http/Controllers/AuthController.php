<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\AccessToken;
use App\Models\ActivationToken;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    protected function handleActivationTokenUsed($activation_token, $user_id, $guard)
    {
        $activation_token = ActivationToken::where([
            'value' => $activation_token,
            'activation_tokenable_id' => $user_id,
            'activation_tokenable_type' => getter()->userType($guard)
        ])->first();

        $activation_token->value = null;
        $activation_token->save();
    }

    protected function credentialsIsValid(Request $request, User $user)
    {
        if (!Hash::check($request->input('password'), $user->password))
        {
            return false;
        }

        return true;
    }

    protected function emailIsActive(User $user)
    {
        if (!$user->active)
        {
            return false;
        }
        
        return true;
    }

    protected function generateAccessToken(Request $request, User $user, $user_type, $length = 64)
    {
        $browser = new \foroco\BrowserDetection();

        // dd($browser->getDevice($request->server('HTTP_USER_AGENT')));

        return AccessToken::create([
            'value' => Str::random($length),
            'session_key' => $this->generateSessionKey(),
            'access_tokenable_id' => $user->id,
            'access_tokenable_type' => $user_type,
            'os' => $browser->getOS($request->server('HTTP_USER_AGENT'))['os_name'],
            'browser' => $browser->getBrowser($request->server('HTTP_USER_AGENT'))['browser_name'],
            'device' => $browser->getDevice($request->server('HTTP_USER_AGENT'))['device_type']
        ]);
    }

    protected function generateSessionKey($length = 32)
    {
        return Str::random($length);
    }

    protected function generateActivationToken(User $user, $user_type, $activation_type = 'web', $length = 64)
    {
        return ActivationToken::create([
            'value' => Str::random($length),
            'type' => $activation_type,
            'activation_tokenable_id' => $user->id,
            'activation_tokenable_type' => $user_type
        ]);
    }

    protected function generateActivationURL(User $user, ActivationToken $activation_token)
    {
        return env('APP_URL') . '/' . 'activate' . '/' . $activation_token->value . '/' . $user->id . '/' . 'user';
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        $user = User::create($request->all());

        $activation_token = $this->generateActivationToken($user, 'App\Models\User');
        
        return response(formatter()->response('Account registration succeed, please activate your account to enable login', ['activation_url' => $this->generateActivationURL($user, $activation_token)]), 200);
    }

    public function activateAccount($activation_token, $user_id, $guard)
    {
        $user = User::whereHas('activation_token', function ($activation_token_query) use ($activation_token, $user_id, $guard) {
            $activation_token_query->where([
                'value' => $activation_token,
                'activation_tokenable_id' => $user_id,
                'activation_tokenable_type' => getter()->userType($guard)
            ]);
        })->first();

        if (!$user)
        {
            return response(formatter()->response('Activation not allowed, invalid token'), 422);
        }
        else if (!$user->activation_token->value)
        {
            return response(formatter()->response('Activation token has been used, account should have been active'), 422);
        }   

        $user->active = true;
        $user->save();

        $this->handleActivationTokenUsed($activation_token, $user_id, $guard);

        return response(formatter()->response('Activation success', $user), 200);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user)
        {
            return response(formatter()->response('E-mail not found, account not exists'), 401);
        }

        if (!$this->credentialsIsValid($request, $user))
        {
            return response(formatter()->response('Incorrect password'), 403);
        }
        
        if (!$this->emailIsActive($user))
        {
            return response(formatter()->response('Account not activated, please activate account to enable login'), 403);
        }

        $access_token = $this->generateAccessToken($request, $user, 'App\Models\User');

        return response(formatter()->response('Login success', formatter()->accessToken($access_token)), 200);
    }
}