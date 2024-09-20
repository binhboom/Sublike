<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\TelegramSdk;
use App\Models\ConfigSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataAdminController extends Controller
{
    public function updateWebsiteConfig(Request $request)
    {
        $site_config = ConfigSite::where('domain', request()->getHost())->where('status', 'active')->first();

        foreach ($request->all() as $key => $value) {
            if ($key != '_token') {
                $site_config->$key = $value;
            }
        }

        $site_config->save();

        return redirect()->back()->with('success', __('Website config updated'));
    }
}
