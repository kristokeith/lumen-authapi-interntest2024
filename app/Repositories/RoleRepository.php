<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    protected $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function getRoles($request)
    {
        $query = Role::query();
        if ($request->search) {
            $query->where('name', 'like', "%$request->search%");
        }
        $query->orderBy($request->sortBy, $request->sortDirection);
        return $query->paginate($request->perPage, ['*'], 'page', $request->page);
    }

    public function getAll()
    {
        return Role::all();
    }

    public function getByUuid($uuid)
    {
        return Role::find($uuid);
    }

    public function getByName($name)
    {
        return Role::findByName($name);
    }

    public function store($request)
    {
        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($this->getPermissionsName($request->uuid_permissions));
        return $role;
    }

    public function update($request, $role)
    {
        $role->update(['name' => $request->name]);
        $role->syncPermissions($this->getPermissionsName($request->uuid_permissions));
        return $role;
    }

    public function destroy($role)
    {
        return $role->delete();
    }

    public function getRolePermissions($role)
    {
        return $role->permissions;
    }

    public function getPermissionsName($uuid_permissions)
    {
        $permission = [];
        foreach ($uuid_permissions as $uuid_permission) {
            $permission[] = $this->permissionRepository->getByUuid($uuid_permission)->name;
        }
        return $permission;
    }


    public function syncRolePermissions($role, $permissions)
    {
        return $role->syncPermissions($permissions);
    }
}
