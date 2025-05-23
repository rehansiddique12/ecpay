<?php

namespace App\Http\Controllers;

use App\Models\CCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CCategoryController extends Controller
{
    // Show all categories
    public function index()
    {
        try {
            $pageTitle = 'Commission Categories';
            $records = CCategory::paginate(10);
            return view('commission_category.index', compact('records', 'pageTitle'));
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to fetch categories');
        }
    }

    // Store a new category
    public function store(Request $request)
    {
       
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            CCategory::create([
                'title' => $request->name,
                'status' => $request->status ?? 1,
            ]);

            return redirect()->back()->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create category.');
        }
    }

    // Show a single category (optional)
    public function show($id)
    {
        try {
            $category = CCategory::findOrFail($id);
            return view('commission_category.show', compact('category'));
        } catch (\Exception $e) {
            Log::error('Error fetching category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Category not found.');
        }
    }

    // Update a category
public function update(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255'
    ]);

    try {
        $category = CCategory::findOrFail($request->id);
        $category->update([
            'title' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Category updated successfully.');
    } catch (\Exception $e) {
        Log::error('Error updating category: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to update category.');
    }
}



    // Delete a category
  public function destroy(Request $request)
{   
    
    try {
        $category = CCategory::findOrFail($request->id);
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    } catch (\Exception $e) {
        \Log::error('Error deleting category: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to delete category.');
    }
}

}
