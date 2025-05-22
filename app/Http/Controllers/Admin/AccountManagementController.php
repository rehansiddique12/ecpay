<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gateway;
use App\Models\Category;
use App\Http\Traits\Upload;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\AccountGateway; // use the new model

class AccountManagementController extends Controller
{
    use Upload;

    public function index()
    {
        $data['methods'] = AccountGateway::orderBy('sort_by', 'asc')->get();
        $data['types'] = Category::where('status', '1')->get();
        $data['pageTitle'] = 'Payment Methods';

        return view('admin.accounts.ewallet_accounts', $data);
    }

    public function create()
    {
        $data['pageTitle'] = 'Add Payment Methods';
        $data['types'] = Category::where('status', '1')->get();
        return view('admin.payment_methods.accounts.create', $data);
    }

    // public function store(Request $request)
    // {

    //     $rules = [
    //         'name' => 'required',
    //         // 'currency' => 'required',
    //         'minimum_deposit_amount' => 'required|numeric',
    //         'maximum_deposit_amount' => 'required|numeric',
    //         // 'percentage_charge' => 'required|numeric',
    //         // 'fixed_charge' => 'required|numeric',
    //         // 'convention_rate' => 'required|numeric',
    //         'minimum_withdrawal_amount' => 'required|numeric',
    //         'maximum_withdrawal_amount' => 'required|numeric',
    //     ];

    //     $this->validate($request, $rules);

    //     $gateway = new AccountGateway;
    //     $input_form = [];

    //     if ($request->has('field_name')) {
    //         for ($a = 0; $a < count($request->field_name); $a++) {
    //             $arr = [];
    //             $arr['field_name'] = clean($request->field_name[$a]);
    //             $arr['field_level'] = $request->field_name[$a];
    //             $arr['type'] = $request->type[$a];
    //             $arr['validation'] = $request->validation[$a];
    //             $input_form[$arr['field_name']] = $arr;
    //         }
    //     }

    //     if ($request->hasFile('image')) {
    //         try {
    //             $gateway->image = $this->uploadImage($request->image, config('location.accounts.path'), config('location.accounts.size'));
    //         } catch (\Exception $exp) {
    //             return back()->with('error', 'Image could not be uploaded.');
    //         }
    //     }


    //     try {

    //         $gateway->name = $request->name ?? null;


    //         $gateway->code = $request->name ? Str::slug($request->name) : null;
    //         $gateway->currency = $request->currency ?? null;
    //         $gateway->symbol = $request->currency ?? null;
    //         $gateway->convention_rate = $request->convention_rate ?? null;
    //         $gateway->withdraw_convention_rate = $request->withdraw_convention_rate ?? null;
    //         $gateway->min_amount = $request->minimum_deposit_amount ?? null;
    //         $gateway->max_amount = $request->maximum_deposit_amount ?? null;
    //         $gateway->minimum_withdrawal_amount = $request->minimum_withdrawal_amount ?? null;
    //         $gateway->maximum_withdrawal_amount = $request->maximum_withdrawal_amount ?? null;
    //         $gateway->fixed_deposit_charge = $request->fixed_deposit_charge ?? null;
    //         $gateway->percentage_deposit_charge = $request->percentage_deposit_charge ?? null;
    //         $gateway->fixed_charge = $request->fixed_withdraw_charge ?? null;
    //         $gateway->percentage_charge = $request->percentage_withdraw_charge ?? null;
    //         $gateway->daily_withdraw_limit = $request->daily_withdraw_limit ?? null;
    //         $gateway->monthly_withdraw_limit = $request->monthly_withdraw_limit ?? null;
    //         $gateway->daily_deposit_limit = $request->daily_deposit_limit ?? null;
    //         $gateway->monthly_deposit_limit = $request->monthly_deposit_limit ?? null;
    //         $gateway->parameters = $input_form ?? null;
    //         $gateway->status = $request->status ?? 1;
    //         $gateway->note = $request->note ?? null;

    //         $res = $gateway->save();

    //         if (! $res) {
    //             throw new \Exception('Unexpected error! Please try again.');
    //         }

