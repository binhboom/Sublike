<?php

use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\Client\AccountController;
use App\Http\Controllers\Api\Client\ConfigController;
use App\Http\Controllers\Api\Client\NotificationController;
use App\Http\Controllers\Api\Client\ServiceApiController;
use App\Http\Controllers\Api\Document\ApiDocumentController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Service\TraodoisubController;
use App\Http\Controllers\CronJob\PriceServiceController;
use App\Http\Controllers\CronJob\RechargeCronJobController;
use App\Http\Controllers\CronJob\ServerActionController;
use App\Http\Controllers\CronJob\StatusOrderServiceController;
use App\Http\Controllers\CronJob\TelegramController;
use App\Http\Controllers\CronJob\UserCronJobController;
use App\Http\Controllers\Tool\ToolController;
use App\Library\CloudflareController;
use App\Library\CpanelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Library\CpanelSdkV2;

use function Pest\Laravel\json;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthApiController::class, 'doLogin'])->name('api.auth.login');
        Route::post('register', [AuthApiController::class, 'doRegister'])->name('api.auth.register');
    });

    Route::prefix('account')->middleware('api.auth')->group(function () {
        Route::get('me', [AccountController::class, 'getMe'])->name('api.account.me');
        Route::post('change-password', [AccountController::class, 'changePassword'])->name('api.account.change-password');
        Route::get('activities', [AccountController::class, 'getActivities'])->name('api.account.activities');
        Route::get('recharges', [AccountController::class, 'getRecharges'])->name('api.account.recharges');
        Route::get('transaction', [AccountController::class, 'getTransaction'])->name('api.account.transaction');
        Route::get('orders', [AccountController::class, 'getOrders'])->name('api.account.orders');
        Route::post('refresh-api-token', [AccountController::class, 'refreshApiToken'])->name('api.account.refresh-api-token');
        Route::get('get/domain', [AccountController::class, 'getWebsite'])->name('api.account.get-website');
        Route::post('update/domain', [AccountController::class, 'updateWebsite'])->name('api.account.update-website');

        // create bill
        Route::post('recharge/create-bill', [AccountController::class, 'createBill'])->name('api.account.create-bill');
        Route::get('payment/bill/{code}', [AccountController::class, 'paymentBill'])->name('api.account.payment-bill');
        Route::get('payment/check/{code}', [AccountController::class, 'checkPayment'])->name('api.account.check-payment');
    });


    Route::prefix('config')->middleware('api.auth')->group(function () {
        Route::get('bank', [ConfigController::class, 'getBank'])->name('api.config.bank');
        Route::get('site', [ConfigController::class, 'getSite'])->name('api.config.site');
        Route::get('level', [ConfigController::class, 'getLevel'])->name('api.config.level');
        Route::get('services', [ConfigController::class, 'getServices'])->name('api.config.services');
    });

    Route::prefix('settings')->group(function(){
        Route::get('notifications', [NotificationController::class, 'getNotifications'])->name('api.settings.notifications');
    });

    Route::get('meta', [ConfigController::class, 'getMeta'])->name('api.config.meta');

    Route::get('services', [ServiceApiController::class, 'getServices'])->name('api.client.services');
    Route::get('service/{platform}/{service}', [ServiceApiController::class, 'getService'])->name('api.client.service');
    Route::get('servers', [ServiceApiController::class, 'getServers'])->name('api.client.servers');
    Route::post('server/total-pay', [ServiceApiController::class, 'getTotalPaymentServer'])->name('api.client.server.total-payment');

    // tạo đơn
    Route::post('start/create/order', [OrderController::class, 'createOrder'])->name('api.create.order')->middleware('xss');
    Route::post('order/refund', [OrderController::class, 'refundOrder'])->name('api.refund.order')->middleware('xss');
    Route::post('order/warranty', [OrderController::class, 'warrantyOrder'])->name('api.warranty.order')->middleware('xss');
    Route::post('order/update', [OrderController::class, 'updateOrder'])->name('api.update.order')->middleware('xss');
    Route::post('order/renews', [OrderController::class, 'renewOrder'])->name('api.renew.order')->middleware('xss');

    Route::get('payment/{code}', [RechargeCronJobController::class, 'payment'])->name('api.payment');
    /* cronJobs */
    Route::prefix('cron-job')->group(function () {
        Route::get('status/service/subgiare', [StatusOrderServiceController::class, 'cronJobStatusServiceSubgiare'])->name('cron-job.status.service.subgiare');
        Route::get('status/service/trumsubre', [StatusOrderServiceController::class, 'cronJobStatusServiceTrumsubre'])->name('cron-job.status.service.trumsubre');
        Route::get('status/service/2mxh', [StatusOrderServiceController::class, 'cronJobStatusService2mxh'])->name('cron-job.status.service.2mxh');
        Route::get('status/service/baostar', [StatusOrderServiceController::class, 'cronJobStatusServiceBaostar'])->name('cron-job.status.service.baostar');
        Route::get('status/service/boosterviews', [StatusOrderServiceController::class, 'cronJobStatusServiceBoosterviews'])->name('cron-job.status.service.boosterviews');
        Route::get('status/service/smmking', [StatusOrderServiceController::class, 'cronJobStatusServiceSmmking'])->name('cron-job.status.service.smmking');
        Route::get('status/service/hacklike17', [StatusOrderServiceController::class, 'cronJobStatusServiceHacklike17'])->name('cron-job.status.service.hacklike17');
        Route::get('status/service/cheotuongtac', [StatusOrderServiceController::class, 'cronJobStatusServiceCheotuongtac'])->name('cron-job.status.service.cheotuongtac');
        Route::get('status/service/tuongtacsale', [StatusOrderServiceController::class, 'cronJobStatusServiceTuongtacsale'])->name('cron-job.status.service.tuongtacsale');
        Route::get('status/service/smmgen', [StatusOrderServiceController::class, 'cronJobStatusServiceSmmgen'])->name('cron-job.status.service.smmgen');
        Route::get('status/service/smmcoder', [StatusOrderServiceController::class, 'cronJobStatusServiceSmmcoder'])->name('cron-job.status.service.smmcoder');
        Route::get('status/service/smmfollows', [StatusOrderServiceController::class, 'cronJobStatusServiceSmmFollows'])->name('cron-job.status.service.smmfollows');
        Route::get('status/service/autolikez', [StatusOrderServiceController::class, 'cronJobStatusServiceAutolikez'])->name('cron-job.status.service.autolikez');
        Route::get('status/service/tds', [StatusOrderServiceController::class, 'cronJobStatusServiceTds'])->name('cron-job.status.service.tds');
        Route::get('status/service/smm/{name}', [StatusOrderServiceController::class, 'cronJobStatusServiceSmm'])->name('cron-job.status.service.smm');

        Route::get('server/action/{action}', [ServerActionController::class, 'serverAction'])->name('cron-job.server.action');
        Route::get('recharge-card/status', [RechargeCronJobController::class, 'rechargeCardStatus'])->name('cron-job.recharge-card.status');
    });
    Route::prefix('tools')->group(function () {
        Route::get('get-uid', [ToolController::class, 'getUid'])->name('tools.get-uid');
    });
    // check price service
    Route::get('price-service/{service}', [PriceServiceController::class, 'checkPriceService'])->name('api.price-service');
    Route::get('price-update-service', [PriceServiceController::class, 'updatePriceService'])->name('api.price-update-service');
    #checking level
    Route::get('check-level', [UserCronJobController::class, 'checkLevel'])->name('api.check-level');
    #check bill expired
    Route::get('check-bill-expired', [RechargeCronJobController::class, 'checkBillExpired'])->name('api.check-bill-expired');

    Route::get('update/orders', [StatusOrderServiceController::class, 'updateOrders'])->name('api.update.orders');
});

