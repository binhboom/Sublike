<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Recharge;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ViewAdminController extends Controller
{
    public function viewDashboard()
    {
        $totalUser = User::where('domain', request()->getHost())->count();
        $totalBalance = User::where('domain', request()->getHost())->sum('balance');
        $totalRecharge = User::where('domain', request()->getHost())->sum('total_recharge');
        $totalUserToday = User::where('domain', request()->getHost())->whereDate('created_at', Carbon::today())->count();
        $totalRevenue = Recharge::where('domain', request()->getHost())->sum('real_amount');
        $totalRefund = Transaction::where('domain', request()->getHost())->where('type', 'refund')->sum('first_balance');
        $totalCanceled = Order::where('domain', request()->getHost())->where('status', 'Cancelled')->count();
        $totalRechargeToday = Recharge::where('domain', request()->getHost())->whereDate('created_at', Carbon::today())->sum('real_amount');

        // static chart label tháng recharge and order
        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = 'Tháng ' . $i;
            $data['recharge'][] = Recharge::where('domain', request()->getHost())->whereMonth('created_at', $i)->sum('real_amount');
            $data['order'][] = Order::where('domain', request()->getHost())->whereMonth('created_at', $i)->sum('payment');
            $data['user'][] = User::where('domain', request()->getHost())->whereMonth('created_at', $i)->count();
        }



        return view('admin.dashboard', compact('totalUser', 'totalBalance', 'totalRecharge', 'totalUserToday', 'totalRevenue', 'totalRefund', 'totalCanceled', 'totalRechargeToday', 'labels', 'data'));
    }

    public function viewWebsiteConfig()
    {
        return view('admin.website.config');
    }
}
