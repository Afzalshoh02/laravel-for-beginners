<?php

use App\Http\Controllers\Profile\AvatarController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use OpenAI\Laravel\Facades\OpenAI;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar');
    Route::post('/profile/avatar/ai', [AvatarController::class, 'generate'])->name('profile.avatar.ai');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::post('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
})->name('login.github');

Route::get('/auth/callback', function () {
    $user = Socialite::driver('github')->stateless()->user();
    $user = \App\Models\User::updateOrCreate(
        [
            'email' => $user->email,
        ],[
            'name' => $user->nickname,
            'password' => 'password',
        ]
    );
    \Illuminate\Support\Facades\Auth::login($user);
    return redirect('/dashboard');
//    dd($user);
});

//Route::get('/openai', function () {
//    $result = OpenAI::images()->create([
////        'model' => 'gpt-3.5-turbo',
////        'model' => 'dall-e-3',
//        "prompt" => "create image coder",
//        "n" => 1,
//        "size" => "256x256"
//    ]);
//    $result;
////    return response(['url' => $result->data[0]->url]);
////    dd($result);
//});
