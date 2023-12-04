<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthenticatedSessionController extends Controller
{
        /**
     * @OA\Post(
     *      path="/api/login",
     *      summary="Efetua login e retorna um token de acesso",
     *      description="Loga um usuário no webservice.",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="email", type="string", description="Email do usuário", example="exemplo@exemplo.com"),
     *              @OA\Property(property="password", type="string", description="Senha do usuário", example="Abcd1234@")
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Sucesso",
     *          @OA\JsonContent(
     *                 @OA\Property(property="token", type="string", description="Token de acesso via API")
     *          )
     *      ),
    *      @OA\Response(
     *          response="422",
     *          description="Conteúdo não processável",
     *          @OA\JsonContent(
     *                 @OA\Property(property="message", type="string", description="These credentials do not match our records.")
     *          )
     *      )
     * )
     **/
    public function auth(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $token = $request->user()->createToken($request->email);
        return response()->json(['token' => $token->plainTextToken]);
    }
}
