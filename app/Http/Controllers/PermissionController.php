<?php

namespace App\Http\Controllers;

use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->middleware('permission:permission-index', ['only' => ['index', 'showById', 'showByName']]);
        $this->middleware('permission:permission-add', ['only' => ['store']]);
        $this->middleware('permission:permission-edit', ['only' => ['update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }

    /**
     * @OA\Get(
     *     path="/permissions",
     *     tags={"Permissions"},
     *     summary="Get all permissions with optional sorting, searching, and pagination",
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
     *                 description="Permission list",
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
        $permissions = $this->permissionRepository->getPermissions($request);
        return response()->json([
            'status' => true,
            'message' => 'Permission list',
            'data' => $permissions,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/permissions/{uuid}",
     *     tags={"Permissions"},
     *     summary="Get permission by uuid",
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
        $permission = $this->permissionRepository->getByUuid($uuid);
        if (!$permission) {
            return response()->json([
                'status' => false,
                'message' => 'Permission not found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Permission get by uuid',
            'data' => $permission,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/permissions/name/{name}",
     *     tags={"Permissions"},
     *     summary="Get permission by name",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="Permission name",
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
        $permission = $this->permissionRepository->getByName($name);
        if (!$permission) {
            return response()->json([
                'status' => false,
                'message' => 'Permission not found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Permission get by name',
            'data' => $permission,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/permissions",
     *     tags={"Permissions"},
     *     summary="Create permission",
     *     security={{"bearerAuth": {}}},
     *          @OA\Parameter(
     *          name="name",
     *          in="query",
     *          required=true,
     *          description="name of permission",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
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
            'name' => 'required|string|unique:permissions,name',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $permission = $this->permissionRepository->store($request);
        return response()->json([
            'status' => true,
            'message' => 'Permission created',
            'data' => $permission,
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/permissions/{uuid}",
     *     tags={"Permissions"},
     *     summary="Update permission",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *    @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="permission UUID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="name of permission",
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
     *         description="Permission not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid data provided"
     *     ),
     * )
     */


    public function update(Request $request, $uuid)
    {
        $permission = $this->permissionRepository->getByUuid($uuid);
        if (!$permission) {
            return response()->json([
                'status' => false,
                'message' => 'Permission not found',
            ], 404);
        }

        $rules = [
            'name' => 'required|string|unique:permissions,name',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $this->permissionRepository->update($request, $permission);
        return response()->json([
            'status' => true,
            'message' => 'Permission updated',
            'data' => $permission,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/permissions/{uuid}",
     *     tags={"Permissions"},
     *     summary="Delete Permission",
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
        $permission = $this->permissionRepository->getByUuid($uuid);
        if (!$permission) {
            return response()->json([
                'status' => false,
                'message' => 'Permission not found',
            ], 404);
        }

        $this->permissionRepository->destroy($permission);
        return response()->json([
            'status' => true,
            'message' => 'Permission deleted',
        ], 200);
    }


}
