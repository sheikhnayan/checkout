<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\PaymentSettingsController;
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
use App\Http\Controllers\EntertainerRegistrationController;
use App\Http\Controllers\EntertainerPublicController;
use App\Http\Controllers\EntertainerAdminController;
use App\Http\Controllers\EntertainerPortalController;
use App\Http\Controllers\PackageCategoryController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FeedModelController;
use App\Http\Controllers\FeedPostController;
use App\Http\Controllers\JobMarketplaceController;
use App\Http\Controllers\Admin\JobMarketplaceController as AdminJobMarketplaceController;
use App\Http\Controllers\Admin\WebsiteRoleController;


// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

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
Route::get('/entertainer/apply', [EntertainerRegistrationController::class, 'showForm'])->name('entertainer.apply');
Route::post('/entertainer/apply', [EntertainerRegistrationController::class, 'submit'])->name('entertainer.apply.submit');
Route::get('/entertainer/{slug}', [EntertainerPublicController::class, 'show'])->name('entertainer.public');

// Public feed routes (must stay before slug route)
Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
Route::post('/feed/posts/{feedPost}/comments', [FeedController::class, 'storeComment'])->name('feed.comments.store');
Route::get('/{slug}/feed/roll-call', [FeedController::class, 'clubRollCall'])->name('club.feed.roll-call');
Route::get('/{slug}/feed/profile', [FeedController::class, 'clubProfile'])->name('club.feed.profile');
Route::get('/{slug}/feed/models/{feedModel}', [FeedController::class, 'modelProfile'])->name('club.feed.model.profile');
Route::get('/{slug}/feed', [FeedController::class, 'clubFeed'])->name('club.feed');

// Public job marketplace routes (must stay before slug route)
Route::get('/jobs', [JobMarketplaceController::class, 'index'])->name('jobs.marketplace');
Route::get('/jobs/listings', [JobMarketplaceController::class, 'listings'])->name('jobs.listings');
Route::get('/jobs/pre-apply', [JobMarketplaceController::class, 'preApplyForm'])->name('jobs.pre-apply');
Route::post('/jobs/pre-apply', [JobMarketplaceController::class, 'submitPreApply'])->name('jobs.pre-apply.submit');
Route::get('/jobs/{job}/apply', [JobMarketplaceController::class, 'applyForm'])->name('jobs.apply');
Route::post('/jobs/{job}/apply', [JobMarketplaceController::class, 'submitApplication'])->name('jobs.apply.submit');

// Frontend routes with slug parameter
Route::get('/{slug}', [FrontendController::class, 'index'])->name('index');

Route::get('/{slug}/addons/{id}', [FrontendController::class, 'addons'])->name('addons');

Route::get('/{slug}/check/{code}', [FrontendController::class, 'checkCode'])->name('check.code');

Route::post('/{slug}/checkout/store', [TransactionController::class, 'store'])->name('checkout.store');

Route::post('/{slug}/reservation/store', [TransactionController::class, 'reservation_store'])->name('reservations.store');

// Cart sharing routes
Route::post('/cart/share', [CartController::class, 'generateSharedLink'])->name('cart.generate-share');
Route::get('/cart/{code}', [CartController::class, 'viewSharedCart'])->name('shared-cart.view');

