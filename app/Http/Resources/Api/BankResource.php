<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->bank_name == 'Momo') {
            $qr_code = "https://chart.googleapis.com/chart?chs=480x480&cht=qr&choe=UTF-8&chl=2|99|" . $this->account_number . "|MOMO|lbd.2005.dev@gmail.com|0|0|" . $this->min_recharge . "|" . site('transfer_code') . request()->user->id . "|transfer_myqr";
        } 
        elseif ($this->bank_name == 'MBBank') {
            $qr_code = "https://img.vietqr.io/image/mb-" . $this->account_number . "-qronly2.jpg?accountName=" . $this->account_name ;
        } 
        elseif ($this->bank_name == 'Techcombank') {
            $qr_code = "https://img.vietqr.io/image/techcombank-" . $this->account_number . "-qronly2.jpg?accountName=" . $this->account_name ;
        } 
        elseif ($this->bank_name == 'ACB') {
            $qr_code = "https://img.vietqr.io/image/ACB-" . $this->account_number . "-qronly2.jpg?accountName=" . $this->account_name ;
        }
        else{
            $qr_code = null;
        }

        /* . "&addInfo=" . site('transfer_code') . request()->user->id
. "&addInfo=" . site('transfer_code') . request()->user->id
. "&addInfo=" . site('transfer_code') . request()->user->id
. "&addInfo=" . site('transfer_code') . request()->user->id */

        return [
            'id' => $this->id,
            'bank_name' => $this->bank_name,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'logo' => asset($this->logo),
            'bank_account' => $this->bank_account,
            'min_recharge' => $this->min_recharge,
            'qr_code' => $qr_code,
        ];
    }
}
