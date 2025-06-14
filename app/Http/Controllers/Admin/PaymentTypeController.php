<?php

namespace App\Http\Controllers\Admin;
use App\Models\Api;
use App\Models\PaymentType;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function type(Request $request)
    {
        $records = PaymentType::paginate('20');
        $partners = Api::where('type', 'Admin')->pluck('name', 'id');
        $pageTitle = $title = __('reports.payment_type_management');

        return view('admin.group.paymenttype', compact('records', 'pageTitle', 'title', 'partners'));
    }


    public function typeAdd(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'status' => 'required',

        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        // Create and save API entry
        PaymentType::create([
            'name' => $request->name,
            'status' => $request->status,

        ]);

        session()->flash('success', 'Type Added Successfully');
        return back();
    }


    public function updatetype(Request $request, $id)
    {
        $validated = $request->validate($this->validationRules());

        $group = PaymentType::findOrFail($id);
        $group->update($validated);

        return back()->with('success', 'Type Updated Successfully');
    }

    protected function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',

        ];
    }


}
