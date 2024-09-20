<?php

use App\Http\Controllers\Admin\DataAdminController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PartnerWebsiteController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServicePlatformController;
use App\Http\Controllers\Admin\ServiceServerController;
use App\Http\Controllers\Admin\TelegramController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ViewAdminController;
use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Guard\AccountController;
use App\Http\Controllers\Guard\PaymentController as GuardPaymentController;
use App\Http\Controllers\Guard\TicketController;
use App\Http\Controllers\Guard\ViewGuardController;
use App\Http\Controllers\Guard\WebSiteController;
use App\Http\Controllers\Service\ViewServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;

Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialController::class, 'handleGoogleCallback']);

Route::get('auth/facebook', [SocialController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('auth/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

Route::prefix('site')->middleware(['installSite'])->group(function () {
    Route::get('install', [AuthenticateController::class, 'viewInstall'])->name('install');
    Route::post('install', [AuthenticateController::class, 'install'])->name('install.post');
});

Route::get('auth/logout', [AuthenticateController::class, 'logout'])->name('logout');
Route::prefix('auth')->middleware(['guest', 'xss'])->group(function () {
    Route::get('login', [AuthenticateController::class, 'viewLogin'])->name('login');
    Route::get('register', [AuthenticateController::class, 'viewRegister'])->name('register');

    Route::post('login', [AuthenticateController::class, 'login'])->name('login.post');
    Route::post('register', [AuthenticateController::class, 'register'])->name('register.post');
});


Route::get('/', function () {
    return view('landing');
    // return abort(404);
    // return redirect()->route('home');
})->name('landing');

Route::prefix('/')->group(function () {
    Route::get('home', [ViewGuardController::class, 'viewHome'])->name('home');

    Route::prefix('account')->middleware(['auth'])->group(function () {
        Route::get('profile', [ViewGuardController::class, 'viewProfile'])->name('account.profile');
        Route::get('recharge', [ViewGuardController::class, 'viewRecharge'])->name('account.recharge');
        Route::get('recharge/card', [ViewGuardController::class, 'viewRechargeCard'])->name('account.recharge.card');
        Route::get('transactions', [ViewGuardController::class, 'viewTransactions'])->name('account.transactions');
        Route::get('progress', [ViewGuardController::class, 'viewProgress'])->name('account.progress');
        Route::get('services', [ViewGuardController::class, 'viewServices'])->name('account.services');

        Route::post('change-password', [AccountController::class, 'changePassword'])->name('account.change-password');
        Route::post('two-factor-auth', [AccountController::class, 'twoFactorAuth'])->name('account.two-factor-auth');
        Route::post('two-factor-auth-disable', [AccountController::class, 'twoFactorAuthDisable'])->name('account.two-factor-auth-disable');
        Route::get('reload-user-token', [AccountController::class, 'reloadUserToken'])->name('account.reload-user-token');
        Route::post('update/status-telegram', [AccountController::class, 'updateStatusTelegram'])->name('account.update.status-telegram');
        Route::post('recharge/card', [AccountController::class, 'rechargeCard'])->name('account.recharge.card.post');
    });

    Route::get('ticket', [TicketController::class, 'viewTicket'])->name('ticket')->middleware(['auth']);
    Route::post('ticket/create', [TicketController::class, 'createTicket'])->name('ticket.create')->middleware(['auth']);
    Route::get('ticket/detail/{id}', [TicketController::class, 'viewTicketDetail'])->name('ticket.detail')->middleware(['auth']);

    Route::get('create/website', [WebSiteController::class, 'viewCreateWebsite'])->name('create.website')->middleware(['auth']);
    Route::post('create/website', [WebSiteController::class, 'createWebsite'])->name('create.website.post')->middleware(['auth']);

    Route::get('service/{platform}/{service}', [ViewServiceController::class, 'viewService'])->name('service');

    // payment bill
    Route::get('/payment/bill/{code}', [GuardPaymentController::class, 'viewBill'])->name('payment.bill')->middleware(['auth']);
    Route::post('/create/bill', [GuardPaymentController::class, 'createBill'])->name('create.bill')->middleware(['auth']);
    Route::get('/payment/check/{code}', [GuardPaymentController::class, 'checkPayment'])->name('payment.check')->middleware(['auth']);

    // level checking
    Route::get('/level/check', [AccountController::class, 'checkLevel'])->name('level.check')->middleware(['auth']);
});

// admin
Route::prefix('admin')->middleware(['isAdmin'])->group(function () {
    Route::get('dashboard', [ViewAdminController::class, 'viewDashboard'])->name('admin.dashboard');
    Route::get('website/config', [ViewAdminController::class, 'viewWebsiteConfig'])->name('admin.website.config');

    Route::get('notify/system', [NotificationController::class, 'viewSystemNotify'])->name('admin.notify.system');
    Route::post('notify/system/create', [NotificationController::class, 'createSystemNotify'])->name('admin.notify.system.create');
    Route::get('notify/system/delete/{id}', [NotificationController::class, 'deleteSystemNotify'])->name('admin.notify.system.delete');
    Route::get('notify/service', [NotificationController::class, 'viewServiceNotify'])->name('admin.notify.service');
    Route::post('notify/service/create', [NotificationController::class, 'createServiceNotify'])->name('admin.notify.service.create');
    Route::get('notify/service/delete/{id}', [NotificationController::class, 'deleteServiceNotify'])->name('admin.notify.service.delete');

    Route::get('telegram/config', [TelegramController::class, 'viewTelegramConfig'])->name('admin.telegram.config');
    Route::get('telegram/set-webhook', [TelegramController::class, 'setWebhook'])->name('admin.telegram.set-webhook');

    Route::get('payment/config', [PaymentController::class, 'viewPaymentConfig'])->name('admin.payment.config');
    Route::post('payment/config/update', [PaymentController::class, 'updatePaymentConfig'])->name('admin.payment.config.update');
    Route::post('payment/update/{bank_name}', [PaymentController::class, 'updatePayment'])->name('admin.payment.update');
    Route::post('payment/promotion/create', [PaymentController::class, 'createPromotion'])->name('admin.payment.promotion.create');
    Route::get('payment/promotion/edit/{id}', [PaymentController::class, 'viewEditPromotion'])->name('admin.payment.promotion.edit');
    Route::post('payment/promotion/update/{id}', [PaymentController::class, 'updatePromotion'])->name('admin.payment.promotion.update');
    Route::get('payment/promotion/delete/{id}', [PaymentController::class, 'deletePromotion'])->name('admin.payment.promotion.delete');

    // website con
    if (request()->getHost() === env('APP_MAIN_SITE')) {
        Route::get('website/partner', [PartnerWebsiteController::class, 'viewPartnerWebsite'])->name('admin.website.partner');
        Route::get('website/partner/edit/{id}', [PartnerWebsiteController::class, 'viewEditPartnerWebsite'])->name('admin.website.partner.edit');
        Route::post('website/partner/update/{id}', [PartnerWebsiteController::class, 'updatePartnerWebsite'])->name('admin.website.partner.update');
        // active
        Route::get('website/partner/active/{id}', [PartnerWebsiteController::class, 'activePartnerWebsite'])->name('admin.website.partner.active');
        Route::get('website/partner/delete/{id}', [PartnerWebsiteController::class, 'deletePartnerWebsite'])->name('admin.website.partner.delete');


        // dịch vụ & và nền tảng
        Route::get('service/platform', [ServicePlatformController::class, 'viewServicePlatform'])->name('admin.service.platform');
        Route::post('service/platform/create', [ServicePlatformController::class, 'createServicePlatform'])->name('admin.service.platform.create');
        Route::get('service/platform/edit/{id}', [ServicePlatformController::class, 'viewEditServicePlatform'])->name('admin.service.platform.edit');
        Route::post('service/platform/update/{id}', [ServicePlatformController::class, 'updateServicePlatform'])->name('admin.service.platform.update');
        Route::get('service/platform/delete/{id}', [ServicePlatformController::class, 'deleteServicePlatform'])->name('admin.service.platform.delete');
        // -- dịch vụ
        Route::get('service', [ServiceController::class, 'viewService'])->name('admin.service');
        Route::post('service/create', [ServiceController::class, 'createService'])->name('admin.service.create');
        Route::get('service/edit/{id}', [ServiceController::class, 'viewEditService'])->name('admin.service.edit');
        Route::post('service/update/{id}', [ServiceController::class, 'updateService'])->name('admin.service.update');
        Route::get('service/delete/{id}', [ServiceController::class, 'deleteService'])->name('admin.service.delete');

        Route::get('service/smm/list', [ServiceController::class, 'viewSmmService'])->name('admin.service.smm');
        Route::post('service/smm/create', [ServiceController::class, 'createSmmService'])->name('admin.service.smm.create');
        Route::get('service/smm/edit/{id}', [ServiceController::class, 'viewEditSmmService'])->name('admin.service.smm.edit');
        Route::post('service/smm/update/{id}', [ServiceController::class, 'updateSmmService'])->name('admin.service.smm.update');
        Route::get('service/smm/delete/{id}', [ServiceController::class, 'deleteSmmService'])->name('admin.service.smm.delete');

        // -- Máy chủ
        Route::post('/service/server/create', [ServiceServerController::class, 'createServer'])->name('admin.server.create');
        Route::get('/service/server/delete/{id}', [ServiceServerController::class, 'deleteServer'])->name('admin.server.delete');


    }


    Route::get('/service/server', [ServiceServerController::class, 'viewServer'])->name('admin.server');
    Route::get('/service/server/edit/{id}', [ServiceServerController::class, 'viewEditServer'])->name('admin.server.edit');
    Route::post('/service/server/update/{id}', [ServiceServerController::class, 'updateServer'])->name('admin.server.update');
    // update price
    Route::post('/service/server/update-price', [ServiceServerController::class, 'updatePrice'])->name('admin.server.update-price');
    Route::post('service/price/update', [ServiceServerController::class, 'updateServicePrice'])->name('admin.service.price.update');

    // user management
    Route::get('users', [UserController::class, 'viewUser'])->name('admin.user');
    Route::get('user/{id}', [UserController::class, 'viewUserDetail'])->name('admin.user.detail');
    Route::post('user/update-lbd/{username}', [UserController::class, 'updateUser'])->name('admin.user.update');
    Route::post('user/update-password/{username}', [UserController::class, 'updatePassword'])->name('admin.user.update-password');
    Route::get('user/delete/{id}', [UserController::class, 'deleteUser'])->name('admin.user.delete');
    Route::get('user/edit/balance', [UserController::class, 'viewUserBalance'])->name('admin.user.balance');
    Route::post('user/update/balance', [UserController::class, 'updateUserBalance'])->name('admin.user.update-balance');
    Route::get('user/transactions/{username}', [UserController::class, 'viewUserTransactions'])->name('admin.user.transactions');


    Route::post('website/update', [DataAdminController::class, 'updateWebsiteConfig'])->name('admin.website.update');

    Route::get('history/user', [HistoryController::class, 'viewHistoryUser'])->name('admin.history.user');
    Route::get('history/orders', [HistoryController::class, 'viewHistoryOrders'])->name('admin.history.orders');
    Route::get('history/payment', [HistoryController::class, 'viewHistoryPayment'])->name('admin.history.payment');
    Route::get('history/transactions', [HistoryController::class, 'viewHistoryTransactions'])->name('admin.history.transactions');
    Route::get('order/edit/{id}', [HistoryController::class, 'viewEditOrder'])->name('admin.order.edit');

    Route::post('/order/update/{id}', [HistoryController::class, 'orderAction'])->name('admin.order.action');
    Route::get('/order/delete/{id}', [HistoryController::class, 'deleteOrder'])->name('admin.order.delete');

});
