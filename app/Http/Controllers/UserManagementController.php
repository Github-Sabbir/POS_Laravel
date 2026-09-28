<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserManagementController extends Controller {
    public function index(){ $users=User::with('roleModel')->orderBy('name')->get();$roles=Role::with('permissions')->orderBy('name')->get();return view('users.index',compact('users','roles')); }
    public function create(){return view('users.form',['user'=>new User(),'roles'=>Role::orderBy('name')->get()]);}
    public function store(Request $request){
        $data=$request->validate(['name'=>'required|string|max:120','email'=>'required|email|max:190|unique:users,email','password'=>'required|string|min:6|confirmed','role_id'=>'required|exists:roles,id','active'=>'nullable|boolean']);
        $role=Role::findOrFail($data['role_id']);
        User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>$data['password'],'role'=>$role->slug,'role_id'=>$role->id,'active'=>$request->boolean('active',true)]);
        return redirect()->route('users.index')->with('success','User created successfully.');
    }
    public function edit(User $user){return view('users.form',['user'=>$user,'roles'=>Role::orderBy('name')->get()]);}
    public function update(Request $request,User $user){
        $data=$request->validate(['name'=>'required|string|max:120','email'=>'required|email|max:190|unique:users,email,'.$user->id,'password'=>'nullable|string|min:6|confirmed','role_id'=>'required|exists:roles,id','active'=>'nullable|boolean']);
        $role=Role::findOrFail($data['role_id']);
        if($this->isLastAdminBeingChanged($user,$role,$request->boolean('active',true))) return back()->withErrors(['role'=>'The last active Admin account must remain active and keep the Admin role.']);
        if($user->id===$request->user()->id && !$request->boolean('active',true)) return back()->withErrors(['active'=>'You cannot deactivate your own account.']);
        $payload=['name'=>$data['name'],'email'=>$data['email'],'role'=>$role->slug,'role_id'=>$role->id,'active'=>$request->boolean('active',true)];
        if(!empty($data['password']))$payload['password']=$data['password'];
        $user->update($payload);return redirect()->route('users.index')->with('success','User updated successfully.');
    }
    public function destroy(Request $request,User $user){
        if($user->id===$request->user()->id)return back()->withErrors(['user'=>'You cannot delete your own account.']);
        if($user->role==='admin' && User::where('role','admin')->where('active',true)->count()<=1)return back()->withErrors(['user'=>'The last active Admin account cannot be deleted.']);
        $user->delete();return back()->with('success','User deleted successfully.');
    }
    public function roles(){return view('users.roles',['roles'=>Role::with('permissions')->orderBy('name')->get(),'permissions'=>Permission::orderBy('group')->orderBy('name')->get()]);}
    public function storeRole(Request $request){
        $data=$request->validate(['name'=>'required|string|max:80|unique:roles,name','description'=>'nullable|string|max:255','permissions'=>'nullable|array','permissions.*'=>'exists:permissions,id']);
        $slug=Str::slug($data['name']);if(Role::where('slug',$slug)->exists())return back()->withErrors(['name'=>'A role with this name/slug already exists.']);
        $role=Role::create(['name'=>$data['name'],'slug'=>$slug,'description'=>$data['description']??null,'is_system'=>false]);$role->permissions()->sync($data['permissions']??[]);return back()->with('success','Role created successfully.');
    }
    public function updateRole(Request $request,Role $role){
        $data=$request->validate(['name'=>'required|string|max:80|unique:roles,name,'.$role->id,'description'=>'nullable|string|max:255','permissions'=>'nullable|array','permissions.*'=>'exists:permissions,id']);
        $slug=$role->slug==='admin'?'admin':Str::slug($data['name']);
        if(Role::where('slug',$slug)->where('id','<>',$role->id)->exists())return back()->withErrors(['name'=>'A role with this name/slug already exists.']);
        $role->update(['name'=>$data['name'],'slug'=>$slug,'description'=>$data['description']??null]);$role->permissions()->sync($data['permissions']??[]);return back()->with('success','Role updated successfully.');
    }
    public function destroyRole(Role $role){if($role->is_system||$role->slug==='admin')return back()->withErrors(['role'=>'System roles cannot be deleted.']);if($role->users()->exists())return back()->withErrors(['role'=>'This role is assigned to users. Reassign them before deleting it.']);$role->delete();return back()->with('success','Role deleted successfully.');}
    private function isLastAdminBeingChanged(User $user,Role $newRole,bool $active):bool {return $user->role==='admin' && $user->active && User::where('role','admin')->where('active',true)->whereKeyNot($user->id)->count()===0 && ($newRole->slug!=='admin'||!$active);}
}
