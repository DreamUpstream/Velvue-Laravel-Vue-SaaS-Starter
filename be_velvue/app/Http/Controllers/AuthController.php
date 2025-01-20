<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLoginLog;
use App\Models\UserProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->ulid = Str::ulid()->toBase32();
        $user->save();

        event(new Registered($user));

        return response()->json([
            'success' => true,
        ], 201);
    }

    /**
     * Redirect to provider for authentication
     */
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from provider
     * @throws \Exception
     */
    public function callback(Request $request, string $provider): View
    {
        $oAuthUser = Socialite::driver($provider)->user();

        if (!$oAuthUser?->token) {
            return view('oauth', [
                'message' => [
                    'success' => false,
                    'message' => __('Unable to authenticate with :provider', ['provider' => $provider]),
                ],
            ]);
        }

        $userProvider = UserProvider::select('id', 'user_id')
            ->where('name', $provider)
            ->where('provider_id', $oAuthUser->id)
            ->first();

        if (!$userProvider) {
            if (User::where('email', $oAuthUser->email)->exists()) {
                return view('oauth', [
                    'message' => [
                        'success' => false,
                        'message' => __('Unable to authenticate with :provider. User with email :email already exists. To connect a new service to your account, you can go to your account settings and go through the process of linking your account.', [
                            'provider' => $provider,
                            'email' => $oAuthUser->email,
                        ]),
                    ],
                ]);
            }

            $user = new User();
            $user->ulid = Str::ulid()->toBase32();
            $user->avatar = $oAuthUser->picture ?? $oAuthUser->avatar_original ?? $oAuthUser->avatar;
            $user->name = $oAuthUser->name;
            $user->email = $oAuthUser->email;
            $user->password = null;
            $user->email_verified_at = now();
            $user->save();

            $user->userProviders()->create([
                'provider_id' => $oAuthUser->id,
                'name' => $provider,
            ]);
        } else {
            $user = $userProvider->user;
        }

        // Log the user in and regenerate the session
        Auth::login($user);
        $request->session()->regenerate();

        return view('oauth', [
            'message' => [
                'success' => true,
                'provider' => $provider,
            ],
        ]);
    }

    /**
     * Handle user login using sessions
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $this->logUserLogin($request->user(), $request);

        return response()->json(['success' => true]);
    }

    public function logUserLogin(User $user, Request $request): void
    {
        UserLoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }


    /**
     * Log out the authenticated user
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get authenticated user details
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                ...$user->toArray(),
                'must_verify_email' => $user->mustVerifyEmail(),
                'has_password' => (bool) $user->password,
                'providers' => $user->userProviders()->select('name')->pluck('name'),
            ],
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     * @throws ValidationException
     */
    public function sendResetPasswordLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['success' => true, 'message' => __($status)]);
    }

    /**
     * Handle an incoming new password request.
     * @throws ValidationException
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['success' => true, 'message' => __($status)]);
    }

    /**
     * Mark the authenticated user's email address as verified
     */
    public function verifyEmail(Request $request, string $ulid, string $hash): JsonResponse
    {
        $user = User::where('ulid', $ulid)->firstOrFail();

        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403, __('Invalid verification link'));

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }

        return response()->json(['success' => true]);
    }


    /**
     * Send a new email verification notification.
     */
    public function verificationNotification(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = $request->user() ?: User::where('email', $request->email)->whereNull('email_verified_at')->first();

        abort_if(!$user, 400);

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => __('Verification link sent!'),
        ]);
    }
}
