<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Upload;
use App\Models\AccountGateway; // use the new model
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class AccountManagementController extends Controller
{
    use Upload;

    public function index()
    {
        $data['methods'] = AccountGateway::orderBy('sort_by', 'asc')->get();
        $data['types'] = Category::where('status','1')->get();
        $data['pageTitle'] = 'Payment Methods';
        
        return view('admin.accounts.ewallet_accounts', $data);
    }

    public function create()
    {
        $data['pageTitle'] = 'Add Payment Methods';
        $data['types'] = Category::where('status','1')->get();
        return view('admin.payment_methods.accounts.create', $data);
    }

    public function store(Request $request)
    {

        $rules = [
            'name' => 'required',
            // 'currency' => 'required',
            'minimum_deposit_amount' => 'required|numeric',
            'maximum_deposit_amount' => 'required|numeric',
            // 'percentage_charge' => 'required|numeric',
            // 'fixed_charge' => 'required|numeric',
            // 'convention_rate' => 'required|numeric',
            'minimum_withdrawal_amount' => 'required|numeric',
            'maximum_withdrawal_amount' => 'required|numeric',
        ];

        $this->validate($request, $rules);

        $gateway = new AccountGateway;
        $input_form = [];

        if ($request->has('field_name')) {
            for ($a = 0; $a < count($request->field_name); $a++) {
                $arr = [];
                $arr['field_name'] = clean($request->field_name[$a]);
                $arr['field_level'] = $request->field_name[$a];
                $arr['type'] = $request->type[$a];
                $arr['validation'] = $request->validation[$a];
                $input_form[$arr['field_name']] = $arr;
            }
        }

        if ($request->hasFile('image')) {
            try {
                $gateway->image = $this->uploadImage($request->image, config('location.accounts.path'), config('location.accounts.size'));
            } catch (\Exception $exp) {
                return back()->with('error', 'Image could not be uploaded.');
            }
        }


        try {

            $gateway->name = $request->name ?? null;
       

            $gateway->code = $request->name ? Str::slug($request->name) : null;
            $gateway->currency = $request->currency ?? null;
            $gateway->symbol = $request->currency ?? null;
            $gateway->convention_rate = $request->convention_rate ?? null;
            $gateway->withdraw_convention_rate = $request->withdraw_convention_rate ?? null;
            $gateway->min_amount = $request->minimum_deposit_amount ?? null;
            $gateway->max_amount = $request->maximum_deposit_amount ?? null;
            $gateway->minimum_withdrawal_amount = $request->minimum_withdrawal_amount ?? null;
            $gateway->maximum_withdrawal_amount = $request->maximum_withdrawal_amount ?? null;
            $gateway->fixed_deposit_charge = $request->fixed_deposit_charge ?? null;
            $gateway->percentage_deposit_charge = $request->percentage_deposit_charge ?? null;
            $gateway->fixed_charge = $request->fixed_withdraw_charge ?? null;
            $gateway->percentage_charge = $request->percentage_withdraw_charge ?? null;
            $gateway->daily_withdraw_limit = $request->daily_withdraw_limit ?? null;
            $gateway->monthly_withdraw_limit = $request->monthly_withdraw_limit ?? null;
            $gateway->daily_deposit_limit = $request->daily_deposit_limit ?? null;
            $gateway->monthly_deposit_limit = $request->monthly_deposit_limit ?? null;
            $gateway->parameters = $input_form ?? null;
            $gateway->status = $request->status ?? 1;
            $gateway->note = $request->note ?? null;

            $res = $gateway->save();

            if (! $res) {
                throw new \Exception('Unexpected error! Please try again.');
            }

            return redirect()->route('admin.accounts.management')->with('success', 'Payment Method has been created.');
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function edit($id)
    {
       
        $data['method'] = AccountGateway::findOrFail($id);
        $data['pageTitle'] = 'Edit Payment Method';

        return view('admin.payment_methods.accounts.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',

            'minimum_deposit_amount' => 'required|numeric',
            'maximum_deposit_amount' => 'required|numeric',

            'minimum_withdrawal_amount' => 'required|numeric',
            'maximum_withdrawal_amount' => 'required|numeric',
        ];

        $gateway = AccountGateway::findOrFail($id);

        $this->validate($request, $rules);

        $input_form = [];

        if ($request->has('field_name')) {
            for ($a = 0; $a < count($request->field_name); $a++) {
                $arr = [];
                $arr['field_name'] = clean($request->field_name[$a]);
                $arr['field_level'] = $request->field_name[$a];
                $arr['type'] = $request->type[$a];
                $arr['validation'] = $request->validation[$a];
                $input_form[$arr['field_name']] = $arr;
            }
        }

        if ($request->hasFile('image')) {
            try {
                $old = $gateway->image ?? null;
                $gateway->image = $this->uploadImage($request->image, config('location.accounts.path'), config('location.accounts.size'), $old);
            } catch (\Exception $exp) {
                return back()->with('error', 'Image could not be uploaded.');
            }
        }

        try {
            $gateway->name = $request->name;
            $gateway->currency = $request->currency;
            $gateway->symbol = $request->currency;
            $gateway->convention_rate = $request->convention_rate;
            $gateway->withdraw_convention_rate = $request->withdraw_convention_rate;
            $gateway->min_amount = $request->minimum_deposit_amount;
            $gateway->max_amount = $request->maximum_deposit_amount;
            $gateway->minimum_withdrawal_amount = $request->minimum_withdrawal_amount;
            $gateway->maximum_withdrawal_amount = $request->maximum_withdrawal_amount;
            $gateway->fixed_deposit_charge = $request->fixed_deposit_charge;
            $gateway->percentage_deposit_charge = $request->percentage_deposit_charge;
            $gateway->fixed_withdraw_charge = $request->fixed_withdraw_charge;
            $gateway->percentage_withdraw_charge = $request->percentage_withdraw_charge;
            $gateway->daily_withdraw_limit = $request->daily_withdraw_limit;
            $gateway->monthly_withdraw_limit = $request->monthly_withdraw_limit;
            $gateway->daily_deposit_limit = $request->daily_deposit_limit;
            $gateway->monthly_deposit_limit = $request->monthly_deposit_limit;
            $gateway->parameters = $input_form;
            $gateway->status = $request->status ?? 1;
            $gateway->note = $request->note;
            $res = $gateway->save();

            if (! $res) {
                throw new \Exception('Unexpected error! Please try again.');
            }

            return back()->with('success', 'Payment Method has been updated.');
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function deactivate(Request $request)
{
    try {
        $record = AccountGateway::where('code', $request->code)->firstOrFail();
        $record->status = $record->status == 1 ? 0 : 1;
        $record->save();

        return redirect()->back()->with('success', 'Gateway status updated successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'An error occurred while deactivating the gateway.');
    }
}


}
