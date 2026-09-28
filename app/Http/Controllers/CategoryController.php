<?php
namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
class CategoryController extends Controller {
 public function index(){return view('categories.index',['categories'=>Category::withCount('products')->orderBy('name')->paginate(20)]);}
 public function create(){return view('categories.form',['category'=>new Category(['active'=>true])]);}
 public function store(Request $r){Category::create($r->validate(['name'=>'required|max:100|unique:categories,name','active'=>'nullable|boolean']));return redirect()->route('categories.index')->with('success','Category saved.');}
 public function edit(Category $category){return view('categories.form',compact('category'));}
 public function update(Request $r,Category $category){$category->update($r->validate(['name'=>'required|max:100|unique:categories,name,'.$category->id,'active'=>'nullable|boolean']));return redirect()->route('categories.index')->with('success','Category updated.');}
 public function destroy(Category $category){if($category->products()->exists())return back()->withErrors(['category'=>'This category is used by products. Set it inactive instead.']);$category->delete();return back()->with('success','Category deleted.');}
}
