<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{

     // List all settings
     public function index()
     {
         $settings = Setting::orderBy('id', 'desc')->get();
         $pageTitle = 'Settings';
         return view('admin.settings.index', compact('settings','pageTitle'));
     }



     public function store(Request $request)
     {
         $request->validate([
             'name' => 'required|string|unique:settings,name',
             'value' => 'required|string',
         ]);

         $setting = Setting::create($request->only('name', 'value'));

         return response()->json(['success' => true, 'message' => 'Setting added successfully.', 'data' => $setting]);
     }

     public function update(Request $request, $id)
     {
         $setting = Setting::findOrFail($id);

         $request->validate([
             'name' => 'required|string|unique:settings,name,' . $id,
             'value' => 'required|string',
         ]);

         $setting->update($request->only('name', 'value'));

         return response()->json(['success' => true, 'message' => 'Setting updated successfully.', 'data' => $setting]);
     }

     // Delete setting
     public function destroy($id)
     {
         $setting = Setting::findOrFail($id);
         $setting->delete();

         return redirect()->route('admin.settings.index')->with('success', 'Setting deleted successfully.');
     }
}
