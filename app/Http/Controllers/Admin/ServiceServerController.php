<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigSite;
use App\Models\ServerAction;
use App\Models\Service;
use App\Models\ServiceServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ServiceServerController extends Controller
{
    public function viewServer(Request $request)
    {
        if ($request->getHost() === env('APP_MAIN_SITE')) {
            $search = $request->search;
            $service = $request->service;
            $visibility = $request->visibility;
            $status = $request->status;

            $servers = ServiceServer::where('domain', request()->getHost())
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('id', 'like', '%' . $search . '%');
                })
                ->when($service, function ($query, $service) {
                    return $query->whereHas('service', function ($query) use ($service) {
                        $query->where('id', $service);
                    });
                })
                ->when($visibility, function ($query, $visibility) {
                    return $query->where('visibility', $visibility);
                })
                ->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->orderBy('id', 'DESC')->paginate(10);

            return view('admin.service.server', compact('servers'));
        } else {
            $search = $request->search;
            $service = $request->service;
            $visibility = $request->visibility;
            $status = $request->status;

            $servers = ServiceServer::where('domain', site('is_domain'))->get();

            foreach ($servers as $server) {
                $serverExist = ServiceServer::where('package_id', $server->package_id)->where('service_id', $server->service_id)->where('domain', request()->getHost())->first();

                $percentMember = site('percent_member') ?? 5;
                $percentCollaborator = site('percent_collaborator') ?? 5;
                $percentAgency = site('percent_agency') ?? 5;
                $percentDistributor = site('percent_distributor') ?? 5;

                $priceCurrent = $server->levelPrice(Auth::user()->level);
                $priceMember = $priceCurrent * ($percentMember / 100);
                $priceCollaborator = $priceCurrent * ($percentCollaborator / 100);
                $priceAgency = $priceCurrent * ($percentAgency / 100);
                $priceDistributor = $priceCurrent * ($percentDistributor / 100);
                $priceMember = $priceCurrent + $priceMember;
                $priceCollaborator = $priceCurrent + $priceCollaborator;
                $priceAgency = $priceCurrent + $priceAgency;
                $priceDistributor = $priceCurrent + $priceDistributor;

                if (!$serverExist) {
                    $new = new ServiceServer();
                    $new->service_id = $server->service_id;
                    $new->name = $server->name;
                    $new->details = $server->details;
                    $new->package_id = $server->package_id;
                    $new->price = $server->levelPrice(Auth::user()->level);
                    $new->price_update = $server->levelPrice(Auth::user()->level);
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
                } else {

                    // if($serverExist->price != $serverExist->price_update){
                    //      $serverExist->update([
                    //         'price' => $serverExist->price_update,
                    //         'price_member' => $priceMember,
                    //         'price_collaborator' => $priceCollaborator,
                    //         'price_agency' => $priceAgency,
                    //         'price_distributor' => $priceDistributor,
                    //     ]);
                    // }

                    if ($server->updated_at > $serverExist->updated_at) {

                        if ($server->levelPrice(Auth::user()->level) != $serverExist->price) {
                            $serverExist->price = $server->levelPrice(Auth::user()->level);
                        }

                        $serverExist->name = $server->name;
                        $serverExist->details = $server->details;
                        $serverExist->package_id = $server->package_id;
                        $serverExist->price = $server->levelPrice(Auth::user()->level);
                        $serverExist->min = $server->min;
                        $serverExist->max = $server->max;
                        $serverExist->limit_day = $server->limit_day;
                        $serverExist->status = $server->status;
                        $serverExist->visibility = $server->visibility;
                        $serverExist->updated_at = $server->updated_at;
                        $serverExist->save();

                        // action
                        $action = $server->action;
                        $ac = $serverExist->action;
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
                    } else {
                    }
                }
            }

            $servers = ServiceServer::where('domain', request()->getHost())
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                })
                ->when($service, function ($query, $service) {
                    return $query->whereHas('service', function ($query) use ($service) {
                        $query->where('id', $service);
                    });
                })
                ->when($visibility, function ($query, $visibility) {
                    return $query->where('visibility', $visibility);
                })
                ->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->orderBy('id', 'DESC')->paginate(10);

            return view('admin.service.partner.server', compact('servers'));
        }
    }

    public function viewEditServer($id)
    {
        if (request()->getHost() === env('APP_MAIN_SITE')) {
            $server = ServiceServer::where('id', $id)->where('domain', request()->getHost())->first();
            if (!$server) {
                return redirect()->back()->with('error', 'Máy chủ này không tồn tại');
            }
            return view('admin.service.server-edit', compact('server'));
        } else {
            $server = ServiceServer::where('id', $id)->where('domain', request()->getHost())->first();
            if (!$server) {
                return redirect()->back()->with('error', 'Máy chủ này không tồn tại');
            }

            return view('admin.service.partner.server-edit', compact('server'));
        }
    }

    public function updatePrice(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'price' => 'required|numeric',
            'type' => 'required|in:default,update',
            'action' => 'required|in:default,percent',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first());
        } else {
            $servers = ServiceServer::where('domain', request()->getHost())->get();
            foreach ($servers as $server) {
                if ($request->type == 'default') {
                    if ($request->action == 'default') {
                        // các giá cấp bậc sẽ được cập nhật theo giá mặc định
                        $priceMember = $request->price + $server->price_member;
                        $priceCollaborator = $request->price + $server->price_collaborator;
                        $priceAgency = $request->price + $server->price_agency;
                        $priceDistributor = $request->price + $server->price_distributor;
                        $server->update([
                            'price_update' => $server->price,
                            'price_member' => $priceMember,
                            'price_collaborator' => $priceCollaborator,
                            'price_agency' => $priceAgency,
                            'price_distributor' => $priceDistributor,
                        ]);
                    }
                    if ($request->action == 'percent') {
                        $priceMember = $server->price + $server->price * $request->price / 100;
                        $priceCollaborator = $server->price + $server->price * $request->price / 100;
                        $priceAgency = $server->price + $server->price * $request->price / 100;
                        $priceDistributor = $server->price + $server->price * $request->price / 100;
                        $server->update([
                            'price_update' => $server->price,
                            'price_member' => $priceMember,
                            'price_collaborator' => $priceCollaborator,
                            'price_agency' => $priceAgency,
                            'price_distributor' => $priceDistributor,
                        ]);
                    }
                } else {
                    if ($server->price !== $server->price_update) {
                        if ($request->action == 'default') {
                            // các giá cấp bậc sẽ được cập nhật theo giá mặc định
                            $priceMember = $request->price + $server->price_member;
                            $priceCollaborator = $request->price + $server->price_collaborator;
                            $priceAgency = $request->price + $server->price_agency;
                            $priceDistributor = $request->price + $server->price_distributor;
                            $server->update([
                                'price' => $request->price,
                                'price_member' => $priceMember,
                                'price_collaborator' => $priceCollaborator,
                                'price_agency' => $priceAgency,
                                'price_distributor' => $priceDistributor,
                            ]);
                        }
                        if ($request->action == 'percent') {
                            $priceMember = $server->price + $server->price * $request->price / 100;
                            $priceCollaborator = $server->price + $server->price * $request->price / 100;
                            $priceAgency = $server->price + $server->price * $request->price / 100;
                            $priceDistributor = $server->price + $server->price * $request->price / 100;
                            $server->update([
                                'price' => $request->price,
                                'price_member' => $priceMember,
                                'price_collaborator' => $priceCollaborator,
                                'price_agency' => $priceAgency,
                                'price_distributor' => $priceDistributor,
                            ]);
                        }
                    }
                }
            }
            return redirect()->back()->with('success', 'Cập nhật giá thành công');
        }
    }

    public function updateServicePrice(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'percent_member' => 'required|numeric',
            'percent_collaborator' => 'required|numeric',
            'percent_agency' => 'required|numeric',
            'percent_distributor' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first());
        } else {
            $servers = ServiceServer::where('domain', request()->getHost())->get();
            foreach ($servers as $server) {
                $priceMember = $server->price + $server->price * $request->percent_member ?? 5 / 100;
                $priceCollaborator = $server->price + $server->price * $request->percent_collaborator ?? 5 / 100;
                $priceAgency = $server->price + $server->price * $request->percent_agency ?? 5 / 100;
                $priceDistributor = $server->price + $server->price * $request->percent_distributor ?? 5 / 100;
                $server->update([
                    'price_member' => $priceMember,
                    'price_collaborator' => $priceCollaborator,
                    'price_agency' => $priceAgency,
                    'price_distributor' => $priceDistributor,
                ]);
                ConfigSite::where('domain', request()->getHost())->update([
                    'percent_member' => $request->percent_member,
                    'percent_collaborator' => $request->percent_collaborator,
                    'percent_agency' => $request->percent_agency,
                    'percent_distributor' => $request->percent_distributor,
                ]);
            }
            return redirect()->back()->with('success', 'Cập nhật giá thành công');
        }
    }

    public function createServer(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'service' => 'required|integer',
            'name' => 'required|string',
            'details' => 'required|string',
            'package_id' => 'required|integer',
            'get_uid' => 'required|in:on,off',
            'limit_day' => 'required|integer',
            'min' => 'required|integer|min:1',
            'max' => 'required|integer|min:1',
            'price_member' => 'required|numeric|min:0',
            'price_collaborator' => 'required|numeric|min:0',
            'price_agency' => 'required|numeric|min:0',
            'price_distributor' => 'required|numeric|min:0',
            'providerName' => 'required|string',
            'providerLink' => 'required|string',
            'providerServer' => 'required|string',
            'providerKey' => 'required|string',
            'refund_status' => 'required|in:on,off',
            'warranty_status' => 'required|in:on,off',
            'renews_status' => 'required|in:on,off',
            'status' => 'required|in:active,inactive',
            'visibility' => 'required|in:public,private',
            'reaction_status' => 'required|in:on,off',
            'quantity_status' => 'required|in:on,off',
            'comments_status' => 'required|in:on,off',
            'minutes_status' => 'required|in:on,off',
            'time_status' => 'required|in:on,off',
            'posts_status' => 'required|in:on,off',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {

            $service = Service::where('id', $request->service)->where('domain', env('APP_MAIN_SITE'))->first();

            if (!$service) {
                return redirect()->back()->with('error', 'Dịch vụ này không tồn tại')->withInput();
            }

            $server = $service->servers()->where('package_id', $request->package_id)->first();
            if ($server) {
                return redirect()->back()->with('error', 'Máy chủ này đã tồn tại')->withInput();
            }

            $server = $service->servers()->create([
                'name' => $request->name,
                'details' => $request->details,
                'package_id' => $request->package_id,
                'limit_day' => $request->limit_day,
                'min' => $request->min,
                'max' => $request->max,
                'price_member' => $request->price_member,
                'price_collaborator' => $request->price_collaborator,
                'price_agency' => $request->price_agency,
                'price_distributor' => $request->price_distributor,
                'providerName' => $request->providerName,
                'providerLink' => $request->providerLink,
                'providerServer' => $request->providerServer,
                'providerKey' => $request->providerKey,
                'status' => $request->status,
                'visibility' => $request->visibility,
                'domain' => request()->getHost(),
            ]);

            $server->actions()->create([
                'get_uid' => $request->get_uid,
                'quantity_status' => $request->quantity_status,
                'reaction_status' => $request->reaction_status,
                'reaction_data' => $request->reaction_data,
                'comments_status' => $request->comments_status,
                'comments_data' => $request->comments_data,
                'minutes_status' => $request->minutes_status,
                'minutes_data' => $request->minutes_data,
                'domain' => request()->getHost(),
                'time_status' => $request->time_status,
                'time_data' => $request->time_data,
                'posts_status' => $request->posts_status,
                'posts_data' => $request->posts_data,
                'refund_status' => $request->refund_status,
                'warranty_status' => $request->warranty_status,
                'renews_status' => $request->renews_status,
            ]);

            return redirect()->back()->with('success', 'Tạo máy chủ thành công');
        }
    }

    public function updateServer(Request $request, $id)
    {
        if (request()->getHost() === env('APP_MAIN_SITE')) {
            $valid = Validator::make($request->all(), [
                'service' => 'required|integer',
                'name' => 'required|string',
                'details' => 'required|string',
                'package_id' => 'required|integer',
                'get_uid' => 'required|in:on,off',
                'limit_day' => 'required|integer',
                'min' => 'required|integer|min:1',
                'max' => 'required|integer|min:1',
                'price_member' => 'required|numeric|min:0',
                'price_collaborator' => 'required|numeric|min:0',
                'price_agency' => 'required|numeric|min:0',
                'price_distributor' => 'required|numeric|min:0',
                'providerName' => 'required|string',
                'providerLink' => 'required|string',
                'providerServer' => 'required|string',
                'providerKey' => 'required|string',
                'refund_status' => 'required|in:on,off',
                'warranty_status' => 'required|in:on,off',
                'renews_status' => 'required|in:on,off',
                'status' => 'required|in:active,inactive',
                'visibility' => 'required|in:public,private',
                'reaction_status' => 'required|in:on,off',
                'quantity_status' => 'required|in:on,off',
                'comments_status' => 'required|in:on,off',
                'minutes_status' => 'required|in:on,off',
                'time_status' => 'required|in:on,off',
                'posts_status' => 'required|in:on,off',
            ]);

            if ($valid->fails()) {
                return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            } else {

                $server = ServiceServer::where('id', $id)->where('domain', request()->getHost())->first();
                if (!$server) {
                    return redirect()->back()->with('error', 'Máy chủ này không tồn tại')->withInput();
                }

                $packageIdCheck = ServiceServer::where('package_id', '!=', $server->package_id)->where('package_id', $request->package_id)->where('service_id', $request->service)->where('domain', request()->getHost())->first();
                if ($packageIdCheck) {
                    return redirect()->back()->with('error', 'Máy chủ này đã tồn tại')->withInput();
                }

                $service = Service::where('id', $request->service)->where('domain', env('APP_MAIN_SITE'))->first();

                if (!$service) {
                    return redirect()->back()->with('error', 'Dịch vụ này không tồn tại')->withInput();
                }

                $server->update([
                    'name' => $request->name,
                    'details' => $request->details,
                    'package_id' => $request->package_id,
                    'limit_day' => $request->limit_day,
                    'price' => $server->price_update,
                    'min' => $request->min,
                    'max' => $request->max,
                    'price_member' => $request->price_member,
                    'price_collaborator' => $request->price_collaborator,
                    'price_agency' => $request->price_agency,
                    'price_distributor' => $request->price_distributor,
                    'providerName' => $request->providerName,
                    'providerLink' => $request->providerLink,
                    'providerServer' => $request->providerServer,
                    'providerKey' => $request->providerKey,
                    'status' => $request->status,
                    'visibility' => $request->visibility,
                ]);

                $server->actions()->update([
                    'get_uid' => $request->get_uid,
                    'quantity_status' => $request->quantity_status,
                    'reaction_status' => $request->reaction_status,
                    'reaction_data' => $request->reaction_data,
                    'comments_status' => $request->comments_status,
                    'comments_data' => $request->comments_data,
                    'minutes_status' => $request->minutes_status,
                    'minutes_data' => $request->minutes_data,
                    'time_status' => $request->time_status,
                    'time_data' => $request->time_data,
                    'posts_status' => $request->posts_status,
                    'posts_data' => $request->posts_data,
                    'refund_status' => $request->refund_status,
                    'warranty_status' => $request->warranty_status,
                    'renews_status' => $request->renews_status,
                ]);

                if ($request->get('price_update') === 1) {
                    $server->update([
                        'price' => $server->price_update,
                    ]);
                }

                return redirect()->back()->with('success', 'Cập nhật máy chủ thành công');
            }
        } else {
            $valid = Validator::make($request->all(), [
                'name' => 'required|string',
                'details' => 'required|string',
                'price_member' => 'required|numeric|min:0',
                'price_collaborator' => 'required|numeric|min:0',
                'price_agency' => 'required|numeric|min:0',
                'price_distributor' => 'required|numeric|min:0',
                'status' => 'required|in:active,inactive',
            ]);

            if ($valid->fails()) {
                return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            } else {

                $server = ServiceServer::where('id', $id)->where('domain', request()->getHost())->first();
                if (!$server) {
                    return redirect()->back()->with('error', 'Máy chủ này không tồn tại')->withInput();
                }

                $service = Service::where('id', $server->service_id)->where('domain', env('APP_MAIN_SITE'))->first();

                if (!$service) {
                    return redirect()->back()->with('error', 'Dịch vụ này không tồn tại')->withInput();
                }

                $server->update([
                    'name' => $request->name,
                    'details' => $request->details,
                    'price_member' => $request->price_member,
                    'price_collaborator' => $request->price_collaborator,
                    'price_agency' => $request->price_agency,
                    'price_distributor' => $request->price_distributor,
                    'status' => $request->status,
                    'visibility' => $request->visibility,
                ]);


                return redirect()->back()->with('success', 'Cập nhật máy chủ thành công');
            }
        }
    }

    public function deleteServer($id)
    {
        $server = ServiceServer::where('id', $id)->where('domain', request()->getHost())->first();
        if (!$server) {
            return redirect()->back()->with('error', 'Máy chủ này không tồn tại');
        }
        $server->actions()->delete();
        $server->delete();
        return redirect()->back()->with('success', 'Xóa máy chủ thành công');
    }
}
