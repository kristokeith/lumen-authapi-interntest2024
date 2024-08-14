<?php

namespace App\Repositories;

use App\Models\SpvStaff;
use App\Models\StaffRegional;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getUsers($perPage = 10, $page = 1, $search = null, $sortBy = 'uuid', $sortDirection = 'asc', $roles_uuid = null)
    {
        $query = User::query();

        if ($roles_uuid) {
        $query->join('model_has_roles', 'users.uuid', '=', 'model_has_roles.model_uuid')
            ->select('users.*', 'model_has_roles.role_id')
            ->where('model_has_roles.role_id', '=', $roles_uuid);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('users.uuid', 'like', "%$search%")
                    ->orWhere('users.name', 'like', "%$search%");
            });
        }

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }


    public function getAll()
    {
        return User::all();
    }

    public function getByUuid($uuid)
    {
        return User::find($uuid);
    }

    public function getByName($name)
    {
        return User::findByName($name);
    }

    public function store($request)
    {
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
        ])->assignRole($this->getRoleName($request->uuid_roles));
        return $user;
    }

    public function update($request, $user)
    {
        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
        ];

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->uuid_roles) {
            $user->syncRoles($this->getRoleName($request->uuid_roles));
        }
        return $user;
    }

    public function destroy($user)
    {
        return $user->delete();
    }


    public function getUserRoles($user)
    {
        return $user->roles;
    }

    public function getRoleName($uuid)
    {
        return $this->roleRepository->getByUuid($uuid)->name;
    }
    public function getProfile($uuid)
    {
        return User::with('roles')->where('uuid', $uuid)->firstOrFail();
    }

    public function getUserByRole($role_name = null, $perPage = null, $page = 1)
    {
        if (!$role_name) {
            $role_name = 'staff';
        }
        $role_uuid = $this->roleRepository->getByName($role_name)->uuid;
        $query = User::query();
        $query->join('model_has_roles', 'users.uuid', '=', 'model_has_roles.model_uuid')
            ->where('model_has_roles.role_id', '=', $role_uuid);
        if ($perPage) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }
        return $query->get();
    }
}
