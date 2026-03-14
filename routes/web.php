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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomInvoiceController;
use App\Http\Controllers\AffiliateRegistrationController;
use App\Http\Controllers\AffiliatePublicController;
use App\Http\Controllers\AffiliateAdminController;
use App\Http\Controllers\AffiliatePortalController;
use App\Http\Controllers\PackageCategoryController;


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

// Thank You page (after successful payment)
Route::get('/thank-you', [TransactionController::class, 'thankYou'])->name('thank-you');

// Custom Invoice Payment routes (client-facing, no auth required)
// MUST be before slug route to avoid 404
Route::get('/custom-invoice/{token}/pay', [CustomInvoiceController::class, 'showPayment'])->name('custom-invoice.pay');
Route::post('/custom-invoice/{token}/process-payment', [CustomInvoiceController::class, 'processPayment'])->name('custom-invoice.process-payment');

// Affiliate public routes (must stay before slug route)
Route::get('/affiliate/apply', [AffiliateRegistrationController::class, 'showForm'])->name('affiliate.apply');
Route::post('/affiliate/apply', [AffiliateRegistrationController::class, 'submit'])->name('affiliate.apply.submit');
Route::get('/affiliate/{slug}', [AffiliatePublicController::class, 'show'])->name('affiliate.public');

// Frontend routes with slug parameter
Route::get('/{slug}', [FrontendController::class, 'index'])->name('index');

Route::get('/{slug}/addons/{id}', [FrontendController::class, 'addons'])->name('addons');

Route::get('/{slug}/check/{code}', [FrontendController::class, 'checkCode'])->name('check.code');

Route::post('/{slug}/checkout/store', [TransactionController::class, 'store'])->name('checkout.store');

Route::post('/{slug}/reservation/store', [TransactionController::class, 'reservation_store'])->name('reservations.store');

// Cart sharing routes
Route::post('/cart/share', [CartController::class, 'generateSharedLink'])->name('cart.generate-share');
Route::get('/cart/{code}', [CartController::class, 'viewSharedCart'])->name('shared-cart.view');

Route::group(['prefix'=> 'admins', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    Route::get('/', [AdminController::class,'index'])->name('index');

    Route::group(['prefix'=> 'website', 'as' => 'website.'], function () {
        Route::get('/', [WebsiteController::class,'index'])->name('index');
        Route::post('/archive/{id}', [WebsiteController::class,'archive'])->name('archive');
        Route::post('/unarchive/{id}', [WebsiteController::class,'unarchive'])->name('unarchive');
        Route::post('/toggle-status/{id}', [WebsiteController::class,'toggleStatus'])->name('toggle-status');
        Route::get('/create', [WebsiteController::class,'create'])->name('create');
        Route::post('/store', [WebsiteController::class,'store'])->name('store');
        Route::get('/edit/{id}', [WebsiteController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [WebsiteController::class,'update'])->name('update');
    });

    Route::group(['prefix'=> 'package-category', 'as' => 'package-category.'], function () {
        Route::post('/store/{websiteId}', [PackageCategoryController::class, 'store'])->name('store');
        Route::post('/update/{id}', [PackageCategoryController::class, 'update'])->name('update');
        Route::post('/destroy/{id}', [PackageCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix'=> 'package', 'as' => 'package.'], function () {
        Route::get('/', [PackageController::class,'index'])->name('index');
        Route::get('/show/{id}', [PackageController::class,'show'])->name('show');
        Route::post('/archive/{id}', [PackageController::class,'archive'])->name('archive');
        Route::post('/unarchive/{id}', [PackageController::class,'unarchive'])->name('unarchive');
            Route::post('/toggle-status/{id}', [PackageController::class,'toggleStatus'])->name('toggle-status');
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
        Route::post('/toggle-status/{id}', [AddonController::class,'toggleStatus'])->name('toggle-status');
        Route::get('/create/{id}', [AddonController::class,'create'])->name('create');
        Route::post('/store', [AddonController::class,'store'])->name('store');
        Route::get('/edit/{id}', [AddonController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [AddonController::class,'update'])->name('update');
            Route::post('/destroy/{id}', [AddonController::class,'destroy'])->name('destroy');
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

    Route::group(['prefix'=> 'profile', 'as' => 'profile.'], function () {
        Route::get('/', [ProfileController::class,'edit'])->name('edit');
        Route::post('/update-password', [ProfileController::class,'updatePassword'])->name('update-password');
    });

    // Custom Invoice routes
    Route::group(['prefix'=> 'custom-invoice', 'as' => 'custom-invoice.'], function () {
        Route::get('/', [CustomInvoiceController::class,'index'])->name('index');
        Route::get('/create', [CustomInvoiceController::class,'create'])->name('create');
        Route::post('/store', [CustomInvoiceController::class,'store'])->name('store');
        Route::post('/store-and-send', [CustomInvoiceController::class,'storeAndSend'])->name('store-and-send');
        Route::post('/{customInvoice}/archive', [CustomInvoiceController::class,'archive'])->name('archive');
        Route::post('/{customInvoice}/unarchive', [CustomInvoiceController::class,'unarchive'])->name('unarchive');
        Route::get('/{customInvoice}', [CustomInvoiceController::class,'show'])->name('show');
        Route::get('/{customInvoice}/edit', [CustomInvoiceController::class,'edit'])->name('edit');
        Route::put('/{customInvoice}', [CustomInvoiceController::class,'update'])->name('update');
        Route::post('/{customInvoice}/send', [CustomInvoiceController::class,'send'])->name('send');
        Route::delete('/{customInvoice}', [CustomInvoiceController::class,'destroy'])->name('destroy');
    });

    // Affiliate admin routes
    Route::group(['prefix'=> 'affiliate', 'as' => 'affiliate.'], function () {
        Route::get('/', [AffiliateAdminController::class, 'index'])->name('index');
        Route::get('/{affiliate}', [AffiliateAdminController::class, 'show'])->name('show');
        Route::post('/{affiliate}/approve', [AffiliateAdminController::class, 'approve'])->name('approve');
        Route::post('/{affiliate}/reject', [AffiliateAdminController::class, 'reject'])->name('reject');
        Route::post('/{affiliate}/packages', [AffiliateAdminController::class, 'updatePackages'])->name('packages.update');
    });

    // Website Users Management
    Route::resource('website-users', App\Http\Controllers\Admin\WebsiteUserController::class);
    Route::post('website-users/archive/{id}', [App\Http\Controllers\Admin\WebsiteUserController::class, 'archive'])->name('website-users.archive');
});

Route::group(['prefix'=> 'affiliate-portal', 'as' => 'affiliate.portal.', 'middleware' => 'auth'], function () {
    Route::get('/dashboard', [AffiliatePortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/packages', [AffiliatePortalController::class, 'packages'])->name('packages');
    Route::post('/packages', [AffiliatePortalController::class, 'savePackages'])->name('packages.save');
    Route::get('/settings', [AffiliatePortalController::class, 'settings'])->name('settings');
    Route::post('/settings', [AffiliatePortalController::class, 'updateSettings'])->name('settings.update');
    Route::get('/wallet', [AffiliatePortalController::class, 'wallet'])->name('wallet');
});

// Payment Logo routes (outside admin group for direct access)
Route::post('/payment-logos', [PaymentLogoController::class, 'store'])->name('payment-logos.store');
Route::delete('/payment-logos/{id}', [PaymentLogoController::class, 'destroy'])->name('payment-logos.destroy');
