<?php

use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SharedContentController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function(){
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('/file', FileController::class);
    Route::resource('/folder', FolderController::class);
    Route::get('/folder/children/{folder}', [FolderController::class, 'showChildren'])->name('folder.children');
    Route::get('/file/download/{file}',[FileController::class, 'downloadFile'])->name('file.download');
    Route::post('/shared-content', [SharedContentController::class, 'store'])->name('shared-content.store');
    Route::get('/shared-content', [SharedContentController::class, 'index'])->name('shared-content.index');
    Route::delete('/shared-content', [SharedContentController::class, 'destroy'])->name('shared-content.destroy');
    Route::group(['middleware' => EnsureUserIsAdmin::class], function(){
        Route::get('/admin-page', [HomeController::class, 'adminPage'])->name('admin-page');
        Route::get('/admin-page/update', [HomeController::class, 'updateAllowedMemory'])->name('admin-page.update');
    });
});
Auth::routes();

Route::get('auth/google/login', [SocialLoginController::class, 'initGoogleLogin'])->name('login.google');
Route::get('auth/google/callback', [SocialLoginController::class, 'googleLoginCallback'])->name('login.google.callback');

Route::get('auth/facebook/login', [SocialLoginController::class, 'initFacebookLogin'])->name('login.facebook');
Route::get('auth/facebook/callback', [SocialLoginController::class, 'facebookLoginCallback'])->name('login.facebook.callback');

Route::get('auth/twitter/login', [SocialLoginController::class, 'initTwitterLogin'])->name('login.twitter');
Route::get('auth/twitter/callback', [SocialLoginController::class, 'twitterLoginCallback'])->name('login.twitter.callback');
