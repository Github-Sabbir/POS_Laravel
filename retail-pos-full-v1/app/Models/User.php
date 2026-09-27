<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
    use Notifiable;
    protected $fillable=['name','email','password','role','role_id','active'];
    protected $hidden=['password','remember_token'];
    protected $casts=['active'=>'boolean','password'=>'hashed'];
    public function roleModel(){ return $this->belongsTo(Role::class, 'role_id'); }
    public function hasPermission(string $permission): bool {
        if ($this->role === 'admin') return true;
        return $this->roleModel && $this->roleModel->permissions()->where('slug',$permission)->exists();
    }
}
