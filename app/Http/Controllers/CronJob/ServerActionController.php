<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Library\TelegramSdk;
use App\Models\Recharge;
use Illuminate\Support\Facades\Artisan;

class ServerActionController extends Controller
{
    public function serverAction(Request $request, $action)
    {

        if ($action === 'recharge-sendser') {
            $command = 'server:recharge-notification --domain=' . $request->domain;
            $this->runCommand($command);
        }

        if($action === 'log-clear') {
            $command = 'log:clear';
            $this->runCommand($command);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Server action is running'
        ]);

    }

    private function runCommand($command)
    {
        Artisan::call($command);
    }
}
