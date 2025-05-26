<?php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use App\Models\Gateway;
use App\Models\Category;
use App\Http\Traits\Upload;
use Illuminate\Support\Str;
use App\Models\AccountGroup;
use Illuminate\Http\Request;
use App\Models\AccountGateway;
use App\Models\EWalletAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AccountManagementController extends Controller
{
    use Upload;

    public function index()
    {
        $data['methods'] = Gateway::orderBy('sort_by', 'asc')->get();
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

        $data['method'] = Gateway::findOrFail($id);
        $data['pageTitle'] = 'Edit Payment Method';

        return view('admin.payment_methods.accounts.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $gateway = Gateway::findOrFail($id);

        $validated = $request->validate([
            'edit_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    $code = strtolower($value);
                    if (Gateway::where('code', $code)->where('id', '!=', $id)->exists()) {
                        $fail('The name field results in a duplicate code: ' . $code);
                    }
                }
            ],
            'edit_currency' => 'required|string|max:10',
            // 'edit_type' => 'required|exists:categories,id',
            'edit_minimum_deposit_amount' => 'required|numeric|min:0',
            'edit_maximum_deposit_amount' => 'required|numeric',
            'edit_minimum_withdrawal_amount' => 'required|numeric|min:0',
            'edit_maximum_withdrawal_amount' => 'required|numeric',
            'edit_convention_rate' => 'required|numeric|min:0',
            'edit_percentage_charge' => 'required|numeric|min:0',
            'edit_fixed_charge' => 'required|numeric|min:0',
            'edit_status' => 'nullable|boolean',
            'edit_note' => 'nullable',
            'edit_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle image update
        if ($request->hasFile('edit_file')) {
            $file = $request->file('edit_file');
            $destinationPath = base_path('assets/uploads/gateway');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Delete old image if exists
            if ($gateway->image && file_exists($destinationPath . '/' . $gateway->image)) {
                unlink($destinationPath . '/' . $gateway->image);
            }

            // Save new image
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $filename);
            $validated['image'] = $filename;
        }

        // Convert checkbox into boolean
        $validated['status'] = $request->has('edit_status') ? 1 : 0;

        // Field mappings (same as store)
        $gateway->name = $validated['edit_name'];
        $gateway->code = strtolower($validated['edit_name']);
        $gateway->currency = $validated['edit_currency'];
        $gateway->symbol = $validated['edit_currency'];
        // $gateway->category_id = $validated['type'];
        $gateway->min_amount = $validated['edit_minimum_deposit_amount'];
        $gateway->max_amount = $validated['edit_maximum_deposit_amount'];
        $gateway->min_withdrawal_amount = $validated['edit_minimum_withdrawal_amount'];
        $gateway->max_withdrawal_amount = $validated['edit_maximum_withdrawal_amount'];

        $gateway->convention_rate = $validated['edit_convention_rate'];
        $gateway->percentage_charge = $validated['edit_percentage_charge'];
        $gateway->fixed_charge = $validated['edit_fixed_charge'];
        $gateway->status = $validated['status'];
        $gateway->note = $validated['edit_note'];

        if (isset($validated['image'])) {
            $gateway->image = $validated['image'];
        }

        $gateway->save();

        return response()->json([
            'message' => 'Gateway updated successfully!',
        ]);
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



    public function accountGroup()
    {
        $pageTitle = "Account Group";

        // Get all groups with their related accounts
        $groups = Group::with(['accounts'])->get();

        $records = EWalletAccount::select('id' , 'account_no')->get();
        return view('admin.accounts.groups', compact('pageTitle', 'groups', 'records'));
    }

    public function addAccountPairs(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'pairs' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Create or update
            $group = Group::updateOrCreate(
                ['id' => $request->id],
                ['name' => $request->group_name]
            );

            // Sync account pairs
            if (!empty($request->pairs)) {
                $group->accounts()->sync($request->pairs); // replaces old entries
            } else {
                $group->accounts()->detach();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => ['server' => [$e->getMessage()]]
            ], 500);
        }
    }


}
