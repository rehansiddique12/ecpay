<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayoutLog;
use App\Models\User;
use App\Models\Language;

class UsersController extends Controller
{

    public function userEdit($id)
    {
        $user = User::findOrFail($id);
        $languages = Language::all();
        return view('admin.users.edit-user', compact('user','languages'));
    }

}
