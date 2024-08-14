<?php

namespace App\Repositories;

use App\Models\Permission;

class PermissionRepository
{
    public function getPermissions($request)
    {
        $query = Permission::query();
        if ($request->search) {
            $query->where('name', 'like', "%$request->search%");
        }
        $query->orderBy($request->sortBy, $request->sortDirection);
        return $query->paginate($request->perPage, ['*'], 'page', $request->page);
    }
    public function getAll()
    {
        return Permission::all();
    }
    public function getByUuid($uuid)
    {
        return Permission::find($uuid);
    }

    public function getByName($name)
    {
        return Permission::findByName($name);
    }

    public function store($request)
    {
        return Permission::create(['name' => $request->name]);
    }

    public function update($request, $permission)
    {
        return $permission->update(['name' => $request->name]);
    }

    public function destroy($permission)
    {
        return $permission->delete();
    }
}