    //         return redirect()->route('admin.accounts.management')->with('success', 'Payment Method has been created.');
    //     } catch (\Exception $exception) {
    //         return back()->with('error', $exception->getMessage());
    //     }
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $code = strtolower($value);
                    if (\App\Models\Gateway::where('code', $code)->exists()) {
                        $fail('The name field results in a duplicate code: ' . $code);
                    }
                }
            ],
            'currency' => 'required|string|max:10',
            'type' => 'required|exists:categories,id',
            'minimum_deposit_amount' => 'required|numeric|min:0',
            'maximum_deposit_amount' => 'required|numeric|gte:minimum_deposit_amount',
            'minimum_withdrawal_amount' => 'required|numeric|min:0',
            'maximum_withdrawal_amount' => 'required|numeric|gte:minimum_withdrawal_amount',
            'status' => 'nullable|boolean',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle image upload to 'assets/uploads/gateway'

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Define the root-level path
            $destinationPath = base_path('assets/uploads/gateway');

            // Make sure the folder exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move the file to your custom directory
            $file->move($destinationPath, $filename);

            // Save the relative path (you can store this in DB)
            // $validated['image'] = 'assets/uploads/gateway/' . $filename;
            $validated['image'] = $filename;
        }

        // Convert checkbox into actual boolean (0 or 1)
        $validated['status'] = $request->has('status') ? 1 : 0;

        // Map type to category_id
        $validated['category_id'] = $validated['type'];
        unset($validated['type']);
        $validated['code']=strtolower($validated['name']);
        $validated['symbol']= $validated['currency'];
        $validated['min_amount']= $validated['minimum_deposit_amount'];
        $validated['max_amount']= $validated['maximum_deposit_amount'];
        $validated['min_withdrawal_amount']= $validated['minimum_withdrawal_amount'];
        $validated['max_withdrawal_amount']= $validated['maximum_withdrawal_amount'];
        Gateway::create($validated);

        return response()->json([
            'message' => 'Gateway added successfully!',
        ]);
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
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
            'convention_rate' => 'required|numeric',
            'minimum_deposit_amount' => 'required|numeric',
            'maximum_deposit_amount' => 'required|numeric',
            'minimum_withdrawal_amount' => 'required|numeric',
            'maximum_withdrawal_amount' => 'required|numeric',
            'percentage_deposit_charge' => 'nullable|numeric',
            'fixed_deposit_charge' => 'nullable|numeric',
            'percentage_withdraw_charge' => 'nullable|numeric',
            'fixed_withdraw_charge' => 'nullable|numeric',
            'status' => 'nullable|in:0,1',
            'note' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $gateway = AccountGateway::findOrFail($id);

        // Validate request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle dynamic fields
        $input_form = [];

        if ($request->has('field_name')) {
            for ($a = 0; $a < count($request->field_name); $a++) {
                $arr = [
                    'field_name' => clean($request->field_name[$a]),
                    'field_level' => $request->field_name[$a],
                    'type' => $request->type[$a] ?? '',
                    'validation' => $request->validation[$a] ?? ''
                ];
                $input_form[$arr['field_name']] = $arr;
            }
        }

        // Handle file upload
        if ($request->hasFile('image')) {
            try {
                $old = $gateway->image ?? null;
                $gateway->image = $this->uploadImage(
                    $request->file('image'),
                    config('location.accounts.path'),
                    config('location.accounts.size'),
                    $old
                );
            } catch (\Exception $e) {
                return response()->json(['error' => 'Image could not be uploaded.'], 500);
            }
        }

        // Assign data
        try {
            $gateway->name = $request->name;
            $gateway->currency = $request->currency;
            $gateway->symbol = $request->currency;
            $gateway->convention_rate = $request->convention_rate;
            $gateway->withdraw_convention_rate = $request->withdraw_convention_rate ?? $request->convention_rate;
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
            $gateway->status = $request->has('status') ? 1 : 0;
            $gateway->note = $request->note;

            $gateway->save();

            return response()->json(['success' => 'Payment Method has been updated.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function deactivate($id)
    {
        try {
            $record = Gateway::where('id', $id)->firstOrFail();
            $record->status = $record->status == 1 ? 0 : 1;
            $record->save();

            return response()->json([
                'success' => true,
                'message' => 'Gateway status updated successfully.',
                'new_status' => $record->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the gateway status.',
            ], 500);
        }
    }

}
