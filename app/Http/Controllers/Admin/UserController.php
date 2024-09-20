<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function viewUser(Request $request)
    {

        $search = $request->get('search');
        $level = $request->get('level');
        $role = $request->get('role');
        $status = $request->get('status');

        $users = User::where('domain', request()->getHost())
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%');
            })
            ->when($level, function ($query, $level) {
                return $query->where('level', $level);
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.user.index', compact('users'));
    }

    public function viewUserBalance()
    {
        return view('admin.user.balance');
    }

    public function viewUserDetail($id)
    {
        $user = User::where('id', $id)->where('domain', request()->getHost())->first();

        if (!$user) {
            return redirect()->route('admin.user')->with('error', __('User not found'));
        }

        return view('admin.user.detail', compact('user'));
    }

    public function viewUserTransactions(Request $request, $username)
    {
        $search = $request->get('search');
        $action = $request->get('type');

        $user = User::where('username', $username)->where('domain', request()->getHost())->first();

        if (!$user) {
            return redirect()->route('admin.user')->with('error', __('User not found'));
        }

        $transactions = $user->transactions()
            ->when($search, function ($query, $search) {
                return $query->where('tran_code', 'like', '%' . $search . '%')
                    ->orWhere('note', 'like', '%' . $search . '%');
            })
            ->when($action, function ($query, $action) {
                return $query->where('type', $action);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.user.transactions', compact('user', 'transactions'));
    }


    public function updateUser(Request $request, $username)
    {
        $user = User::where('username', $username)->where('domain', request()->getHost())->first();

        if (!$user) {
            return redirect()->route('admin.user')->with('error', __('User not found'));
        }

        $valid = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'level' => 'required|in:member,agency,distributor,collaborator',
            'role' => 'required|in:member,admin',
            'status' => 'required|in:active,inactive,banned',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first());
        } else {
            $user->name = $request->name;
            $user->level = $request->level;
            $user->role = $request->role;
            $user->status = $request->status;
            $user->save();

            return redirect()->back()->with('success', __('Update user successfully'));
        }
    }

    public function updatePassword(Request $request, $username)
    {
        $user = User::where('username', $username)->where('domain', request()->getHost())->first();

        if (!$user) {
            return redirect()->route('admin.user')->with('error', __('User not found'));
        }

        $valid = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first());
        } else {
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->back()->with('success', __('Update password successfully'));
        }
    }

    public function updateUserBalance(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'username' => 'required|string',
            'balance' => 'required|numeric',
            'note' => 'nullable|string',
            'type' => 'required|in:add,sub',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first());
        } else {

            $user = User::where('username', $request->username)->where('domain', request()->getHost())->first();

            if (!$user) {
                return redirect()->back()->with('error', __('User not found'));
            }

            $user->balance = $request->type == 'add' ? $user->balance + $request->balance : $user->balance - $request->balance;
            $user->total_recharge = $request->type == 'add' ? $user->total_recharge + $request->balance : $user->total_recharge - $request->balance;
            $user->save();

            $user->transactions()->create([
                'tran_code' => 'INV_24' . rand(1000000, 9999999),
                'type' => 'balance',
                'action' => $request->type,
                'first_balance' => $user->balance,
                'before_balance' => $request->type == 'add' ? $user->balance - $request->balance : $user->balance + $request->balance,
                'after_balance' => $user->balance,
                'note' => $request->note ?? $request->type . ' balance',
                'ip' => $request->ip(),
                'domain' => $request->getHost()
            ]);

            return redirect()->back()->with('success', __('Update balance successfully'));
        }
    }

    public function deleteUser(Request $request, $id){
        $user = User::where('domain', $request->getHost())->where('id', $id);

        if(!$user){
            return redirect()->back()->with('error', __('Không tìm thấy ID User này!'));
        }

        $user->delete();
        return redirect()->back()->with('success', __('Xóa thành công tài khoản này!'));
    }
}