Route::prefix('d')->group(function () {
    Route::get('get/me', [ApiDocumentController::class, 'getMe'])->name('api-document.get-me');

    Route::prefix('services')->group(function () {
        Route::get('/', [ApiDocumentController::class, 'getServices'])->name('api-document.get-services');
        Route::get('servers', [ApiDocumentController::class, 'getServersByServices'])->name('api-document.get-servers');
        Route::get('{id}', [ApiDocumentController::class, 'getServiceById'])->name('api-document.get-service');
    });

    Route::prefix('servers')->group(function () {
        Route::get('/', [ApiDocumentController::class, 'getServers'])->name('api-document.get-servers');
        Route::get('{id}', [ApiDocumentController::class, 'getServerById'])->name('api-document.get-server');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [ApiDocumentController::class, 'getOrders'])->name('api-document.get-orders');
        Route::get('{id}', [ApiDocumentController::class, 'getOrderById'])->name('api-document.get-order');
    });
});

Route::prefix('telegram')->group(function () {
    // get webhook info
    Route::get('get-webhook-info', function () {
        $telegram = new App\Library\TelegramSdk();
        $response = $telegram->botNotify()->getWebhookInfo();
        dd($response);
    });

    // remove webhook
    Route::get('remove-webhook', function () {
        $telegram = new App\Library\TelegramSdk();
        $response = $telegram->botNotify()->removeWebhook();
        dd($response);
    });

    // webhook
    Route::any('weere', [TelegramController::class, 'callbackData'])->name('telegram.set-webhook');
});
