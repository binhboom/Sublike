<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Models\ServiceServer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function viewTicket(Request $request)
    {

        $search = $request->get('search');
        $replied_status = $request->get('replied_status');
        // $tickets = Ticket::where('user_id', auth()->id())->where('domain', $request->getHost())->get();

        $tickets = Ticket::where('user_id', auth()->id())->where('domain', $request->getHost())
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })->orderBy('created_at', 'desc')->paginate(10);


        $service_servers = ServiceServer::where('status', 'active')->where('domain', $request->getHost())->get();

        return view('guard.ticket', compact('service_servers', 'tickets'));
    }

    public function viewTicketDetail(Request $request, $id)
    {
        $ticket = Ticket::where('id', $id)->where('user_id', auth()->id())->where('domain', $request->getHost())->first();
        if ($ticket) {
            return view('guard.ticket-detail', compact('ticket'));
        } else {
            return redirect()->back()->with('error', 'Không tìm thấy ticket');
        }
    }

    public function createTicket(Request $request)
    {

        return redirect()->back()->with('error', 'Chức năng hiện đang demo');

        // $valid = Validator::make($request->all(), [
        //     'type' => 'required|in:1,2,3,4,5',
        //     'title' => 'required|string|max:255',
        //     'description' => 'required|string',
        //     'service_id' => 'required|exists:service_servers,id',
        //     'priority' => 'required|in:low,medium,high',
        // ]);

        // if ($valid->fails()) {
        //     return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        // } else {
        //     $ticket = new Ticket();
        //     $ticket->title = $request->title;
        //     $ticket->description = $request->description;
        //     $ticket->type = $request->type;
        //     $ticket->user_id = auth()->id();
        //     $ticket->server_id = $request->service_id;
        //     $ticket->priority = $request->priority;
        //     $ticket->domain = $request->getHost();
        //     $ticket->created_by = auth()->id();
        //     $ticket->replied_status = 1;
        //     $ticket->status = 'open';
        //     $ticket->save();

        //     return redirect()->back()->with('success', 'Tạo ticket thành công');
        // }
    }
}
