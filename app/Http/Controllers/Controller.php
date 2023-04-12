<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'identity' => ['required'],
            'password' => 'required|string',
        ]);

        $user = DB::table('users')
            ->select('*')
            ->where('email', $request->post('identity'))
            ->orWhere('phone', $request->post('identity'))
            ->limit(1)
            ->get();

        if (!$user || !Hash::check($request->post('password'), $user->password)) {
            throw ValidationException::withMessages([
                'identity' => [__('auth.failed')],
            ]);
        }

        $data =  [
            'token_type' => 'Bearer',
            'token' => $user->createToken('api')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ]
        ];

        return response()->json($data);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'unique:users,phone'],
            'email' => 'required|email|unique:users,email',
            'full_name' => 'required|max:255',
            'password' => ['required', 'confirmed',],
            'password_confirmation' => 'required',
        ]);

        $user = new User;
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->full_name = $request->get('full_name');
        $user->password = Hash::make($request->get('password'));
        $user->saveOrFail();

        return response()->json(['user' => $user]);
    }

    public function list(ProductListRequest $request, ProductService $service): LengthAwarePaginator
    {
        return $service->getList($request->validated());
    }
}
