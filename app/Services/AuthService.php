<?php

namespace App\Services;

use App\Mail\ForgotPasswordMail;
use App\Mail\VerifyAccount;
use App\Models\User;
use App\Models\VerifyUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Register a new user.
     */
    public function register(array $data)
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'],
        ]);

        $token = Str::random(60);

        VerifyUser::create([
            'token'   => $token,
            'user_id' => $user->id,
        ]);

        // Email Data
        $email_data = [
            'token'     => $token,
            'email'     => $user->email,
            'user_name' => $user->name,
        ];

        Mail::to($email_data['email'])->queue(new VerifyAccount($email_data));

        return $user;
    }

    /**
     * Authenticate user login.
     */
    public function login($credentials)
    {
        if (! Auth::attempt($credentials)) {
            return ['status' => false, 'error' => 'Invalid login credentials'];
        }

        $user = Auth::user();

        // if ($user->email_verified_at === null) {
        //     Auth::logout();
        //     return ['status' => false, 'error' => 'Please verify your email before logging in.'];
        // }

        return ['status' => true, 'success' => 'Login successful'];
    }

    /**
     * Send password reset link (custom implementation).
     */
    public function forgetPassword(string $email)
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return false;
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'created_at' => Carbon::now(),
        ]);

        $email_data = [
            'token'     => $token,
            'email'     => $email,
            'user_name' => $user->name,
        ];

        Mail::to($email_data['email'])->queue(new ForgotPasswordMail($email_data));

        return true;
    }

    /**
     * Get password reset view.
     */
    public function getResetPasswordView($token)
    {
        $passwordReset = DB::table('password_resets')->where('token', $token)->first();

        if (! $passwordReset) {
            return null;
        }

        return [
            'token' => $token,
            'email' => $passwordReset->email,
        ];
    }

    /**
     * Handle password reset submission.
     */
    public function resetPasswordSubmit(array $data)
    {
        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $data['email'],
                'token' => $data['token'],
            ])
            ->first();

        if (! $updatePassword) {
            return 'Invalid token!';
        }

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return 'User not found.';
        }

        // Update user's password
        $user->password            = Hash::make($data['password']);
        $user->password_changed_at = now();
        $user->save();

        // Remove password reset record
        DB::table('password_resets')->where(['email' => $data['email']])->delete();

        return true;
    }

    public function verifyEmail($token)
    {
        $verifiedUser = VerifyUser::where('token', $token)->first();

        if (! $verifiedUser) {
            return [
                'status'  => false,
                'message' => 'Invalid or expired verification token.',
            ];
        }

        $user = $verifiedUser->user;

        if (! $user->email_verified_at) {
            $user->email_verified_at = Carbon::now();
            $user->save();

            return [
                'status'  => true,
                'message' => 'Your email has been verified.',
            ];
        }

        return [
            'status'  => true,
            'message' => 'Your email has already been verified. Login to continue.',
        ];
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logged out successfully');
    }
}
