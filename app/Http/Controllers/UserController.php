<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userRepository, $roleRepository;
    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->middleware('permission:user-index', ['only' => ['index', 'show', 'getUserRoles', 'getUserByRole']]);
        $this->middleware('permission:user-add', ['only' => ['store']]);
        $this->middleware('permission:user-edit', ['only' => ['update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"User"},
     *     summary="Get all users with optional sorting, searching, and pagination",
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
     *             enum={"uuid", "name"},
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
     *    @OA\Parameter(
     *         name="roles_uuid",
     *         in="query",
     *         description="Filter by role",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
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
     *                 description="User list",
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
        $limit = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'uuid');
        $sortDirection = $request->input('sortDirection', 'asc');
        $roles_uuid = $request->input('roles_uuid');
        $users = $this->userRepository->getUsers($limit, $page, $search, $sortBy, $sortDirection, $roles_uuid);

        return response()->json([
            'status' => true,
            'message' => 'User list',
            'data' => $users,
        ], 200);
    }
    /**
     * @OA\Get(
     *     path="/users/{uuid}",
     *     tags={"User"},
     *     summary="Get user by index",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="User UUID",
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
    public function show($uuid)
    {
        $user = User::findOrFail($uuid);
        return response()->json($user);
    }
    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"User"},
     *     summary="Store New User",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="User's name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         description="User's username",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         description="User's email",
     *         @OA\Schema(type="string", format="email")
     *     ),
     *     @OA\Parameter(
     *         name="no_hp",
     *         in="query",
     *         required=true,
     *         description="User's phone number",
     *         @OA\Schema(type="string", format="numeric")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         description="User's password",
     *         @OA\Schema(type="string", minLength=8)
     *     ),
     *     @OA\Parameter(
     *         name="uuid_roles",
     *         in="query",
     *         required=true,
     *         description="User role",
     *         @OA\Schema(
     *             type="string", format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User Created Successfully"
     *     ),
     *     @OA\Response(
     *       response="400",
     *       description="Bad request - Invalid data provided"
     *     ),
     *     @OA\Response(
     *       response="409",
     *       description="Conflict - Email already exists"
     *     ),
     *     @OA\Response(
     *       response="500",
     *       description="Internal Server Error"
     *     )
     * )
     */

    public function store(Request $request)
    {

        $rules = [
            'name' => 'required|string',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'no_hp' => 'required|numeric',
            'password' => 'required|string|min:8',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $user = $this->userRepository->store($request);
        return response()->json($user, 201);
    }

    /**
     * @OA\Post(
     *     path="/users/{uuid}",
     *     tags={"User"},
     *     summary="Update user",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="User UUID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="User's name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         description="User's username",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         description="User's email",
     *         @OA\Schema(type="string", format="email")
     *     ),
     *     @OA\Parameter(
     *         name="no_hp",
     *         in="query",
     *         required=true,
     *         description="User's phone number",
     *         @OA\Schema(type="string", format="numeric")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         description="User's password (min 8 characters)",
     *         @OA\Schema(type="string", minLength=8)
     *     ),
     *     @OA\Parameter(
     *         name="uuid_roles",
     *         in="query",
     *         required=false,
     *         description="User role",
     *         @OA\Schema(
     *             type="string", format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid data provided"
     *     ),
     * )
     */
    public function update(Request $request, $uuid)
    {
        $user = $this->userRepository->getByUuid($uuid);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $rules = [
            'name'      => 'required|string',
            'username'  => 'required|string|unique:users,username,' . $user->uuid . ',uuid',
            'email'     => 'required|email|unique:users,email,' . $user->uuid . ',uuid',
            'no_hp'     => 'required|numeric',
            'password'  => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $this->userRepository->update($request, $user);
        return response()->json([
            'status' => true,
            'message' => 'User updated',
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/users/{uuid}",
     *     tags={"User"},
     *     summary="Delete user",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="User UUID",
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
     *         description="User not found"
     *     ),
     * )
     */
    public function destroy($uuid)
    {
        $user = $this->userRepository->getByUuid($uuid);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $this->userRepository->destroy($user);

        return response()->json([
            'status' => true,
            'message' => 'User deleted',
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/users/profile/get",
     *     tags={"User"},
     *     summary="Get authenticated user profile",
     *     security={
     *         {"bearerAuth": {}}
     *     },
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
     *                 description="User profile data",
     *                 @OA\Property(
     *                     property="uuid",
     *                     type="string",
     *                     description="User UUID"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="User name"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     type="string",
     *                     description="User username"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email"
     *                 ),
     *                 @OA\Property(
     *                     property="no_hp",
     *                     type="string",
     *                     description="User phone number"
     *                 ),
     *                 @OA\Property(
     *                     property="roles",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="uuid",
     *                             type="string",
     *                             description="Role UUID"
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string",
     *                             description="Role name"
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $profile = $this->userRepository->getProfile($user->uuid);

        return response()->json([
            'status' => true,
            'message' => 'User profile retrieved successfully',
            'data' => $profile,
        ], 200);
    }
}
