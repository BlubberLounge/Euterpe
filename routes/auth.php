<?php

use App\Enums\ExternalServices;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Models\ExternalServiceUser;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\Music\SpotifyService;

Route::get('/auth/spotify/redirect', function (Request $request, SpotifyService $service) {
    return redirect($service->getAuthorizationUrl());
})->name('auth.spotify.redirect'); //oauth-redirect

Route::get('/auth/spotify/callback', function (Request $request, SpotifyService $service) {
    $authUser = $service->user($request);

    $user = User::updateOrCreate(
        ['email' => $authUser['email']],
        [
            'name' => $authUser['id'],
            'password' => $authUser['id']
        ]
    );

    $user->refresh();

    ExternalServiceUser::updateOrCreate(
        [
            'user_id' => $user->id,
            'service' => ExternalServices::SPOTIFY,
        ],
        [
            'name' => $user->id,
            'email' => $authUser['email'],
            'access_token' => $authUser['access_token'],
            'refresh_token' => $authUser['refresh_token'],
            'token_expires_at' => now()->addSeconds($authUser['expires_in'] ?? 3600),
        ]
    );

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->intended(route('dashboard'));
})->name('spotify.callback');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Route::get('/auth/spotify/redirect', function (Request $request, SpotifyOAuthService $OAuth) {
    //     return $OAuth->redirect($request);
    // })->name('spotify.redirect'); //oauth-redirect

    // Route::get('/auth/spotify/callback', function (Request $request, SpotifyOAuthService $OAuth) {
    //     $authUser = $OAuth->user($request);

    //     $user = User::updateOrCreate(
    //         ['email' => $authUser['email']],
    //         [
    //             'name' => $authUser['id'],
    //             'password' => $authUser['id']
    //         ]
    //     );

    //     ExternalServiceUser::updateOrCreate(
    //         [
    //             'user_id' => $user->id,
    //             'service' => ExternalServices::SPOTIFY,
    //         ],
    //         [
    //             'access_token' => $authUser['access_token'],
    //             'refresh_token' => $authUser['refresh_token'] ?? null,
    //             // 'token_expires_at' => now()->addSeconds($authUser['expires_in'] ?? 3600),
    //         ]
    //     );

    //     Auth::login($user);
    //     $request->session()->regenerate();

    //     return redirect()->intended(route('dashboard'));
    // })->name('spotify.callback');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
