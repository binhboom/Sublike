<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Library\GoogleAuthenticator;
use App\Models\Banking;
use App\Models\Order;
use App\Models\Recharge;
use App\Models\RechargeCard;
use App\Models\RechargePromotion;
use App\Models\ServicePlatform;
use App\Models\Transaction;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;

class ViewGuardController extends Controller
{
    public function viewHome()
    {
        return view('guard.home');
    }

    public function viewProfile()
    {

        $ga = new GoogleAuthenticator();
        $secret = $ga->createSecret();

        $name = request()->getHost() . ':' . Auth::user()->username;
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($name, $secret);

        $user = Auth::user();

        if ($user->two_factor_auth !== 'yes') {
            $user->two_factor_secret = $secret;
            $user->save();
        }

        return view('guard.profile.index', compact('secret', 'qrCodeUrl'));
    }

    public function viewRecharge()
    {

        $momo = Banking::where('domain', request()->getHost())->where('bank_name', 'Momo')->first();
        $mbbank = Banking::where('domain', request()->getHost())->where('bank_name', 'MBBank')->first();
        $techcombank = Banking::where('domain', request()->getHost())->where('bank_name', 'Techcombank')->first();
        $acb = Banking::where('domain', request()->getHost())->where('bank_name', 'ACB')->first();
        $viettinbank = Banking::where('domain', request()->getHost())->where('bank_name', 'Viettinbank')->first();

        if (!$momo) {
            $momo = new Banking();
            $momo->bank_name = 'Momo';
            $momo->domain = request()->getHost();
            $momo->status = 'inactive';
            $momo->save();
        }

        if (!$mbbank) {
            $mbbank = new Banking();
            $mbbank->bank_name = 'MBBank';
            $mbbank->domain = request()->getHost();
            $mbbank->status = 'inactive';
            $mbbank->save();
        }

        if (!$techcombank) {
            $techcombank = new Banking();
            $techcombank->bank_name = 'Techcombank';
            $techcombank->domain = request()->getHost();
            $techcombank->status = 'inactive';
            $techcombank->save();
        }

        if (!$acb) {
            $acb = new Banking();
            $acb->bank_name = 'ACB';
            $acb->domain = request()->getHost();
            $acb->status = 'inactive';
            $acb->save();
        }


        if (!$viettinbank) {
            $acb = new Banking();
            $acb->bank_name = 'Viettinbank';
            $acb->domain = request()->getHost();
            $acb->status = 'inactive';
            $acb->save();
        }

        $rechargePromotions = RechargePromotion::where('domain', request()->getHost())->where('status', 'active')->get();

        $recharges = Recharge::where('domain', request()->getHost())->where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);

        return view('guard.recharge', compact('momo', 'mbbank', 'techcombank', 'acb', 'recharges', 'viettinbank', 'rechargePromotions'));
    }

    public function viewRechargeCard()
    {
        $rechargeCards = RechargeCard::where('domain', request()->getHost())->where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);

        return view('guard.recharge-card', compact('rechargeCards'));
    }

    public function viewStatistics()
    {
        return view('guard.profile.statistics');
    }

    public function viewTransactions()
    {

        $search = request()->search;

        $transactions = Transaction::where('user_id', Auth::id())->where('domain', request()->getHost())
            ->when($search, function ($query, $search) {
                return $query->where('tran_code', 'like', '%' . $search . '%');
            })->orderBy('id', 'desc')->paginate(10);

        return view('guard.profile.transactions', compact('transactions'));
    }

    public function viewProgress()
    {

        $search = request()->search;

        $orders = Order::where('user_id', Auth::id())
            ->when($search, function ($query, $search) {
                return $query->where('order_code', 'like', '%' . $search . '%');
            })->orderBy('id', 'desc')->paginate(10);

        return view('guard.profile.progress', compact('orders'));
    }

    public function viewServices()
    {

        $platforms = ServicePlatform::where('domain', request()->getHost())->get();

        return view('guard.profile.services', compact('platforms'));
    }
}
