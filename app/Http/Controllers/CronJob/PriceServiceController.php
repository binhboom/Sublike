<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Api\Service\BoosterviewsController;
use App\Http\Controllers\Api\Service\CheoTuongTacController;
use App\Http\Controllers\Api\Service\SmmCustomController;
use App\Http\Controllers\Api\Service\TuongTacSaleController;
use App\Http\Controllers\Controller;
use App\Models\ServerAction;
use App\Models\ServiceServer;
use App\Models\SmmPanelPartner;
use App\Models\User;
use Illuminate\Http\Request;

class PriceServiceController extends Controller
{
    public function checkPriceService($service)
    {
        if ($service === 'boosterviews') {

            $boosterviews = new BoosterviewsController();
            $services = $boosterviews->services();
            if (isset($services)) {
                foreach ($services as $service) {
                    $service_id = $service['service'];
                    $price = $service['rate'];
                    $servers = ServiceServer::where('domain', env('APP_MAIN_SITE'))->where('providerServer', $service_id)->get();
                    foreach ($servers as $server) {
                        if ($server->price_update !== $price) {
                            $server->price_update = $price;
                            $server->price_member = $price + ($price * 5 / 100); // 5%
                            $server->price_collaborator = $price + ($price * 5 / 100); // 5%
                            $server->price_agency = $price + ($price * 5 / 100); // 5%
                            $server->price_distributor = $price + ($price * 5 / 100); // 5%
                            $server->status = 'active'; // set status to 'inactive' to force user to update price
                            $server->save();
                        }
                    }
                }
            }
        } elseif ($service === 'cheotuongtac') {
            $cheotuongtac = new CheoTuongTacController();
            $services = $cheotuongtac->services();
            if (isset($services)) {
                foreach ($services as $service) {
                    $service_id = $service['service'];
                    $price = $service['rate'];
                    $servers = ServiceServer::where('domain', env('APP_MAIN_SITE'))->where('providerServer', $service_id)->get();
                    foreach ($servers as $server) {
                        if ($server->price_update !== $price) {
                            $server->price_update = $price;
                            $server->price_member = $price + ($price * 5 / 100); // 5%
                            $server->price_collaborator = $price + ($price * 5 / 100); // 5%
                            $server->price_agency = $price + ($price * 5 / 100); // 5%
                            $server->price_distributor = $price + ($price * 5 / 100); // 5%
                            $server->status = 'active'; // set status to 'inactive' to force user to update price
                            $server->save();
                        }
                    }
                }
            }
        } elseif ($service === 'tuongtacsale') {
            $tuongtacsale = new TuongTacSaleController();
            $services = $tuongtacsale->services();
            if (isset($services)) {
                foreach ($services as $service) {
                    $service_id = $service['service'];
                    $price = $service['rate'];
                    $servers = ServiceServer::where('domain', env('APP_MAIN_SITE'))->where('providerServer', $service_id)->get();
                    foreach ($servers as $server) {
                        if ($server->price_update !== $price) {
                            $server->price_update = $price;
                            $server->price_member = $price + ($price * 35 / 100); // 5%
                            $server->price_collaborator = $price + ($price * 30 / 100); // 5%
                            $server->price_agency = $price + ($price * 27 / 100); // 5%
                            $server->price_distributor = $price + ($price * 25 / 100); // 5%
                            $server->status = 'active'; // set status to 'inactive' to force user to update price
                            $server->save();
                        }
                    }
                }
            }
        } else {
            $smmcheck = SmmPanelPartner::where('domain', env('APP_MAIN_SITE'))->where('name', $service)->first();
            if ($smmcheck) {

                $smm = new SmmCustomController();
                $smm->api_url = $smmcheck->url_api;
                $smm->api_key = $smmcheck->api_token;
                $services = $smm->services();
                if (isset($services)) {
                    foreach ($services as $service) {
                        $service_id = $service['service'];
                        $price = $service['rate'];
                        $servers = ServiceServer::where('domain', env('APP_MAIN_SITE'))->where('providerServer', $service_id)->get();
                        $price_vnd = $price * 26;
                        foreach ($servers as $server) {
                            if ($server->price_update !== $price_vnd) {
                                
                                $price_member = $price_vnd + ($smmcheck->price_update /100) * $price_vnd;
                                
                                $server->price_update = $price_vnd;
                                
                                $server->price_member = $price_vnd + ($smmcheck->price_update /100) * $price_vnd; // 5%
                                $server->price_collaborator = $price_vnd + ($smmcheck->price_update /100) * $price_vnd; // 5%
                                $server->price_agency = $price_vnd + ($smmcheck->price_update /100) * $price_vnd; // 5%
                                $server->price_distributor = $price_vnd + ($smmcheck->price_update /100) * $price_vnd; // 5%
                                $server->status = 'active'; // set status to 'inactive' to force user to update price
                                $server->save();
                            }
                        }
                    }
                }

                // $servers = ServiceServer::where('domain', env('APP_MAIN_SITE'))->where('providerName', $service)->get();
                // foreach ($servers as $server) {
                //     $providerServer = $server->providerServer;
                //     $smm = new SmmCustomController();
                //     $smm->api_url = $smmcheck->url_api;
                //     $smm->api_key = $smmcheck->api_token;
                //     $services = $smm->services();
                //     foreach ($services as $service) {
                //         $service_id = $service['service'];
                //         $price = $service['rate'];

                //         if ($service_id === $providerServer) {
                //             if ($server->price_update !== $price) {
                //                 // $server->price_update = $price;
                //                 // $server->status = 'inactive'; // set status to 'inactive' to force user to update price
                //                 // $server->save();
                //                 if ($smmcheck->update_price == 'on') {
                //                     $price_update = $smmcheck->price_update; // % cập nhật giá
                //                     $price = $price + ($price * $price_update / 100);
                //                     $server->price_update = $price;
                //                     $server->status = 'active'; // set status to 'inactive' to force user to update price
                //                     $server->save();
                //                 }
                //             }
                //         }
                //     }
                // }
            }
        }
    }

