<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api;
use App\Models\User;
use App\Models\Admin;
use App\Models\Language;
use App\Models\PayoutLog;
use App\Models\UserRoles;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    use Upload, Notify;

    public function userEdit($id)
    {
        $user = User::findOrFail($id);
        $languages = Language::all();
        $pageTitle = ' User Edit';
        return view('admin.users.edit-user', compact('user','languages', 'pageTitle'));
    }

     public function passwordUpdate(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:5|same:password_confirmation',
        ]);
        $user = User::findOrFail($id);
        $user->password = bcrypt($request->password);
        $user->save();

        $this->sendMailSms($user, 'PASSWORD_CHANGED', [
            'password' => $request->password
        ]);
        return back()->with('success', 'Updated Successfully.');
    }



  public function userUpdate(Request $request, $id)
    {
        $languages = Language::all()->pluck('id');

        $userData = Purify::clean($request->except('_token', '_method'));
        $user = User::findOrFail($id);

        $rules = [
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|required|string|max:20',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'language_id' => ['nullable', Rule::in($languages)],
            'address' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($userData, $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?? null;
                $user->image = $this->uploadImage(
                    $request->image,
                    config('location.user.path'),
                    config('location.user.size'),
                    $old
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Image could not be uploaded.');
            }
        }

        $user->email = $userData['email'] ?? $user->email;
        $user->phone = $userData['phone'] ?? $user->phone;
        $user->language_id = $userData['language_id'] ?? $user->language_id;
        $user->address = $userData['address'] ?? $user->address;

        $user->status = isset($userData['status']) && $userData['status'] === 'on' ? 0 : 1;
        $user->email_verification = isset($userData['email_verification']) && $userData['email_verification'] === 'on' ? 0 : 1;
        $user->sms_verification = isset($userData['sms_verification']) && $userData['sms_verification'] === 'on' ? 0 : 1;
        $user->two_fa_verify = isset($userData['two_fa_verify']) && $userData['two_fa_verify'] === 'on' ? 1 : 0;

        $user->save();

        return back()->with('success', 'User updated successfully.');
    }



    public function index( Request $request)
    {
        $users = User::orderBy('id', 'DESC')->paginate(config('basic.paginate'));

        $locations = UserLocation::pluck('location', 'id');
        $userRoles = UserRoles::where('used_for', 'Admin')->pluck('name', 'id');

        if ($request->ajax()) {
            // Prepare the query for Admins


            $query = Admin::with(['location'])
                ->select(['id', 'name', 'username', 'email', 'phone', 'status', 'admin_access', 'role_type', 'location_id' ,'last_login'])->where('id', '!=', 76);
                // Apply filters
                if ($request->filled('location')) {
                    $query->where('location_id', $request->location);
                }

                if ($request->filled('role_type')) {
                    $query->where('role_type', $request->role_type);
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

            // Process the DataTables response with pagination, search, etc.
                // Return DataTables response
                return DataTables::of($query)
                    ->editColumn('status', function ($admin) {
                        $toggleRoute = route('admin.toggleStaffStatus', $admin->id);

                        return '<span class="toggle-status"
                                    data-id="' . $admin->id . '"
                                    data-url="' . $toggleRoute . '"
                                    style="cursor: pointer;">
                                    ' . ($admin->status == 1 ? '<span class="badge bg-success">' . __('accounts.active') . '</span>' : '<span class="badge bg-danger">' . __('accounts.inactive') . '</span>') . '
                                </span>';
                    })
                    ->editColumn('role_type', function ($admin) {
                        return isset($admin->role_type) ? $admin->role_type : 'N/A';
                    })
                    ->addColumn('location_name', function ($admin) {
                        return isset($admin->location_id) && $admin->location
                            ? $admin->location->location
                            : 'N/A';
                    })
                    ->addColumn('last_login_human', function ($admin) {
                        return $admin->last_login ? \Carbon\Carbon::parse($admin->last_login)->diffForHumans() : 'Never';
                    })
                    ->addColumn('action', function ($admin) {
                        $updateRoute = route('admin.updateStaff', ':id');

                        return '<button class="btn btn-sm btn-primary editAdminBtn"
                                data-id="' . $admin->id . '"
                                data-name="' . $admin->name . '"
                                data-username="' . $admin->username . '"
                                data-email="' . $admin->email . '"
                                data-phone="' . $admin->phone . '"
                                data-status="' . $admin->status . '"
                                data-role-type="' . $admin->role_type . '"
                                data-location="' . $admin->location_id . '"
                                data-admin-access=\'' . json_encode($admin->admin_access) . '\'
                                data-route="' . $updateRoute . '"
                                data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <i class="fa fa-edit"></i>
                            </button>';
                    })
                    ->rawColumns(['status', 'location_name', 'action'])
                    ->toJson();
                // ->make(true);
        }

        $pageTitle =  __('userManagement.user_managment') ;
        return view('admin.users.list', compact('users', 'pageTitle' , 'locations', 'userRoles'));
    }

    public function toggleStaffStatus($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->status = $admin->status == 1 ? 0 : 1;
        $admin->save();

        return response()->json([
            'success' => true,
            'new_status' => $admin->status,
            'message' => 'Status updated successfully.',
        ]);
    }


   public function storeStaff(Request $request)
    {

        //return response()->json(['success' => false, 'message' => $request->role_type]);
        // Validation
        $validated = $request->validate([
            'name' => 'required|max:191',
            'username' => 'required|alpha_dash|unique:admins,username',
            'email' => 'nullable|email|max:191|unique:admins,email',
            'location' => 'required|exists:user_locations,id',
            'role_type' => [
                'required',
                Rule::exists('user_roles', 'name')->where(function ($query) {
                    $query->where('status', 1);
                }),
            ],
            'password' => 'required|min:5',
            'status' => 'required|in:0,1',
            // 'access' => 'array',
        ]);
        //return response()->json(['success' => false, 'message' => $validated['role_type']]);
        $find_role = UserRoles::where('name', $validated['role_type'])->first();

        // Create new admin instance
        $admin = new Admin();
        $admin->name = $validated['name'];
        $admin->username = $validated['username'];
        $admin->email = $validated['email'];
        $admin->phone = $request->phone;
        $admin->role_type = $find_role->name;
        $admin->location_id = $validated['location'];
        $admin->admin_access =  json_decode((string) $find_role->admin_access, true);
        // Hash the password
        $admin->password = Hash::make($request->password);

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
        // Validation
        $validated = $request->validate([
            'update-name' => 'required|string|max:191',
            'update-username' => [
                'required',
                'alpha_dash',
                Rule::unique('admins', 'username')->ignore($admin->id),
            ],
            'update-email' => [
                'nullable',
                'email',
                'max:191',
                Rule::unique('admins', 'email')->ignore($admin->id),
            ],
            'update-password' => 'nullable|min:5',
            'update-status' => 'required|in:0,1',
            'update-phone' => 'nullable|string|max:20',
            'edit_location' => [
                'required',
                Rule::exists('user_locations', 'id'),
            ],
            'role_type_edit' => [
                'required',
                Rule::exists('user_roles', 'name')->where(function ($query) {
                    $query->where('status', 1);
                }),
            ],
        ], [], [
            'update-name' => 'Name',
            'update-username' => 'Username',
            'update-email' => 'Email',
            'update-password' => 'Password',
            'update-status' => 'Status',
            'update-phone' => 'Phone',
            'edit_location' => 'Location',
            'role_type_edit' => 'Role',
        ]);

        $find_role = UserRoles::where('name', $request->role_type_edit)->first();

        try {
            // Update the admin record
            $admin->update([
                'name' => $validated['update-name'],
                'username' => $validated['update-username'],
                'email' => $validated['update-email'],
                'phone' => $validated['update-phone'] ?? null,
                'password' => $request->filled('update-password') ? Hash::make($validated['update-password']) : $admin->password,
                'role_type' => $find_role->name,
                'location_id' => $validated['edit_location'],
                'admin_access' => json_decode((string) $find_role->admin_access, true),
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

    public function location()
    {
        // $userLocations = UserLocation::orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        // $pageTitle = 'User Locations';
        // return view('admin.users.user-location', compact('userLocations', 'pageTitle'));

        if (request()->ajax()) {
            $locations = UserLocation::orderBy('id', 'DESC');

            return DataTables::of($locations)
                ->addIndexColumn()
                ->addColumn('location', function ($location) {
                    return $location->location;
                })
               ->addColumn('status', function ($location) {
                    $statusClass = $location->status == 1 ? 'bg-success' : 'bg-danger';
                    $statusText = $location->status == 1 ? 'Active' : 'Deactive';

                    return '<span class="toggle-status" data-id="'.$location->id.'" style="cursor:pointer;">'.($location->status == 1 ? '<span class="badge bg-success">' . __('userManagement.active') . '</span>' : '<span class="badge bg-danger">' . __('userManagement.inactive') . '</span>').'</span>';
                })
                ->addColumn('action', function ($location) {
                    return view('admin.users.partials.location-actions', compact('location'))->render();
                })
                ->rawColumns(['action' , 'location' ,'status'])
                ->make(true);
        }

        $pageTitle =  __('userManagement.user_locations') ;
        return view('admin.users.user-location', compact('pageTitle'));


    }

    public function toggleLocationStatus(Request $request)
    {
        $location = UserLocation::findOrFail($request->id);
        $location->status = $location->status == 1 ? 0 : 1;
        $location->save();

        return response()->json([
            'success' => true,
            'status' => $location->status,
            'message' => 'Location status updated successfully.',
        ]);
    }


    public function roles_and_permission(Request $request)
    {
        $userLocations = UserLocation::orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        $pageTitle =  __('userManagement.roles_and_permission') ;

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

        $pageTitle =  __('userManagement.role_categories') ;
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
        $request->validate([
            'location' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => 'required|boolean',
        ]);

        $existing = UserLocation::withTrashed()
        ->where('location', $request->location)
        ->first();

        if ($existing && is_null($existing->deleted_at)) {
            return response()->json([
                'success' => false,
                'message' => 'The location already exists.'
            ], 409);
        }

        if ($existing && !is_null($existing->deleted_at)) {
            // Restore and update the soft-deleted record
            $existing->restore();
            $existing->status = $request->status;
            $existing->save();
        } else {
            // Create a new record
            UserLocation::create([
                'location' => $request->location,
                'status' => $request->status,
            ]);
        }

        return response()->json(['success' => true]);
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


    public function deleteUserLocation(Request $request)
    {
       $id = (int)$request->input('id');
        try {
            $role = UserLocation::findOrFail($id);
            if ($role->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Location deleted successfully']);
            }

        } catch (\Exception $e) {
           return response()->json(['status' => 'error', 'message' => 'Error deleting role: ' . $e->getMessage()]);
        }
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
