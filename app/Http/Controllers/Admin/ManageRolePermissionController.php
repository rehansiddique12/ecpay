<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class ManageRolePermissionController extends Controller
{
    public function staff(Request $request)
    {
        $pageTitle = 'Manage Admin & Permission';

        // Check if the user has the 'view' permission
        // if (!adminAccessRoute(config('role.manage_staff.access.view'))) {
        //     abort(403, 'Unauthorized');
        // }

        // Handle DataTables AJAX request
        if ($request->ajax()) {
            // Prepare the query for Admins
            $admins = Admin::select(['id', 'name', 'username', 'email', 'phone', 'status', 'admin_access', 'role_type']);

            // Process the DataTables response with pagination, search, etc.
            return DataTables::of($admins)
                ->editColumn('status', function ($admin) {
                    return $admin->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Deactive</span>';
                })
                ->addColumn('action', function ($admin) {
                    $updateRoute = route('admin.updateStaff', ':id');

                    // Generate the "Edit" button
                    return '<button class="btn btn-sm btn-primary editAdminBtn"
                            data-id="' . $admin->id . '"
                            data-name="' . $admin->name . '"
                            data-username="' . $admin->username . '"
                            data-email="' . $admin->email . '"
                            data-phone="' . $admin->phone . '"
                            data-status="' . $admin->status . '"
                            data-role-type="' . $admin->role_type . '"
                            data-admin-access=\'' . json_encode($admin->admin_access) . '\'
                            data-route="' . $updateRoute . '"
                            data-bs-toggle="modal" data-bs-target="#editUserModal">
                                <i class="fa fa-edit"></i>
                            </button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);  // Return the paginated data as expected by DataTables
        }
        $list_roles = UserRoles::where('used_for', 'Admin')->get();

        // Normal return when not AJAX
        return view('admin.staff.index', compact('pageTitle', 'list_roles'));
    }



    public function storeStaff(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|max:191',
            'username' => 'required|alpha_dash|unique:admins,username',
            'email' => 'required|email|max:191|unique:admins,email',
            'password' => 'required|min:5',  // Password is required and must be at least 5 characters long
            'status' => 'required|in:0,1',  // Ensure status is either 0 or 1
            'access' => 'array',
        ]);

        $find_role = UserRoles::where('id', $request->role_type)->first();
        if (!$find_role) {
            return response()->json(['success' => false, 'message' => 'Role not found.']);
        }

        // Create new admin instance
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->role_type = $find_role->name;
        $admin->admin_access = $find_role->admin_access;

        // Hash the password
        $admin->password = Hash::make($request->password);

        // Handle admin access permissions if available
        // $admin->admin_access = $request->has('access') ? explode(',', implode(',', $request->access)) : [];

        // Set status
        $admin->status = $request->status;

        // Save the admin
        $admin->save();

        // Flash success message
        // session()->flash('success', 'Added Successfully');

        // Redirect (adjust the route as necessary)
        return response()->json(['success' => true, 'message' => 'Admin Successfully Added.']);
    }

    public function updateStaff(Request $request, Admin $admin)
    {
        // Validate request data
        $validated = $request->validate([
            'update-name' => 'required|string|max:191',
            'update-username' => 'required|alpha_dash|unique:admins,username,' . $admin->id,
            'update-email' => 'required|email|max:191|unique:admins,email,' . $admin->id,
            'update-password' => 'nullable|min:5',
            'update-status' => 'required|in:0,1',
            'update-phone' => 'nullable|string|max:20',
            'update-access' => 'nullable|array',
        ], [], [
            'update-name' => 'Name',
            'update-username' => 'Username',
            'update-email' => 'Email',
            'update-password' => 'Password',
            'update-status' => 'Status',
            'update-phone' => 'Phone',
            'update-access' => 'Access',
        ]);

        $find_role = UserRoles::where('name', $request->role_type_edit)->first();
        if (!$find_role) {
            return response()->json(['success' => false, 'message' => 'Role not found.']);
        }

        try {
            // Update the admin record
            $admin->update([
                'name' => $validated['update-name'],
                'username' => $validated['update-username'],
                'email' => $validated['update-email'],
                'phone' => $validated['update-phone'] ?? null,
                'password' => $request->filled('update-password') ? Hash::make($validated['update-password']) : $admin->password,
                'role_type' => $find_role->name,
                'admin_access' => $find_role->admin_access,
                'status' => $validated['update-status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin updated successfully!',
            ], 200);
        } catch (\Exception $e) {
            // Return a generic error if something goes wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