Route::group(['prefix'=> 'admins', 'as' => 'admin.', 'middleware' => ['auth', 'image.upload.guard', 'route.permission']], function () {
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
        Route::get('/{website}/payment-settings', [PaymentSettingsController::class,'edit'])->name('payment-settings');
        Route::post('/{website}/payment-settings', [PaymentSettingsController::class,'update'])->name('payment-settings.update');
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
        Route::get('/scan', [TransactionController::class, 'scanPage'])->name('scan');
        Route::get('/scan/lookup', [TransactionController::class, 'scanLookup'])->name('scan.lookup');
        Route::post('/scan/check-in', [TransactionController::class, 'scanCheckIn'])->name('scan.check-in');
    });

    Route::group(['prefix' => 'jobs', 'as' => 'jobs.'], function () {
        Route::get('/', [AdminJobMarketplaceController::class, 'index'])->name('index');
        Route::get('/create', [AdminJobMarketplaceController::class, 'create'])->name('create');
        Route::post('/store', [AdminJobMarketplaceController::class, 'store'])->name('store');
        Route::get('/{job}/edit', [AdminJobMarketplaceController::class, 'edit'])->name('edit');
        Route::post('/{job}/update', [AdminJobMarketplaceController::class, 'update'])->name('update');

        Route::get('/applications', [AdminJobMarketplaceController::class, 'applications'])->name('applications');
        Route::get('/applications/{application}', [AdminJobMarketplaceController::class, 'showApplication'])->name('applications.show');
        Route::post('/applications/{application}/status', [AdminJobMarketplaceController::class, 'updateApplicationStatus'])->name('applications.status');

        Route::get('/preference-requests', [AdminJobMarketplaceController::class, 'preferenceRequests'])->name('preference-requests');
        Route::post('/preference-requests/{preferenceRequest}/status', [AdminJobMarketplaceController::class, 'updatePreferenceStatus'])->name('preference-requests.status');
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
        Route::post('/{affiliate}/commission', [AffiliateAdminController::class, 'updateCommission'])->name('commission.update');
        Route::post('/{affiliate}/packages', [AffiliateAdminController::class, 'updatePackages'])->name('packages.update');
    });

    Route::group(['prefix'=> 'entertainer', 'as' => 'entertainer.'], function () {
        Route::get('/', [EntertainerAdminController::class, 'index'])->name('index');
        Route::get('/{entertainer}', [EntertainerAdminController::class, 'show'])->name('show');
        Route::post('/{entertainer}/approve', [EntertainerAdminController::class, 'approve'])->name('approve');
        Route::post('/{entertainer}/reject', [EntertainerAdminController::class, 'reject'])->name('reject');
        Route::post('/{entertainer}/commission', [EntertainerAdminController::class, 'updateCommission'])->name('commission.update');
    });

    Route::group(['prefix' => 'feed-model', 'as' => 'feed-model.'], function () {
        Route::get('/', [FeedModelController::class, 'index'])->name('index');
        Route::get('/create', [FeedModelController::class, 'create'])->name('create');
        Route::post('/store', [FeedModelController::class, 'store'])->name('store');
        Route::get('/edit/{feedModel}', [FeedModelController::class, 'edit'])->name('edit');
        Route::post('/update/{feedModel}', [FeedModelController::class, 'update'])->name('update');
        Route::post('/destroy/{feedModel}', [FeedModelController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix' => 'feed-post', 'as' => 'feed-post.'], function () {
        Route::get('/', [FeedPostController::class, 'index'])->name('index');
        Route::get('/create', [FeedPostController::class, 'create'])->name('create');
        Route::post('/store', [FeedPostController::class, 'store'])->name('store');
        Route::get('/show/{feedPost}', [FeedPostController::class, 'show'])->name('show');
        Route::get('/edit/{feedPost}', [FeedPostController::class, 'edit'])->name('edit');
        Route::post('/update/{feedPost}', [FeedPostController::class, 'update'])->name('update');
        Route::post('/destroy/{feedPost}', [FeedPostController::class, 'destroy'])->name('destroy');
        Route::post('/{feedPost}/comments/{feedComment}/toggle', [FeedPostController::class, 'toggleCommentVisibility'])->name('comments.toggle');
        Route::post('/{feedPost}/comments/{feedComment}/destroy', [FeedPostController::class, 'destroyComment'])->name('comments.destroy');
    });

    // Website Users Management
    Route::resource('website-users', App\Http\Controllers\Admin\WebsiteUserController::class);
    Route::post('website-users/archive/{id}', [App\Http\Controllers\Admin\WebsiteUserController::class, 'archive'])->name('website-users.archive');

    // Website Role Management
    Route::resource('website-roles', WebsiteRoleController::class)->except(['show']);
    Route::post('website-roles/{website_role}/archive', [WebsiteRoleController::class, 'archive'])->name('website-roles.archive');
});

Route::group(['prefix'=> 'affiliate-portal', 'as' => 'affiliate.portal.', 'middleware' => ['auth', 'image.upload.guard']], function () {
    Route::get('/dashboard', [AffiliatePortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/packages', [AffiliatePortalController::class, 'packages'])->name('packages');
    Route::post('/packages', [AffiliatePortalController::class, 'savePackages'])->name('packages.save');
    Route::get('/settings', [AffiliatePortalController::class, 'settings'])->name('settings');
    Route::post('/settings', [AffiliatePortalController::class, 'updateSettings'])->name('settings.update');
    Route::get('/wallet', [AffiliatePortalController::class, 'wallet'])->name('wallet');
});

Route::group(['prefix'=> 'entertainer-portal', 'as' => 'entertainer.portal.', 'middleware' => ['auth', 'image.upload.guard']], function () {
    Route::get('/dashboard', [EntertainerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/packages', [EntertainerPortalController::class, 'packages'])->name('packages');
    Route::post('/packages', [EntertainerPortalController::class, 'savePackages'])->name('packages.save');
    Route::get('/settings', [EntertainerPortalController::class, 'settings'])->name('settings');
    Route::post('/settings', [EntertainerPortalController::class, 'updateSettings'])->name('settings.update');
    Route::get('/wallet', [EntertainerPortalController::class, 'wallet'])->name('wallet');
});

// Payment Logo routes (outside admin group for direct access)
Route::post('/payment-logos', [PaymentLogoController::class, 'store'])->middleware('image.upload.guard')->name('payment-logos.store');
Route::delete('/payment-logos/{id}', [PaymentLogoController::class, 'destroy'])->name('payment-logos.destroy');
