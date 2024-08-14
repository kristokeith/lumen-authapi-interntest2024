<?php

namespace App\Http\Controllers;

use App\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Controller constructor.
     *
     * @param  \App\Auth  $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @OA\Post(
     *   path="/auth/login",
     *   tags={"Authentication"},
     *   summary="Login",
     *   security={
     *      {"bearer": {}}
     *   },
     *   summary="Login",
     *
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *               @OA\Property(
     *                   property="email",
     *                   type="string",
     *                   format="email"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   type="string",
     *                   format="password"
     *               ),
     *               required={"email", "password"}
     *           )
     *       )
     *   ),
     *
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               @OA\Property(
     *                   property="status",
     *                   type="boolean"
     *               ),
     *               @OA\Property(
     *                   property="message",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="data",
     *                   @OA\Property(
     *                       property="token",
     *                       type="string"
     *                   ),
     *                   @OA\Property(
     *                       property="user",
     *                       @OA\Property(
     *                           property="id",
     *                           type="integer"
     *                       ),
     *                       @OA\Property(
     *                           property="role_id",
     *                           type="integer"
     *                       ),
     *                       @OA\Property(
     *                           property="name",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="birthday",
     *                           type="string",
     *                           format="date-time"
     *                       ),
     *                       @OA\Property(
     *                           property="email",
     *                           type="string",
     *                           format="email"
     *                       ),
     *                       @OA\Property(
     *                           property="username",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="phone",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="province_id",
     *                           type="integer"
     *                       ),
     *                       @OA\Property(
     *                           property="city_id",
     *                           type="integer"
     *                       ),
     *                       @OA\Property(
     *                           property="district_id",
     *                           type="integer"
     *                       ),
     *                       @OA\Property(
     *                           property="village_id",
     *                           type="integer"
     *                       ),
     *                       @OA\Property(
     *                           property="address",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="ktp",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="ktp_number",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="npwp",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="npwp_number",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="two_factor_secret",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="two_factor_recovery_codes",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="avatar",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="email_verified_at",
     *                           type="string",
     *                           format="date-time"
     *                       ),
     *                       @OA\Property(
     *                           property="api_token",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="status",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="log_kalkulator",
     *                           type="string"
     *                       ),
     *                       @OA\Property(
     *                           property="created_at",
     *                           type="string",
     *                           format="date-time"
     *                       ),
     *                       @OA\Property(
     *                           property="updated_at",
     *                           type="string",
     *                           format="date-time"
     *                       ),
     *                       @OA\Property(
     *                           property="deleted_at",
     *                           type="string",
     *                           format="date-time"
     *                       )
     *                   )
     *               )
     *           )
     *       )
     *   ),
     *
     *   @OA\Response(
     *       response=400,
     *       description="Bad Request",
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               @OA\Property(
     *                   property="status",
     *                   type="boolean"
     *               ),
     *               @OA\Property(
     *                   property="message",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="data",
     *                   type="null"
     *               )
     *           )
     *       )
     *   ),
     *
     *   @OA\Response(
     *       response=500,
     *       description="Internal Error",
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               @OA\Property(
     *                   property="status",
     *                   type="boolean"
     *               ),
     *               @OA\Property(
     *                   property="message",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="data",
     *                   @OA\Property(
     *                       property="messages",
     *                       type="object"
     *                   )
     *               )
     *           )
     *       )
     *   )
     * )
     **/

    public function store(Request $request): JsonResponse
    {
        $token = $this->auth->authenticateByEmailAndPassword(
            (string) $request->input('email'),
            (string) $request->input('password')
        );

        $user = $this->auth->getAuthenticatedUser();

        return response()->json([
            'status' => true,
            'message' => 'Login Sukses',
            'data' => $token,
            'user' => $user,
        ], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/auth/data",
     *     tags={"Authentication"},
     *     summary="Get the authenticated User",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="User ID"),
     *             @OA\Property(property="name", type="string", description="User name"),
     *             @OA\Property(property="email", type="string", format="email", description="User email"),
     *             @OA\Property(property="created_at", type="string", format="date-time", description="User creation date/time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="User update date/time"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", description="Unauthorized"),
     *             @OA\Property(property="message", type="string", description="Invalid or missing authentication token"),
     *         )
     *     )
     * )
     */
    public function show(): JsonResponse
    {
        $user = $this->auth->getAuthenticatedUser();

        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Put(
     *   path="/auth/refresh-token",
     *   tags={"Authentication"},
     *   summary="Refresh a token",
     *   security={{ "bearerAuth": {} }},
     *   @OA\Response(
     *       response=200,
     *       description="Successful token refresh",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="access_token", type="string", description="Refreshed JWT token"),
     *           @OA\Property(property="token_type", type="string", description="Bearer"),
     *           @OA\Property(property="expires_in", type="integer", description="Token expiration time in seconds"),
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthorized",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="error", type="string", description="Unauthorized"),
     *           @OA\Property(property="message", type="string", description="Invalid or missing authentication token"),
     *       )
     *   )
     * )
     */
    public function update(): JsonResponse
    {
        $token = $this->auth->refreshAuthenticationToken();

        return response()->json($token, Response::HTTP_OK);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *     path="/auth/invalid-token",
     *     tags={"Authentication"},
     *     summary="Log the user out (Invalidate the token)",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=204,
     *         description="Token invalidated successfully",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", description="Unauthorized"),
     *             @OA\Property(property="message", type="string", description="Invalid or missing authentication token"),
     *         )
     *     )
     * )
     */
    public function destroy(): JsonResponse
    {
        $this->auth->invalidateAuthenticationToken();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
