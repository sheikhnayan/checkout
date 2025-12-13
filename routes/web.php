<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PaymentLogoController;
use App\Http\Controllers\AuthController;


// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/run-migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return 'Migration completed: ' . Artisan::output();
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Test route - must be before the catch-all slug route
Route::get('/test', [TransactionController::class, 'test'])->name('test');

// Frontend routes with slug parameter
Route::get('/{slug}', [FrontendController::class, 'index'])->name('index');

Route::get('/{slug}/addons/{id}', [FrontendController::class, 'addons'])->name('addons');

Route::get('/{slug}/check/{code}', [FrontendController::class, 'checkCode'])->name('check.code');

Route::post('/{slug}/checkout/store', [TransactionController::class, 'store'])->name('checkout.store');

Route::post('/{slug}/reservation/store', [TransactionController::class, 'reservation_store'])->name('reservations.store');

Route::group(['prefix'=> 'admins', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    Route::get('/', [AdminController::class,'index'])->name('index');

    Route::group(['prefix'=> 'website', 'as' => 'website.'], function () {
        Route::get('/', [WebsiteController::class,'index'])->name('index');
        Route::post('/archive/{id}', [WebsiteController::class,'archive'])->name('archive');
        Route::post('/unarchive/{id}', [WebsiteController::class,'unarchive'])->name('unarchive');
        Route::get('/create', [WebsiteController::class,'create'])->name('create');
        Route::post('/store', [WebsiteController::class,'store'])->name('store');
        Route::get('/edit/{id}', [WebsiteController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [WebsiteController::class,'update'])->name('update');
    });

    Route::group(['prefix'=> 'package', 'as' => 'package.'], function () {
        Route::get('/', [PackageController::class,'index'])->name('index');
        Route::get('/show/{id}', [PackageController::class,'show'])->name('show');
        Route::post('/archive/{id}', [PackageController::class,'archive'])->name('archive');
        Route::post('/unarchive/{id}', [PackageController::class,'unarchive'])->name('unarchive');
        Route::get('/create/{id}', [PackageController::class,'create'])->name('create');
        Route::post('/store', [PackageController::class,'store'])->name('store');
        Route::get('/edit/{id}', [PackageController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [PackageController::class,'update'])->name('update');
    });

    Route::group(['prefix'=> 'event', 'as' => 'event.'], function () {
        Route::get('/', [EventController::class,'index'])->name('index');
        Route::get('/show/{id}', [EventController::class,'show'])->name('show');
        Route::post('/archive/{id}', [EventController::class,'archive'])->name('archive');
        Route::post('/unarchive/{id}', [EventController::class,'unarchive'])->name('unarchive');
        Route::get('/create/{id}', [EventController::class,'create'])->name('create');
        Route::post('/store', [EventController::class,'store'])->name('store');
        Route::get('/edit/{id}', [EventController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [EventController::class,'update'])->name('update');
    });
    
    Route::group(['prefix'=> 'addon', 'as' => 'addon.'], function () {
        Route::get('/', [AddonController::class,'index'])->name('index');
        Route::get('/show/{id}', [AddonController::class,'show'])->name('show');
        Route::post('/archive/{id}', [AddonController::class,'archive'])->name('archive');
        Route::post('/unarchive/{id}', [AddonController::class,'unarchive'])->name('unarchive');
        Route::get('/create/{id}', [AddonController::class,'create'])->name('create');
        Route::post('/store', [AddonController::class,'store'])->name('store');
        Route::get('/edit/{id}', [AddonController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [AddonController::class,'update'])->name('update');
    });
    
    Route::group(['prefix'=> 'promo_code', 'as' => 'promo_code.'], function () {
        Route::get('/', [PromoCodeController::class,'index'])->name('index');
        Route::get('/show/{id}', [PromoCodeController::class,'show'])->name('show');
        Route::post('/archive/{id}', [PromoCodeController::class,'archive'])->name('archive');
        Route::post('/unarchive/{id}', [PromoCodeController::class,'unarchive'])->name('unarchive');
        Route::get('/create/{id}', [PromoCodeController::class,'create'])->name('create');
        Route::post('/store', [PromoCodeController::class,'store'])->name('store');
        Route::get('/edit/{id}', [PromoCodeController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [PromoCodeController::class,'update'])->name('update');
    });

    Route::group(['prefix'=> 'transaction', 'as' => 'transaction.'], function () {
        Route::get('/', [TransactionController::class,'index'])->name('index');
        Route::get('/show/{id}', [TransactionController::class,'show'])->name('show');
        Route::get('/change/{id}/{status}', [TransactionController::class,'update'])->name('update');
    });

    Route::group(['prefix'=> 'setting', 'as' => 'setting.'], function () {
        Route::get('/edit/{id}', [SettingController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [SettingController::class,'update'])->name('update');
    });

    // Website Users Management
    Route::resource('website-users', App\Http\Controllers\Admin\WebsiteUserController::class);
    Route::post('website-users/archive/{id}', [App\Http\Controllers\Admin\WebsiteUserController::class, 'archive'])->name('website-users.archive');
});

// Payment Logo routes (outside admin group for direct access)
Route::post('/payment-logos', [PaymentLogoController::class, 'store'])->name('payment-logos.store');
Route::delete('/payment-logos/{id}', [PaymentLogoController::class, 'destroy'])->name('payment-logos.destroy');
