<?php
namespace App\Http\Controllers;
use App\Models\Brand;
use Illuminate\Http\Request;
class BrandController extends Controller {
 public function index(){return view('brands.index',['brands'=>Brand::withCount('products')->orderBy('name')->paginate(20)]);}
 public function create(){return view('brands.form',['brand'=>new Brand(['active'=>true])]);}
 public function store(Request $r){Brand::create($r->validate(['name'=>'required|max:100|unique:brands,name','active'=>'nullable|boolean']));return redirect()->route('brands.index')->with('success','Brand saved.');}
 public function edit(Brand $brand){return view('brands.form',compact('brand'));}
 public function update(Request $r,Brand $brand){$brand->update($r->validate(['name'=>'required|max:100|unique:brands,name,'.$brand->id,'active'=>'nullable|boolean']));return redirect()->route('brands.index')->with('success','Brand updated.');}
 public function destroy(Brand $brand){if($brand->products()->exists())return back()->withErrors(['brand'=>'This brand is used by products. Set it inactive instead.']);$brand->delete();return back()->with('success','Brand deleted.');}
}
