<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{

    /**
     * @OA\Post(
     *      path="/api/user",
     *      summary="Cria um novo usuário e retorna seus dados como o token de acesso",
     *      description="Insere um novo usuário na base de dados e retorna um token de acesso.",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", description="Nome completo do usuário", example="João da Silva"),
     *              @OA\Property(property="email", type="string", description="Email do usuário", example="exemplo@exemplo.com"),
     *              @OA\Property(property="password", type="string", description="Senha do usuário", example="Abcd1234@"),
     *              @OA\Property(property="password_confirmation", type="string", description="Confirmação da senha do usuário", example="Abcd1234@")
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Sucesso",
     *          @OA\JsonContent(
     *                 @OA\Property(property="token", type="string", description="Token de acesso do usuário"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="name", type="string", description="Nome do usuário"),
     *                     @OA\Property(property="email", type="string", description="Email do usuário"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Data e hora da última atualização"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Data e hora da criação"),
     *                     @OA\Property(property="id", type="integer", description="ID do usuário")
     *                 )
     *          )
     *      )
     * )
     **/
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    }
}