    public function updatePriceService(Request $request)
    {
        $servers = ServiceServer::where('domain', site('is_domain'))->get();
        foreach ($servers as $server) {
            $serverExist = ServiceServer::where('package_id', $server->package_id)->where('service_id', $server->service_id)->where('domain', request()->getHost())->first();
            if ($serverExist) {
                $admin = site('admin_username') ?? 1;
                $user = User::where('username', $admin)->where('domain', site('is_domain'))->first();
                if ($user) {
                    $percentMember = site('percent_member') ?? 5;
                    $percentCollaborator = site('percent_collaborator') ?? 5;
                    $percentAgency = site('percent_agency') ?? 5;
                    $percentDistributor = site('percent_distributor') ?? 5;

                    $priceCurrent = $server->levelPrice($user->level);

                    $priceMember = $priceCurrent * ($percentMember / 100);
                    $priceCollaborator = $priceCurrent * ($percentCollaborator / 100);
                    $priceAgency = $priceCurrent * ($percentAgency / 100);
                    $priceDistributor = $priceCurrent * ($percentDistributor / 100);
                    $priceMember = $priceCurrent + $priceMember;
                    $priceCollaborator = $priceCurrent + $priceCollaborator;
                    $priceAgency = $priceCurrent + $priceAgency;
                    $priceDistributor = $priceCurrent + $priceDistributor;

                    // if ($server->name != $serverExist->name || $server->details != $serverExist->details) {
                    $serverExist->update([
                        'name' => $server->name,
                        'details' => $server->details,
                        'min' => $server->min,
                        'max' => $server->max,
                        'limit_day' => $server->limit_day,
                        'status' => $server->status,
                        'visibility' => $server->visibility,
                    ]);
                    // }

                    // if ($serverExist->price != $priceCurrent) {
                    $serverExist->update([
                        'price' => $serverExist->price_update,
                        'price_member' => $priceMember,
                        'price_collaborator' => $priceCollaborator,
                        'price_agency' => $priceAgency,
                        'price_distributor' => $priceDistributor,
                    ]);

                    // update action
                    $action = $server->action;
                    $ac = ServerAction::where('server_id', $serverExist->id)->where('domain', $request->getHost())->first();

                    if (!$ac) {
                        $ac = new ServerAction();
                        $ac->server_id = $serverExist->id;
                        echo "new";
                    }

                    $ac->update([
                        'get_uid' => $action->get_uid,
                        'quantity_status' => $action->quantity_status,
                        'reaction_status' => $action->reaction_status,
                        'reaction_data' => $action->reaction_data,
                        'comments_status' => $action->comments_status,
                        'comments_data' => $action->comments_data,
                        'minutes_status' => $action->minutes_status,
                        'minutes_data' => $action->minutes_data,
                        'time_status' => $action->time_status,
                        'time_data' => $action->time_data,
                        'posts_status' => $action->posts_status,
                        'posts_data' => $action->posts_data,
                        'refund_status' => $action->refund_status,
                        'warranty_status' => $action->warranty_status,
                    ]);

                    echo "done";
                    // }
                }
            } else {

                $admin = User::where('username', site('admin_username') ?? 1)->where('domain', site('is_domain'))->first();

                $percentMember = site('percent_member') ?? 5;
                $percentCollaborator = site('percent_collaborator') ?? 5;
                $percentAgency = site('percent_agency') ?? 5;
                $percentDistributor = site('percent_distributor') ?? 5;

                $priceCurrent = $server->levelPrice($admin->level);

                $priceMember = $priceCurrent * ($percentMember / 100);
                $priceCollaborator = $priceCurrent * ($percentCollaborator / 100);
                $priceAgency = $priceCurrent * ($percentAgency / 100);
                $priceDistributor = $priceCurrent * ($percentDistributor / 100);
                $priceMember = $priceCurrent + $priceMember;
                $priceCollaborator = $priceCurrent + $priceCollaborator;
                $priceAgency = $priceCurrent + $priceAgency;
                $priceDistributor = $priceCurrent + $priceDistributor;

                $new = new ServiceServer();
                $new->service_id = $server->service_id;
                $new->name = $server->name;
                $new->details = $server->details;
                $new->package_id = $server->package_id;
                $new->price = $server->levelPrice($admin->level);
                $new->price_update = $server->levelPrice($admin->level);
                $new->price_member = $priceMember;
                $new->price_collaborator = $priceCollaborator;
                $new->price_agency = $priceAgency;
                $new->price_distributor = $priceDistributor;
                $new->min = $server->min;
                $new->max = $server->max;
                $new->limit_day = $server->limit_day;
                $new->status = $server->status;
                $new->visibility = $server->visibility;
                $new->domain = request()->getHost();
                $new->updated_at = $server->updated_at;
                $new->save();

                // action
                $action = $server->action;
                $ac = new ServerAction();
                $ac->server_id = $new->id;
                $ac->get_uid = $action->get_uid;
                $ac->quantity_status = $action->quantity_status;
                $ac->reaction_status = $action->reaction_status;
                $ac->reaction_data = $action->reaction_data;
                $ac->comments_status = $action->comments_status;
                $ac->comments_data = $action->comments_data;
                $ac->minutes_status = $action->minutes_status;
                $ac->minutes_data = $action->minutes_data;
                $ac->time_status = $action->time_status;
                $ac->time_data = $action->time_data;
                $ac->posts_status = $action->posts_status;
                $ac->posts_data = $action->posts_data;
                $ac->refund_status = $action->refund_status;
                $ac->warranty_status = $action->warranty_status;
                $ac->domain = request()->getHost();
                $ac->save();
            }
        }

        // xoá các dịch vụ không còn tồn tại
        $servers = ServiceServer::where('domain', request()->getHost())->get();
        foreach ($servers as $server) {
            $serverExist = ServiceServer::where('package_id', $server->package_id)->where('service_id', $server->service_id)->where('domain', site('is_domain'))->first();
            if (!$serverExist) {
                $server->delete();
            }
        }
    }
}
