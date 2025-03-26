<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class ManageRolePermissionController extends Controller
{
    // public function staff()
    // {
    //     $pageTitle  = 'Manage Admin & Permission';
    //     $admins = Admin::select('id','username','email','phone','status')->where('id','!=',auth()->guard('admin')->id()) ->paginate(10);;
    //     // $data['admins'] = Admin::get();

    //     return view('admin.staff.index', compact('admins' , 'pageTitle'));
    // }
    // public function staff(Request $request)
    // {
    //     $pageTitle = 'Manage Admin & Permission';

    //     // Check if the user has the 'view' permission
    //     if (!adminAccessRoute(config('role.manage_staff.access.view'))) {
    //         abort(403, 'Unauthorized');
    //     }

    //     // Handle DataTables AJAX request
    //     if ($request->ajax()) {
    //         // Get the page length, start, and draw from DataTables request
    //         $start = $request->input('start');
    //         $length = $request->input('length');
    //         $search = $request->input('search')['value'];

    //         // Query with pagination, search, and sorting
    //         $adminsQuery = Admin::select('id','name', 'username', 'email', 'phone', 'status', 'admin_access')
    //                             ->where('id', '!=', auth()->guard('admin')->id()) // Exclude the logged-in admin
    //                             ->when($search, function ($query, $search) {
    //                                 return $query->where('username', 'like', "%{$search}%")
    //                                             ->orWhere('email', 'like', "%{$search}%");
    //                             });

    //         // Get the total records for pagination
    //         $totalRecords = $adminsQuery->count();

    //         // Apply pagination
    //         $admins = $adminsQuery->offset($start) // Set the starting point for pagination
    //                             ->limit($length) // Set the page length
    //                             ->get();

    //         // Map the data for DataTables formatting
    //         $data = $admins->map(function ($admin) {
    //             // Generate the update route with the user ID
    //             $updateRoute = route('admin.updateStaff', $admin->id); // Use the Laravel route helper

    //             // Generate the action column with the edit button
    //             return [
    //                 'DT_RowIndex' => $admin->id,
    //                 'username' => $admin->username,
    //                 'email' => $admin->email,
    //                 'phone' => $admin->phone,
    //                 'status' => $admin->status == 1
    //                     ? '<span class="badge bg-success">Active</span>'
    //                     : '<span class="badge bg-danger">Deactive</span>',
    //                 'action' => adminAccessRoute(config('role.manage_staff.access.edit'))
    //                     ? '<button class="btn btn-primary btn-sm editAdminBtn"
    //                                 data-id="' . $admin->id . '"
    //                                 data-name="' . $admin->name . '"
    //                                 data-username="' . $admin->username . '"
    //                                 data-email="' . $admin->email . '"
    //                                 data-phone="' . $admin->phone . '"
    //                                 data-status="' . $admin->status . '"
    //                                 data-admin-access=\'' . json_encode($admin->admin_access) . '\'
    //                                 data-route="' . $updateRoute . '"
    //                                 data-bs-toggle="modal" data-bs-target="#editUserModal">
    //                             <i class="fa fa-edit"></i> Edit
    //                         </button>'
    //                     : ''
    //             ];
    //         });

    //         // Return the formatted data
    //         return response()->json([
    //             'draw' => $request->input('draw'),
    //             'recordsTotal' => $totalRecords,
    //             'recordsFiltered' => $totalRecords, // In this case, we're showing all records after search
    //             'data' => $data
    //         ]);
    //     }

    //     // Normal return when not AJAX
    //     return view('admin.staff.index', compact('pageTitle'));
    // }
    public function staff(Request $request)
    {
        $pageTitle = 'Manage Admin & Permission';

        // Check if the user has the 'view' permission
        if (!adminAccessRoute(config('role.manage_staff.access.view'))) {
            abort(403, 'Unauthorized');
        }

        // Handle DataTables AJAX request
        if ($request->ajax()) {
            // Prepare the query for Admins
            $admins = Admin::select(['id', 'name', 'username', 'email', 'phone', 'status', 'admin_access']);

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
                            data-admin-access=\'' . json_encode($admin->admin_access) . '\'
                            data-route="' . $updateRoute . '"
                            data-bs-toggle="modal" data-bs-target="#editUserModal">
                                <i class="fa fa-edit"></i>
                            </button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);  // Return the paginated data as expected by DataTables
        }

        // Normal return when not AJAX
        return view('admin.staff.index', compact('pageTitle'));
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

        // Create new admin instance
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->phone = $request->phone;

        // Hash the password
        $admin->password = Hash::make($request->password);

        // Handle admin access permissions if available
        $admin->admin_access = $request->has('access') ? explode(',', implode(',', $request->access)) : [];

        // Set status
        $admin->status = $request->status;

        // Save the admin
        $admin->save();

        // Flash success message
        // session()->flash('success', 'Added Successfully');

        // Redirect (adjust the route as necessary)
        return response()->json(['success' => true , 'message' => 'Admin Successfully Added.']);
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


        // Prepare admin_access array
        $admin_access = (isset($request->update_access)) ? explode(',', join(',', $request->update_access)) : [];

        try {
            // Update the admin record
            $admin->update([
                'name' => $validated['update-name'],
                'username' => $validated['update-username'],
                'email' => $validated['update-email'],
                'phone' => $validated['update-phone'] ?? null,
                'password' => $request->filled('update-password') ? Hash::make($validated['update-password']) : $admin->password,
                'admin_access' => $admin_access,
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
