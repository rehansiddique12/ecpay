<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\MerchantAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MerchantAccountController extends Controller
{
    public function apis(Request $request)
    {   
        $records = MerchantAccount::get();
        $pageTitle = "Manage Merchant Accounts";
        return view('admin.merchant_account.index', compact('records', 'pageTitle'));
    }


    public function apisAdd(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ip' => 'required|string',
            'username' => 'required|string',
            'status' => 'required',
            'password' => 'required|string|min:5',
        ]);


        $api = new MerchantAccount;
        $api->account_name = $request->account_name;
        $api->e_wallet_phone_number = $request->e_wallet_phone_number;
        $api->app_key = $request->app_key;
        $api->app_secret = $request->app_secret;
        $api->username = $request->username;
        $api->password = $request->password;
        $api->status = $request->status;
        $api->save();
        session()->flash('success', 'Added Successfully');
        return back();
    }

    public function apisDelete($id)
    {
        $api = MerchantAccount::findOrFail($id);
        $api->delete();

        return redirect()->route('admin.merchant_accounts')->with('success', 'Merchant Account deleted successfully.');
    }

    public function updateApi(Request $request, $id)
    {


        $validator = Validator::make($request->all(), [
            'ip' => 'required|string',
            'username' => 'required|string',
            'status' => 'required',
            'password' => 'required|string|min:5',
        ]);

        $api = MerchantAccount::findOrFail($id);
        $api->account_name = $request->account_name;
        $api->e_wallet_phone_number = $request->e_wallet_phone_number;
        $api->app_key = $request->app_key;
        $api->app_secret = $request->app_secret;
        $api->username = $request->username;
        $api->password = $request->password;
        $api->status = $request->status;
        $api->save();
        session()->flash('success', 'Merchant Account Updated');
        return back();
    }
}
