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
use App\Http\Controllers\StaffRegistrationController;
use App\Http\Controllers\StaffAdminController;
use App\Http\Controllers\EntertainerRegistrationController;
use App\Http\Controllers\EntertainerPublicController;
use App\Http\Controllers\EntertainerAdminController;
use App\Http\Controllers\EntertainerPortalController;
use App\Http\Controllers\SocialSignupController;
use App\Http\Controllers\PackageCategoryController;
use App\Http\Controllers\TelnyxWebhookController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FeedModelController;
use App\Http\Controllers\FeedPostController;
use App\Http\Controllers\CheckoutPopupController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\JobMarketplaceController;
use App\Http\Controllers\Admin\JobMarketplaceController as AdminJobMarketplaceController;
use App\Http\Controllers\Admin\WebsiteRoleController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\ReportController;


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

// W-9 Form routes (client-facing, no auth required)
// MUST be before slug route to avoid 404
use App\Http\Controllers\W9FormController;
Route::get('/w9/thank-you', [W9FormController::class, 'thankYou'])->name('w9.thank-you');
Route::get('/w9/{token}', [W9FormController::class, 'show'])->name('w9.show');
Route::post('/w9/{token}/submit', [W9FormController::class, 'store'])->name('w9.store');
Route::get('/admin/w9/{id}/modal', [W9FormController::class, 'viewModal'])->name('w9.modal')->middleware('auth');
Route::get('/admin/w9/{id}/download-pdf', [W9FormController::class, 'downloadPdf'])->name('w9.download')->middleware('auth');

