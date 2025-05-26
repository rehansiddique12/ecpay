<?php

namespace App\Http\Controllers\Admin;

// app/Http/Controllers/Admin/CategoryController.php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use App\Models\Gateway;

use App\Models\Category;
use App\Models\AccountGroup;
use Illuminate\Http\Request;

use App\Models\AccountGateway;
use App\Models\EWalletAccount;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $data['methods'] = Gateway::orderBy('sort_by', 'asc')->get();
        $data['categories'] = Category::all();

        $data['pageTitle'] = 'Accounts Management';
        $data['groups'] = AccountGroup::all();
        $this->updateLimits();

        $data['records'] = EWalletAccount::with(['apiHits' => function ($query) {
            $query->whereBetween('created_at', [now()->subSeconds(70), now()]);
        } ,'location'])->paginate(20);

        foreach ($data['records'] as $record) {
            $record->live = $record->apiHits ? 1 : 0; // If relation exists, set live = 1
        }

        return view('admin.accounts.ewallet_accounts', $data);
    }

    public function addAccount(Request $request)
    {
        $pageTitle = 'Add New Account';
        $categories = Category::select('name', 'id')->get();
        $methods = Gateway::select('name', 'id')->where('status', 1)->get();
        $groups = Group::all();
        $users_locations=UserLocation::where('status' , 1)->get();
        return view('admin.accounts.add_account', compact('pageTitle', 'categories', 'methods' , 'groups' ,'users_locations'));
    }

    public  function  addCategory(Request $request)
    {
        $pageTitle = 'Categories List';
        // $categories = Category::select('name' , 'id' , 'status')->get();

        if (request()->ajax()) {
            $categories = Category::orderBy('id', 'DESC');

            return DataTables::of($categories)
                ->addIndexColumn()

                ->addColumn('status', function ($category) {
                    $statusClass = $category->status == 1 ? 'bg-success' : 'bg-danger';
                    $statusText = $category->status == 1 ? 'Active' : 'Deactive';

                    return '<span class="toggle-status" data-id="' . $category->id . '" style="cursor:pointer;">' . ($category->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Deactive</span>') . '</span>';
                })
                ->addColumn('action', function ($category) {
                    return view('admin.accounts.partials.location-actions', compact('category'))->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.accounts.add_category', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        // Use manual validation to return JSON on failure
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create($validator->validated());

        return response()->json([
            'success' => 'true',
            'message' => 'Category created successfully.',
            'data' => $category
        ]);
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'edit_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($id),
            ],
            'edit_status' => 'required|boolean',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.'
            ], 404);
        }

        $category->update([
            'name' => $request->input('edit_name'),
            'status' => $request->input('edit_status'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully.',
            'data' => $category,
        ]);
    }


    public function destroy(Request $request)
    {
        $id = (int)$request->input('id');
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ]);
    }


    public function gateway()
    {
        $pageTitle = 'Gateways';
        if (request()->ajax()) {
            $gateways = Gateway::orderBy('id', 'DESC');

            return DataTables::of($gateways)
                ->addIndexColumn()
                ->editColumn('status', function ($gateways) {
                    $toggleRoute = route('admin.accounts.payment.methods.deactivate', $gateways->id);

                    return '<span class="toggle-status"
                                data-id="' . $gateways->id . '"
                                data-url="' . $toggleRoute . '"
                                style="cursor: pointer;">
                                ' . ($gateways->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Deactive</span>') . '
                            </span>';
                })
                ->addColumn('action', function ($gateway) {
                    return view('admin.accounts.partials.gateway-actions', compact('gateway'))->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        $categories = Category::all();
        return view('admin.accounts.add_gateway', compact('pageTitle' , 'categories'));
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

    public function getAccountsByCategory($category_id)
    {
        $accounts = Gateway::where('category_id', $category_id)
                        ->where('status', 1)
                        ->get(['id', 'name', 'currency']);

        return response()->json($accounts);
    }
}
