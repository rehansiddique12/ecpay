<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Language;
use App\Models\PayoutLog;
use App\Models\UserLocation;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Api;

class UsersController extends Controller
{

    public function userEdit($id)
    {
        $user = User::findOrFail($id);
        $languages = Language::all();
        $pageTitle = ' User Edit';
        return view('admin.users.edit-user', compact('user','languages', 'pageTitle'));
    }


    public function index()
    {
        $users = User::orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        $pageTitle = 'User Management';
        return view('admin.users.list', compact('users', 'pageTitle'));
    }



    public function location()
    {
        $userLocations = UserLocation::orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        $pageTitle = 'User Locations';
        return view('admin.users.user-location', compact('userLocations', 'pageTitle'));
    }

    public function roles_and_permission(Request $request)
    {
        $userLocations = UserLocation::orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        $pageTitle = 'Roles_and_Permission';

        // Get all roles
        $roles_list = UserRoles::where('used_for', 'Admin')->get();

        // Get the selected role ID from the URL (e.g., ?role_select=1)
        $selectedRoleId = $request->input('role_select');

        // Fetch the selected role (if any)
        $selectedRole = null;
        $storedPermissions = [];

        if ($selectedRoleId) {
            $selectedRole = UserRoles::find($selectedRoleId);
            if ($selectedRole) {
                $storedPermissions = json_decode($selectedRole->admin_access, true);
            }
        }
        // dd($storedPermissions);
        return view('admin.users.rolespermission', compact(
            'pageTitle',
            'roles_list',
            'selectedRoleId',
            'selectedRole',
            'storedPermissions'
        ));
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = UserRoles::find($id);

        if (!$role) {
            return back()->with('error', 'Role not found!');
        }

        // Convert access to array
        $access = (isset($request->access)) ? explode(',',join(',',$request->access)) : [];

        // Update role's access
        $role->admin_access = $access;
        $role->save();

        // Find all users with this role and update their admin_access
        $all_users = Admin::where('role_type', $role->name)->get();
        foreach ($all_users as $user) {
            $user->admin_access = $access;
            $user->save();
        }

        return back()->with('success', 'Permissions updated!');
    }
    public function rolesCategory()
    {
        $roles_select_box = UserRoles::where('used_for', 'Admin')->pluck('name', 'name');
        if (request()->ajax()) {
            $roles = UserRoles::where('used_for', 'Admin')->select(['id', 'name']);

            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('action', function ($role) {
                    return view('admin.users.partials.role-actions', compact('role'))->render();
                })
                ->rawColumns(['action']) // Allow HTML for action buttons
                ->make(true);
        }

        $pageTitle = 'User Roles';
        return view('admin.users.user-roles', compact('pageTitle' , 'roles_select_box'));
    }



    public function search(Request $request)
    {
        $search = $request->all();
        $dateSearch = $request->date_time;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);
        $users = User::when(isset($search['search']), function ($query) use ($search) {
            return $query->where('email', 'LIKE', "%{$search['search']}%")
                ->orWhere('username', 'LIKE', "%{$search['search']}%");
        })
            ->when($date == 1, function ($query) use ($dateSearch) {
                return $query->whereDate("created_at", $dateSearch);
            })
            ->when(isset($search['status']), function ($query) use ($search) {
                return $query->where('status', $search['status']);
            })
            ->paginate(config('basic.paginate'));
            $pageTitle = 'User Management';
        return view('admin.users.list', compact('users', 'search', 'pageTitle'));
    }


    // public function userAdd(Request $request)
    // {
    //     // Validate request
    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required|string',
    //         'status' => 'required',
    //         'password' => 'required|string|min:5',
    //     ]);

    //     // Create and save API entry
    //     Api::create([
    //         'username' => $request->username,
    //         'location' => $request->location,
    //         'roles' => $request->roles,
    //         'password' => bcrypt($request->password), // Secure password hashing
    //         'status' => $request->status,
    //     ]);

    //     session()->flash('success', 'Added Successfully');
    //     return back();
    // }

    public function updateUserLocation(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'location' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Find and update user
        $user = User::findOrFail($request->id);
        $user->location = $request->location;
        $user->status = $request->status;
        $user->save();

        session()->flash('success', 'Location updated successfully');
        return back();
    }

    public function addUserLocation(Request $request)
{
    if ($request->isMethod('GET')) {
        $pageTitle = 'Add User Location';
        $userLocation = null; // Important!
        return view('admin.users.add-location', compact('pageTitle', 'userLocation'));
    }

    $validator = Validator::make($request->all(), [
        'location' => 'required|string',
        'status' => 'required|boolean',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    UserLocation::create($request->only('location', 'status'));

    session()->flash('success', 'User location added successfully');
    return redirect()->route('admin.location');
}


    public function editUserLocation($id)
    {
        $userLocation = UserLocation::findOrFail($id);
        $pageTitle = 'Edit User Location';
        return view('admin.users.edit-location', compact('userLocation', 'pageTitle'));
    }

    public function updateUserLocationDetails(Request $request, $id)
{
    // dd($request->all());
    $validated = $request->validate([
        'location' => 'required|string',
        'status' => 'required|boolean',
    ]);

    $userLocation = UserLocation::findOrFail($id);
    $userLocation->update($validated);

    session()->flash('success', 'User location updated successfully');
    return redirect()->route('admin.location');
}


    public function deleteUserLocation($id)
    {
        $userLocation = UserLocation::findOrFail($id);
        $userLocation->delete();

        session()->flash('success', 'User location deleted successfully');
        return back();
    }

    public function addRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|unique:user_roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        UserRoles::create([
            'name' => $request->role,
            'used_for' => 'Admin'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Role added successfully'
        ]);
    }

    public function copyRole(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'add_new_role' => 'required|string|unique:user_roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $existingRole = UserRoles::where('name', $request->copy_role_name)->first();
        if (!$existingRole) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }


        UserRoles::create([
            'name' => $request->add_new_role,
            'used_for' => 'Admin',
            'admin_access' => $existingRole->admin_access,
        ]);

        session()->flash('success', 'Role added successfully');
        return back();
    }

    public function updateRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'roles_name' => 'required|string|max:255|unique:user_roles,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = UserRoles::findOrFail($id);
            $role->name = $request->roles_name;
            $role->used_for = "Admin";

            if ($role->save()) {
                return response()->json(['status' => 'success', 'message' => 'Role updated successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Failed to update the role']);
            }

        } catch (QueryException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'General error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteRole(Request $request)
    {
        $id = (int)$request->input('id');
        try {
            $role = UserRoles::findOrFail($id);
            if ($role->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Role deleted successfully']);
            }

        } catch (\Exception $e) {
           return response()->json(['status' => 'error', 'message' => 'Error deleting role: ' . $e->getMessage()]);
        }
    }

    public function getRoles()
    {
        $roles = UserRoles::where('used_for' , 'Admin')->get(['id', 'name']); // latest roles first
        return response()->json(['roles' => $roles]);
    }

}
