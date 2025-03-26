<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Purify\Facades\Purify;
use \Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    use Upload;

    public function __construct()
    {
        // dd('hello');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }


    public function profile()
{
    $pageTitle = 'Admin Profile';
    $admin = Auth::guard('admin')->user(); // Ensure admin data is being passed
    return view('admin.profile', compact('pageTitle', 'admin'));
}

public function profileUpdate(Request $request)
{
    $req = $request->except('_token', '_method');

    // Strip tags to prevent XSS
    $req['name'] = strip_tags($req['name'] ?? '');
    $req['username'] = strip_tags($req['username'] ?? '');
    $req['email'] = filter_var($req['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $req['phone'] = strip_tags($req['phone'] ?? '');
    $req['address'] = strip_tags($req['address'] ?? '');

    // Validation rules
    $rules = [
        'name' => 'sometimes|required|string|max:255',
        'username' => 'sometimes|required|string|max:255|unique:admins,username,' . $this->user->id,
        'email' => 'sometimes|required|email|max:255|unique:admins,email,' . $this->user->id,
        'phone' => 'sometimes|required|string|max:20',
        'address' => 'sometimes|required|string|max:500',
        'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048' // 2MB max
    ];

    $validator = Validator::make($req, $rules);
    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $user = $this->user;

    // ✅ Fix Image Upload
    if ($request->hasFile('image')) {
        try {
            $image = $request->file('image');
            $filename = 'admin_' . time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/admin/');

            // Create directory if not exists
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Move file to directory
            $image->move($path, $filename);

            // Delete old image if exists
            if ($user->image && file_exists(public_path('uploads/admin/' . $user->image))) {
                unlink(public_path('uploads/admin/' . $user->image));
            }

            // Save new image filename
            $user->image = $filename;
        } catch (\Exception $exp) {
            return back()->with('error', 'Image could not be uploaded.');
        }
    }

    // Update user details
    $user->name = $req['name'];
    $user->username = $req['username'];
    $user->email = $req['email'];
    $user->phone = $req['phone'];
    $user->address = $req['address'];
    $user->save();

    return back()->with('success', 'Profile Updated Successfully.');
}

    public function dashboard()
    {
        $pageTitle = 'Dashboard';
        return view('admin.dashboard', compact('pageTitle'));
    }



    public function password()
    {
        $pageTitle = 'Admin Profile';
        return view('admin.password', compact('pageTitle'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = auth()->user(); // Get authenticated user

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', "Password didn't match");
        }

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return back()->with('success', 'Password has been changed.');
    }
}
