<?php

namespace App\Http\Controllers\Admin;

// app/Http/Controllers/Admin/CategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountGateway;

use App\Models\EWalletAccount;
use App\Models\Category;
use App\Models\AccountGroup;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $data['methods'] = AccountGateway::orderBy('sort_by', 'asc')->get();
        $data['categories'] = Category::all();
        $data['pageTitle'] = 'Accounts Management';
        $data['groups'] = AccountGroup::all();
        $this->updateLimits();

        $data['records'] = EWalletAccount::with(['apiHits' => function ($query) {
            $query->whereBetween('created_at', [now()->subSeconds(70), now()]);
        }])->paginate(20);

        foreach ($data['records'] as $record) {
            $record->live = $record->apiHits ? 1 : 0; // If relation exists, set live = 1
        }
        return view('admin.accounts.ewallet_accounts', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',

        ]);

        Category::create($validated);

        return redirect()->back()->with('success', 'Category created.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',

        ]);

        $category = Category::findOrFail($id);
        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted.');
    }

    public function updateLimits()
    {
        $todayDate = now()->toDateString();  // Use Carbon for better date handling
        $thisMonth = now()->month;

        EWalletAccount::where('last_limit_reset', '!=', $todayDate)
            ->update([
                'daily_received' => 0,
                'daily_sent' => 0,
                'last_limit_reset' => $todayDate
            ]);

        EWalletAccount::whereMonth('last_limit_reset', '!=', $thisMonth)
            ->update([
                'monthly_received' => 0,
                'monthly_sent' => 0
            ]);
    }

    public function changeStatus($id)
{
    $category = Category::findOrFail($id);
    $category->status = $category->status == 1 ? 0 : 1;
    $category->save();
    return response()->json([
        'success' => true,
        'status' => $category->status,
    ]);
}


}
