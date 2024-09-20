<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use App\Models\ConfigSite;
use App\Models\User;
use Illuminate\Http\Request;

class UserCronJobController extends Controller
{
    public function checkLevel(Request $request)
    {
        $level = $request->level;
        // if ($level === 'member') {
        $users = User::where('level', 'member')->limit(500)->get();
        foreach ($users as $user) {
            $config = ConfigSite::where('domain', $user->domain)->first();
            if ($config) {
                $min_collaborator = $config->collaborator;
                $min_agency = $config->agency;
                $min_distributor = $config->distributor;

                if ($user->total_recharge >= $min_collaborator) {
                    $user->level = 'collaborator';
                    $user->save();
                }
            }
        }
        // }

        // if ($level === 'collaborator') {
        $users = User::where('level', 'collaborator')->limit(500)->get();
        foreach ($users as $user) {
            $config = ConfigSite::where('domain', $user->domain)->first();
            if ($config) {
                $min_agency = $config->agency;
                $min_distributor = $config->distributor;

                if ($user->total_recharge >= $min_agency) {
                    $user->level = 'agency';
                    $user->save();
                }
            }
        }
        // }

        // if ($level === 'agency') {
        $users = User::where('level', 'agency')->limit(500)->get();
        foreach ($users as $user) {
            $config = ConfigSite::where('domain', $user->domain)->first();
            if ($config) {
                $min_distributor = $config->distributor;
                if ($user->total_recharge >= $min_distributor) {
                    $user->level = 'distributor';
                    $user->save();
                }
            }
        }
        // }
    }
}
