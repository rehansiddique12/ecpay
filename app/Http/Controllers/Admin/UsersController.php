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


    public function roles_and_permission()
    {
        $userLocations = UserLocation::orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        $pageTitle = 'Roles_and_Permission';
        return view('admin.users.rolespermission', compact('userLocations', 'pageTitle'));
    }


    public function rolesCategory()
    {
        $UserRoles =UserRoles ::orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        $pageTitle = 'User Roles';
        return view('admin.users.user-roles', compact('UserRoles', 'pageTitle'));
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
            'role' => 'required|string|unique:user_roles,roles_name'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        UserRoles::create([
            'roles_name' => $request->role
        ]);

        session()->flash('success', 'Role added successfully');
        return back();
    }

    public function updateRole(Request $request, $id)
{
    $request->validate([
        'roles_name' => 'required|string|max:255',
    ]);

    $role = UserRoles::findOrFail($id);
    $role->roles_name = $request->roles_name;
    $role->save();

    session()->flash('success', 'Role updated successfully!');
    return back(); // redirect wherever you want
}


    public function deleteRole($id)
    {
        try {
            $role = UserRoles::findOrFail($id);
            $role->delete();
            session()->flash('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting role: ' . $e->getMessage());
        }
        return back();
    }

}
