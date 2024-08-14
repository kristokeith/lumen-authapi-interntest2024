<?php

namespace App\Http\Controllers;

use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->middleware('permission:role-index', ['only' => ['index', 'showById', 'showByName', 'getRolePermissions']]);
        $this->middleware('permission:role-add', ['only' => ['store']]);
        $this->middleware('permission:role-edit', ['only' => ['update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);

    }

    /**
     * @OA\Get(
     *     path="/roles",
     *     tags={"Roles"},
     *     summary="Get all roles with optional sorting, searching, and pagination",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword for UUID or name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Field to sort by (uuid or name)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"uuid", "name", "guard_name", "created_at", "updated_at"},
     *             default="uuid"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortDirection",
     *         in="query",
     *         description="Sort direction (asc or desc)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             default="asc"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the request"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Response message"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Role list",
     *                 @OA\Property(
     *                     property="current_page",
     *                     type="integer",
     *                     description="Current page number"
     *                 ),
     *                 @OA\Property(
     *                     property="first_page_url",
     *                     type="string",
     *                     description="URL of the first page"
     *                 ),
     *                 @OA\Property(
     *                     property="from",
     *                     type="integer",
     *                     description="Index of the first item in the current page"
     *                 ),
     *                 @OA\Property(
     *                     property="last_page",
     *                     type="integer",
     *                     description="Last page number"
     *                 ),
     *                 @OA\Property(
     *                     property="last_page_url",
     *                     type="string",
     *                     description="URL of the last page"
     *                 ),
     *                 @OA\Property(
     *                     property="next_page_url",
     *                     type="string",
     *                     description="URL of the next page"
     *                 ),
     *                 @OA\Property(
     *                     property="path",
     *                     type="string",
     *                     description="URL path of the current request"
     *                 ),
     *                 @OA\Property(
     *                     property="per_page",
     *                     type="integer",
     *                     description="Number of items per page"
     *                 ),
     *                 @OA\Property(
     *                     property="prev_page_url",
     *                     type="string",
     *                     description="URL of the previous page"
     *                 ),
     *                 @OA\Property(
     *                     property="to",
     *                     type="integer",
     *                     description="Index of the last item in the current page"
     *                 ),
     *                 @OA\Property(
     *                     property="total",
     *                     type="integer",
     *                     description="Total number of items"
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        $roles = $this->roleRepository->getRoles($request);
        return response()->json([
            'status' => true,
            'message' => 'Role list',
            'data' => $roles,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/roles/{uuid}",
     *     tags={"Roles"},
     *     summary="Get roles by uuid",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Permission UUID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */

    public function showById($uuid)
    {
        $role = $this->roleRepository->getByUuid($uuid);
        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Role get by uuid',
            'data' => $role,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/roles/name/{name}",
     *     tags={"Roles"},
     *     summary="Get role by name",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="Role name",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */

    public function showByName($name)
    {
        $role = $this->roleRepository->getByName($name);
        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Role get by name',
            'data' => $role,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/roles",
     *     tags={"Roles"},
     *     summary="Create role",
     *     security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=true,
     *      description="Name of the role",
     *      @OA\Schema(
     *          type="string"
     *      )
     *     ),
     *               @OA\Parameter(
     *           name="uuid_permissions[]",
     *           in="query",
     *           required=false,
     *           description="Array of uuid permissions",
     *           @OA\Schema(
     *               type="array",
     *               @OA\Items(type="string")
     *           )
     *       ),
     *     @OA\Response(
     *         response=201,
     *         description="Permission created"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|unique:roles,name',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $role = $this->roleRepository->store($request);

        return response()->json([
            'status' => true,
            'message' => 'Role created',
            'data' => $role,
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/roles/{uuid}",
     *     tags={"Roles"},
     *     summary="Update role",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *    @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Role UUID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=true,
     *      description="Name of the role",
     *      @OA\Schema(
     *          type="string"
     *      )
     *  ),
     *          @OA\Parameter(
     *          name="uuid_permissions[]",
     *          in="query",
     *          required=false,
     *          description="Array of uuid permissions",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(type="string")
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid data provided"
     *     ),
     * )
     */

    public function update(Request $request, $uuid)
    {
        $role = $this->roleRepository->getByUuid($uuid);

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found',
            ], 404);
        }

        $rules = [
            'name' => 'required|string|unique:roles,name,' . $role->uuid. ',uuid',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $this->roleRepository->update($request, $role);

        return response()->json([
            'status' => true,
            'message' => 'Role updated',
            'data' => $role,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/roles/{uuid}",
     *     tags={"Roles"},
     *     summary="Delete Role",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Role UUID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found"
     *     ),
     * )
     */

    public function destroy($uuid)
    {
        $role = $this->roleRepository->getByUuid($uuid);

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found',
            ], 404);
        }

        $this->roleRepository->destroy($role);

        return response()->json([
            'status' => true,
            'message' => 'Role deleted',
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/roles/{uuid}/permissions",
     *     tags={"Roles"},
     *     summary="Get role permissions",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Role UUID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */

    public function getRolePermissions($uuid)
    {
        $role = $this->roleRepository->getByUuid($uuid);
        return response()->json([
            'status' => true,
            'message' => 'Role permissions',
            'data' => $this->roleRepository->getRolePermissions($role),
        ], 200);
    }
}
