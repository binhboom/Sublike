<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BankResource;
use App\Http\Resources\Api\PlatformServiceApiResource;
use App\Http\Resources\Document\ServicesResource;
use App\Http\Resources\Document\ServicesWithServersResources;
use App\Models\Banking;
use App\Models\ConfigSite;
use App\Models\Service;
use App\Models\ServicePlatform;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function getBank(Request $request)
    {
        $banks = Banking::where('status', 'active')->where('domain', $request->getHost())->get();

        $config = [
            'promotion' => site('percent_promotion') ?? 0,
            'transfer_code' => site('transfer_code') . '' . $request->user->id,
        ];

        return response()->json([
            'configs' => $config,
            'banks' => BankResource::collection($banks),
        ]);
    }

    public function getSite(Request $request)
    {
        $config = ConfigSite::where('domain', $request->getHost())->first();
        return response()->json([
            'status' => 'success',
            'data' => $config,
        ]);
    }

    public function getMeta(Request $request)
    {
        $config = ConfigSite::where('domain', $request->getHost())->first();
        return response()->json([
            'status' => 'success',
            'message' => 'Lấy dữ liệu thành công',
            'data' => [
                'site' => $config->name_site,
                'title' => $config->title,
                'description' => $config->description,
                'keywords' => $config->keywords,
                'author' => $config->author,
                'thumbnail' => $config->thumbnail,
                'logo' => $config->logo,
                'favicon' => $config->favicon,
                'facebook' => $config->facebook,
                'zalo' => $config->zalo,
                'telegram' => $config->telegram,
                'url' => $config->url,
                'notice' => $config->notice,
                'collaborator' => $config->collaborator,
                'agency' => $config->agency,
                'distributor' => $config->distributor,
                'nameserver_1' => $config->nameserver_1,
                'nameserver_2' => $config->nameserver_2,
            ],
        ]);
    }

    public function getServices(Request $request)
    {
        $platforms = ServicePlatform::where('status', 'active')->where('domain', env('APP_MAIN_SITE'))->get();
        return response()->json([
            'code' => '200',
            'status' => 'success',
            'data' => PlatformServiceApiResource::collection($platforms),
        ]);
    }
}
