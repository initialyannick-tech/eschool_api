<?php

namespace Modules\Admin\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Admin\Models\Permission;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Admin\Transformers\RoleResource;

class RoleRepository
{
    public function paginate(): AnonymousResourceCollection
    {
        $roles = Role::orderBy('id', 'desc')->with(['permissions'])-> paginate(10);
        return RoleResource::collection($roles);
    }


    public function liste(): AnonymousResourceCollection
    {
        $roles = Role::all();
        return RoleResource::collection($roles);
    }

    public function store($data): false|Role
    {
        $permissions = $data['permissions'];
        $role = new Role;
        $role->fill($data);
        if($role->save()){
            $role->permissions()->sync($permissions);
            return $role;
        }
        return false;
    }

    public function update($data, $id)
    {
        $permissions = $data['permissions'];
        $role = Role::whereId($id)->first();
        $role->fill($data);
        if($role->save()){
            $role->permissions()->sync($permissions);
            return $role;
        }
        return false;
    }

    public function destroy($id)
    {
        $users = User::where('role_id', $id)->count();
        if($users > 0){
            return false;
        } else {
            $role = Role::find($id);
            if($role->delete()){
                return true;
            }
        }
    }


    public function permissions()
    {
        return Permission::all();
    }
}