// DEBUG: Test W9 data
Route::get('/debug/w9/{id}', function($id) {
    try {
        $w9 = \App\Models\W9Form::find($id);
        if (!$w9) {
            return response()->json(['error' => 'W9Form not found'], 404);
        }
        return response()->json([
            'id' => $w9->id,
            'id_document_type' => $w9->id_document_type,
            'id_front_image' => $w9->id_front_image,
            'id_back_image' => $w9->id_back_image,
            'status' => $w9->status,
            'created_at' => $w9->created_at,
            'certification_ip' => $w9->certification_ip,
            'full_name' => $w9->full_name,
            'street_address' => $w9->street_address,
            'city' => $w9->city,
            'state' => $w9->state,
            'zip_code' => $w9->zip_code,
            'tax_id_type' => $w9->tax_id_type,
            'tax_id_number' => $w9->tax_id_number,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// DEBUG: Test PDF generation
Route::get('/debug/pdf/{id}', function($id) {
    try {
        $w9 = \App\Models\W9Form::find($id);
        if (!$w9) {
            return response()->json(['error' => 'W9Form not found'], 404);
        }

        // Test template path
        $templatePath = storage_path('app/public/w9-template/fw9_template.pdf');
        return response()->json([
            'template_path' => $templatePath,
            'template_exists' => file_exists($templatePath),
            'w9_data' => [
                'full_name' => $w9->full_name,
                'id_document_type' => $w9->id_document_type,
                'created_at' => $w9->created_at ? $w9->created_at->format('Y-m-d H:i:s') : null,
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
    }
});

// affiliate public routes (must stay before slug route)
Route::get('/staff/apply', [StaffRegistrationController::class, 'showForm'])->name('staff.apply');
Route::post('/staff/apply', [StaffRegistrationController::class, 'submit'])
    ->middleware(['throttle:10,60', \App\Http\Middleware\HandleThrottleResponse::class])
    ->name('staff.apply.submit');

Route::get('/affiliate/apply', [AffiliateRegistrationController::class, 'showForm'])->name('affiliate.apply');
Route::post('/affiliate/apply', [AffiliateRegistrationController::class, 'submit'])
    ->middleware(['throttle:10,60', \App\Http\Middleware\HandleThrottleResponse::class])
    ->name('affiliate.apply.submit');
Route::get('/signup/{role}/{provider}/redirect', [SocialSignupController::class, 'redirect'])->name('social.signup.redirect');
Route::get('/signup/{role}/{provider}/callback', [SocialSignupController::class, 'callback'])->name('social.signup.callback');
Route::get('/affiliate/{slug}', [AffiliatePublicController::class, 'show'])->name('affiliate.public');
Route::get('/entertainer/apply', [EntertainerRegistrationController::class, 'showForm'])->name('entertainer.apply');
Route::post('/entertainer/apply', [EntertainerRegistrationController::class, 'submit'])
    ->middleware(['throttle:10,60', \App\Http\Middleware\HandleThrottleResponse::class])
    ->name('entertainer.apply.submit');
Route::get('/entertainer/{slug}', [EntertainerPublicController::class, 'show'])->name('entertainer.public');
Route::get('/incident-witness/{token}', [IncidentController::class, 'publicWitnessForm'])->name('incident.witness.form');
Route::post('/incident-witness/{token}', [IncidentController::class, 'publicWitnessStore'])->name('incident.witness.submit');

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

Route::get('/{slug}/addons/{id}', [FrontendController::class, 'addons'])->name('addons');

Route::get('/{slug}/check/{code}', [FrontendController::class, 'checkCode'])->name('check.code');
Route::get('/{slug}/auto-discounts', [FrontendController::class, 'autoDiscounts'])->name('auto.discounts');

Route::get('/{slug}/package/{packageId}/capacity', [FrontendController::class, 'checkPackageCapacity'])->name('package.capacity');

Route::post('/{slug}/checkout/store', [TransactionController::class, 'store'])->name('checkout.store');

Route::post('/{slug}/reservation/store', [TransactionController::class, 'reservation_store'])
    ->middleware(['throttle:10,60', \App\Http\Middleware\HandleThrottleResponse::class])
    ->name('reservations.store');

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
        Route::post('/archive/{id}', [PackageCategoryController::class, 'archive'])->name('archive');
        Route::post('/unarchive/{id}', [PackageCategoryController::class, 'unarchive'])->name('unarchive');
        Route::post('/destroy/{id}', [PackageCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/reorder/{websiteId}', [PackageCategoryController::class, 'reorder'])->name('reorder');
    });

    Route::group(['prefix'=> 'package', 'as' => 'package.'], function () {
        Route::get('/', [PackageController::class,'index'])->name('index');
        Route::get('/show/{id}', [PackageController::class,'show'])->name('show');
        Route::post('/archive/{id}', [PackageController::class,'archive'])->name('archive');
        Route::post('/unarchive/{id}', [PackageController::class,'unarchive'])->name('unarchive');
        Route::post('/toggle-status/{id}', [PackageController::class,'toggleStatus'])->name('toggle-status');
        Route::get('/create/{id}', [PackageController::class,'create'])->name('create');
        Route::get('/create-targeted/{audience}', [PackageController::class,'createTargeted'])->name('create-targeted');
        Route::post('/store', [PackageController::class,'store'])->name('store');
        Route::post('/store-targeted', [PackageController::class,'storeTargeted'])->name('store-targeted');
        Route::get('/edit/{id}', [PackageController::class,'edit'])->name('edit');
        Route::get('/edit-targeted/{id}', [PackageController::class,'editTargeted'])->name('edit-targeted');
        Route::post('/update/{id}', [PackageController::class,'update'])->name('update');
        Route::post('/update-targeted/{id}', [PackageController::class,'updateTargeted'])->name('update-targeted');
        Route::post('/reorder/{websiteId}', [PackageController::class,'reorder'])->name('reorder');
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
        Route::get('/create-targeted/{audience}', [PromoCodeController::class,'createTargeted'])->name('create-targeted');
        Route::get('/create/{id}', [PromoCodeController::class,'create'])->name('create');
        Route::post('/store', [PromoCodeController::class,'store'])->name('store');
        Route::get('/edit/{id}', [PromoCodeController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [PromoCodeController::class,'update'])->name('update');
    });

    Route::group(['prefix'=> 'popup', 'as' => 'popup.'], function () {
        Route::get('/', [CheckoutPopupController::class,'index'])->name('index');
        Route::get('/show/{id}', [CheckoutPopupController::class,'show'])->name('show');
        Route::post('/archive/{id}', [CheckoutPopupController::class,'archive'])->name('archive');
        Route::post('/unarchive/{id}', [CheckoutPopupController::class,'unarchive'])->name('unarchive');
        Route::get('/create/{id}', [CheckoutPopupController::class,'create'])->name('create');
        Route::post('/store', [CheckoutPopupController::class,'store'])->name('store');
        Route::get('/edit/{id}', [CheckoutPopupController::class,'edit'])->name('edit');
        Route::post('/update/{id}', [CheckoutPopupController::class,'update'])->name('update');
    });

    Route::group(['prefix'=> 'incident', 'as' => 'incident.'], function () {
        Route::get('/', [IncidentController::class, 'index'])->name('index');
        Route::get('/show/{websiteId}', [IncidentController::class, 'show'])->name('show');
        Route::get('/create/{websiteId}', [IncidentController::class, 'create'])->name('create');
        Route::post('/store', [IncidentController::class, 'store'])->name('store');
        Route::get('/details/{incidentId}', [IncidentController::class, 'details'])->name('details');
        Route::post('/details/{incidentId}/status', [IncidentController::class, 'updateStatus'])->name('status.update');
        Route::get('/{incidentId}/witness/create', [IncidentController::class, 'createWitness'])->name('witness.create');
        Route::post('/{incidentId}/witness/store', [IncidentController::class, 'storeWitness'])->name('witness.store');
        Route::get('/witness/{witnessId}/print', [IncidentController::class, 'printWitness'])->name('witness.print');
        Route::get('/witness/{witnessId}/download', [IncidentController::class, 'downloadWitness'])->name('witness.download');
        Route::get('/{incidentId}/export', [IncidentController::class, 'export'])->name('export');
    });

    Route::group(['prefix'=> 'transaction', 'as' => 'transaction.'], function () {
        Route::get('/', [TransactionController::class,'index'])->name('index');
        Route::get('/affiliate', [TransactionController::class,'affiliateIndex'])->name('affiliate');
        Route::get('/entertainer', [TransactionController::class,'entertainerIndex'])->name('entertainer');
        Route::get('/show/{id}', [TransactionController::class,'show'])->name('show');
        Route::get('/{id}/details', [TransactionController::class,'details'])->name('details');
        Route::get('/change/{id}/{status}', [TransactionController::class,'update'])->name('update');
        Route::get('/scan', [TransactionController::class, 'scanPage'])->name('scan');
        Route::get('/scan/lookup', [TransactionController::class, 'scanLookup'])->name('scan.lookup');
        Route::post('/scan/check-in', [TransactionController::class, 'scanCheckIn'])->name('scan.check-in');
        Route::get('/{id}/checkin-photo', [TransactionController::class, 'viewCheckinPhoto'])->name('checkin-photo');
        Route::get('/{transactionId}/id-photos', [TransactionController::class, 'getIdPhotos'])->name('id-photos');
        Route::get('/{transactionId}/id-photo/{side}', [TransactionController::class, 'getIdPhoto'])->name('id-photo');
        Route::get('/{id}/pdf', [TransactionController::class, 'downloadPdf'])->name('pdf');
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

    // affiliate admin routes
    Route::group(['prefix'=> 'affiliate', 'as' => 'affiliate.'], function () {
        Route::get('/', [AffiliateAdminController::class, 'index'])->name('index');
        Route::get('/{affiliate}', [AffiliateAdminController::class, 'show'])->name('show');
        Route::post('/{affiliate}/approve', [AffiliateAdminController::class, 'approve'])->name('approve');
        Route::post('/{affiliate}/unapprove', [AffiliateAdminController::class, 'unapprove'])->name('unapprove');
        Route::post('/{affiliate}/reject', [AffiliateAdminController::class, 'reject'])->name('reject');
        Route::post('/{affiliate}/commission', [AffiliateAdminController::class, 'updateCommission'])->name('commission.update');
        Route::post('/{affiliate}/packages', [AffiliateAdminController::class, 'updatePackages'])->name('packages.update');
    });

    Route::group(['prefix'=> 'entertainer', 'as' => 'entertainer.'], function () {
        Route::get('/', [EntertainerAdminController::class, 'index'])->name('index');
        Route::get('/{entertainer}', [EntertainerAdminController::class, 'show'])->name('show');
        Route::post('/{entertainer}/approve', [EntertainerAdminController::class, 'approve'])->name('approve');
        Route::post('/{entertainer}/unapprove', [EntertainerAdminController::class, 'unapprove'])->name('unapprove');
        Route::post('/{entertainer}/reject', [EntertainerAdminController::class, 'reject'])->name('reject');
        Route::post('/{entertainer}/commission', [EntertainerAdminController::class, 'updateCommission'])->name('commission.update');
    });

    Route::group(['prefix' => 'staff', 'as' => 'staff.'], function () {
        Route::get('/', [StaffAdminController::class, 'index'])->name('index');
        Route::get('/{type}/{id}', [StaffAdminController::class, 'show'])->name('show');
        Route::post('/{type}/{id}/approve', [StaffAdminController::class, 'approve'])->name('approve');
        Route::post('/{type}/{id}/reject', [StaffAdminController::class, 'reject'])->name('reject');
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
        Route::post('/bulk-approve', [FeedPostController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/approve/{feedPost}', [FeedPostController::class, 'approve'])->name('approve');
        Route::post('/reject/{feedPost}', [FeedPostController::class, 'reject'])->name('reject');
        Route::post('/destroy/{feedPost}', [FeedPostController::class, 'destroy'])->name('destroy');
        Route::post('/{feedPost}/comments/{feedComment}/toggle', [FeedPostController::class, 'toggleCommentVisibility'])->name('comments.toggle');
        Route::post('/{feedPost}/comments/{feedComment}/destroy', [FeedPostController::class, 'destroyComment'])->name('comments.destroy');
    });

    // Website Users Management
    Route::resource('website-users', App\Http\Controllers\Admin\WebsiteUserController::class);
    Route::post('website-users/archive/{id}', [App\Http\Controllers\Admin\WebsiteUserController::class, 'archive'])->name('website-users.archive');

    // Manager Users Management (super admin only — enforced inside controller)
    Route::resource('manager-users', App\Http\Controllers\Admin\ManagerUserController::class);
    Route::post('manager-users/archive/{id}', [App\Http\Controllers\Admin\ManagerUserController::class, 'archive'])->name('manager-users.archive');

    // Website Role Management
    Route::resource('website-roles', WebsiteRoleController::class)->except(['show']);
    Route::post('website-roles/{website_role}/archive', [WebsiteRoleController::class, 'archive'])->name('website-roles.archive');

    // Withdraw management (admin side)
    Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.'], function () {
        Route::get('/affiliates', [WithdrawController::class, 'adminAffiliates'])->name('affiliates');
        Route::post('/affiliates/{id}/status', [WithdrawController::class, 'adminAffiliateStatus'])->name('affiliates.status');
        Route::post('/affiliates/charge', [WithdrawController::class, 'adminAffiliateCharge'])->name('affiliates.charge');
        Route::get('/entertainers', [WithdrawController::class, 'adminEntertainers'])->name('entertainers');
        Route::post('/entertainers/{id}/status', [WithdrawController::class, 'adminEntertainerStatus'])->name('entertainers.status');
        Route::post('/entertainers/charge', [WithdrawController::class, 'adminEntertainerCharge'])->name('entertainers.charge');
    });

    // Reports (Analytics & Reporting)
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/category/{category}', [ReportController::class, 'byCategory'])->name('category');
        Route::get('/{report}', [ReportController::class, 'show'])->name('show');
        Route::get('/{report}/metadata', [ReportController::class, 'metadata'])->name('metadata');
        Route::post('/{report}/preferences', [ReportController::class, 'savePreference'])->name('preferences.save');
        Route::get('/saved-reports', [ReportController::class, 'getSavedReports'])->name('saved');
        Route::delete('/preferences/{preference}', [ReportController::class, 'deletePreference'])->name('preferences.delete');
        Route::post('/{report}/export', [ReportController::class, 'export'])->name('export');
    });
});

Route::group(['prefix'=> 'affiliate-portal', 'as' => 'affiliate.portal.', 'middleware' => ['auth', 'image.upload.guard']], function () {
    Route::get('/dashboard', [AffiliatePortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/packages', [AffiliatePortalController::class, 'packages'])->name('packages');
    Route::post('/packages', [AffiliatePortalController::class, 'savePackages'])->name('packages.save');
    Route::get('/settings', [AffiliatePortalController::class, 'settings'])->name('settings');
    Route::post('/settings', [AffiliatePortalController::class, 'updateSettings'])->name('settings.update');
    Route::get('/wallet', [AffiliatePortalController::class, 'wallet'])->name('wallet');
    // Withdraw
    Route::get('/withdraw', [WithdrawController::class, 'index'])->name('withdraw');
    Route::post('/withdraw/request', [WithdrawController::class, 'storeRequest'])->name('withdraw.request');
    Route::post('/withdraw/methods', [WithdrawController::class, 'storeMethod'])->name('withdraw.methods.store');
    Route::post('/withdraw/methods/{id}/delete', [WithdrawController::class, 'destroyMethod'])->name('withdraw.methods.destroy');
    Route::post('/withdraw/methods/{id}/default', [WithdrawController::class, 'setDefaultMethod'])->name('withdraw.methods.default');
});

Route::group(['prefix'=> 'entertainer-portal', 'as' => 'entertainer.portal.', 'middleware' => ['auth', 'image.upload.guard']], function () {
    Route::get('/dashboard', [EntertainerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/packages', [EntertainerPortalController::class, 'packages'])->name('packages');
    Route::post('/packages', [EntertainerPortalController::class, 'savePackages'])->name('packages.save');
    Route::get('/settings', [EntertainerPortalController::class, 'settings'])->name('settings');
    Route::post('/settings', [EntertainerPortalController::class, 'updateSettings'])->name('settings.update');
    Route::get('/wallet', [EntertainerPortalController::class, 'wallet'])->name('wallet');
    // Withdraw
    Route::get('/withdraw', [WithdrawController::class, 'index'])->name('withdraw');
    Route::post('/withdraw/request', [WithdrawController::class, 'storeRequest'])->name('withdraw.request');
    Route::post('/withdraw/methods', [WithdrawController::class, 'storeMethod'])->name('withdraw.methods.store');
    Route::post('/withdraw/methods/{id}/delete', [WithdrawController::class, 'destroyMethod'])->name('withdraw.methods.destroy');
    Route::post('/withdraw/methods/{id}/default', [WithdrawController::class, 'setDefaultMethod'])->name('withdraw.methods.default');
});

// Payment Logo routes (outside admin group for direct access)
Route::post('/payment-logos', [PaymentLogoController::class, 'store'])->middleware('image.upload.guard')->name('payment-logos.store');
Route::delete('/payment-logos/{id}', [PaymentLogoController::class, 'destroy'])->name('payment-logos.destroy');

// Telnyx Webhook routes (SMS delivery notifications - no CSRF needed)
Route::post('/webhooks/telnyx/sms', [TelnyxWebhookController::class, 'handleSmsWebhook'])->name('telnyx.webhook.sms');

// Frontend catch-all route with slug parameter
// Keep this at the very end so it does not shadow admin/auth/portal routes.
Route::get('/{slug}', [FrontendController::class, 'index'])->name('index');
