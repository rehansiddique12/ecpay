<?php

namespace App\Http\Controllers\Partner;

use App\Models\Api;
use App\Models\PartnerLog;
use Illuminate\Http\Request;
use App\Models\TwoStepVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ManageRolePermissionController extends Controller
{

    public function staff(Request $request)
    {
        $log = "View Staff";
        $this->addLogs($log);
        $user = Auth::guard('partner')->user();
        $partners = Api::where('id', '!=', auth()->guard('partner')->id())->where('api_key', $user->api_key)->where('type', '!=', 'Admin')->paginate(config('basic.paginate'));


        if ($request->ajax()) {
            // Prepare the query for Admins
            $query = Api::select(['id', 'name', 'username', 'email', 'phone', 'status', 'admin_access', 'role_type'])
                ->where('id', '!=', auth()
                ->guard('partner')->id())
                ->where('api_key', $user->api_key)
                ->where('type', '!=', 'Admin');

            // Return DataTables response
            return DataTables::of($query)
                ->editColumn('status', function ($admin) {
                    $toggleRoute = route('partner.toggleStaffStatus', $admin->id);

                    return '<span class="toggle-status"
                                    data-id="' . $admin->id . '"
                                    data-url="' . $toggleRoute . '"
                                    style="cursor: pointer;">
                                    ' . ($admin->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Deactive</span>') . '
                                </span>';
                })
                ->editColumn('role_type', function ($admin) {
                    return isset($admin->role_type) ? $admin->role_type : 'N/A';
                })
                ->addColumn('action', function ($admin) {
                    return view('partner.staff.action_menu', compact('admin'))->render();
                })
                // ->addColumn('action', function ($admin) {
                //     $updateRoute = route('partner.updateStaff', ':id');

                //     return '<button class="btn btn-sm btn-primary editAdminBtn"
                //             data-id="' . $admin->id . '"
                //             data-name="' . $admin->name . '"
                //             data-username="' . $admin->username . '"
                //             data-email="' . $admin->email . '"
                //             data-phone="' . $admin->phone . '"
                //             data-status="' . $admin->status . '"
                //             data-role-type="' . $admin->role_type . '"
                //             data-location="' . $admin->location_id . '"
                //             data-admin-access=\'' . json_encode($admin->admin_access) . '\'
                //             data-route="' . $updateRoute . '"
                //             data-bs-toggle="modal" data-bs-target="#editUserModal">
                //                 <i class="fa fa-edit"></i>
                //         </button>';
                // })
                ->rawColumns(['status', 'action'])
                ->toJson();
            // ->make(true);
        }
        $pageTitle = 'Manage Staff & Permission';

        // $data['partners'] = Api::get();
        return view('partner.staff.index', compact('pageTitle', 'partners'));
    }

    public function storeStaff(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:191',
            'username' => 'required|alpha_dash|unique:apis,username',
            'email' => 'required|email|max:191|unique:apis,email',
            'password' => 'nullable|min:5|confirmed',
            'status' => 'required'
        ]);

        $user = Auth::guard('partner')->user();

        $log = "Add New Staff " . $request->name;
        $this->addLogs($log);

        $item = new Api();
        $item->name = $request->name;
        $item->username = $request->username;
        $item->email = $request->email;
        $item->phone = $request->phone;

        $item->website = $user->website;
        $item->api_key = $user->api_key;
        $item->type = "Staff";

        if (isset($request->password)) {
            $item->password = Hash::make($request->password);
        }

        $item->admin_access = (isset($request->access)) ? explode(',', join(',', $request->access)) : [];
        $item->status = $request->status;
        $item->save();

        session()->flash('success', 'Added Successfully');
        return back();
    }

    public function apisDelete(Request $request)
    {
        $id = (int)$request->input('id');
        try {
            $api = Api::findOrFail($id);
            if ($api->delete()) {
                return response()->json(['status' => 'success', 'message' => 'API deleted successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error deleting role: ' . $e->getMessage()]);
        }
    }

    public function updateStaff(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'edit_name' => 'required|max:191',
            'edit_username' => 'required|alpha_dash|unique:apis,username,' . $id,
            'edit_email' => 'required|email|max:191|unique:apis,email,' . $id,
            'edit_password' => 'nullable|min:5|confirmed',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Log the update action
        $log = "Update Staff " . $request->edit_name;
        $this->addLogs($log);

        // Find staff
        $item = Api::findOrFail($id);
        $item->name = $request->edit_name;
        $item->username = $request->edit_username;
        $item->email = $request->edit_email;
        $item->phone = $request->edit_phone;
        $item->password_string = $request->edit_password;

        if ($request->filled('edit_password')) {
            $item->password = Hash::make($request->edit_password);
        }

        // Assuming 'edit_access' holds permissions array from checkboxes
        $item->admin_access = $request->has('edit_access')
            ? explode(',', join(',', (array) $request->edit_access))
            : [];

        $item->status = $request->status;
        $item->save();

        // Return success JSON response
        return response()->json([
            'message' => 'Updated Successfully'
        ]);
    }


    function addLogs($log)
    {

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $user = Auth::guard('partner')->user();

        $partnerlog = new PartnerLog();
        $partnerlog->api_id = $user->id;
        $partnerlog->log = $log;
        $partnerlog->ip_address = $ipAddress;
        $partnerlog->save();
    }

    public function toggleStaffStatus($id)
    {
        $admin = Api::findOrFail($id);
        $admin->status = $admin->status == 1 ? 0 : 1;
        $admin->save();

        return response()->json([
            'success' => true,
            'new_status' => $admin->status,
            'message' => 'Status updated successfully.',
        ]);
    }

    public function apisReset($id)
    {
        $api = Api::findOrFail($id);

        $log = "QR Code Reset of " . $api->name;
        $this->addLogs($log);
        $TwoStepVerification = TwoStepVerification::where('user_id', $id)
            ->first();
        if ($TwoStepVerification) {
            $TwoStepVerification->g_auth_status = 'No';
            $TwoStepVerification->save();
        }

        session()->flash('success', 'Reset Successfully');
        return back();
    }
}
