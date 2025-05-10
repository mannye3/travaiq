<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordSubmitRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showForgetPasswordForm()
    {
        return view('auth.resetPassword');
    }

    public function loginPost(LoginRequest $request)
    {
        //  return "here";
        $loginResponse = $this->authService->login($request->only('email', 'password'));

        if ($loginResponse['status']) {
            return redirect()->route('dashboard');
        }

        return redirect()->back()->with('error', $loginResponse['error']);
    }

    /**
     * Handle forgot password request.
     */
    public function userForgetpassword(ForgetPasswordRequest $request)
    {

        return $success = $this->authService->forgetPassword($request->email);

        if (! $success) {
            return back()->with('error', 'Email not found.');
        }

        return back()->with('success', 'We have e-mailed your password reset link!');
    }

    /**
     * Show reset password view.
     */
    public function resetpassword($token)
    {
        $data = $this->authService->getResetPasswordView($token);

        if (! $data) {
            return redirect()->route('password.request')->with('error', 'Invalid password reset token.');
        }

        return view('auth.passwords.reset', $data);
    }

    /**
     * Handle password reset submission.
     */
    public function resetpasswordsubmit(ResetPasswordSubmitRequest $request)
    {
        $result = $this->authService->resetPasswordSubmit($request->validated());

        if ($result !== true) {
            return back()->withInput()->with('error', $result);
        }

        return redirect('/login')->with('success', 'Your password has been changed!');
    }

    public function verifyEmail($token)
    {
        $result = $this->authService->verifyEmail($token);

        return redirect(route('login'))->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
}
