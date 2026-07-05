<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Ejemplo de endpoint CRUD (solo lectura) para servir de plantilla a módulos de negocio futuros:
 * paginación estándar, autorización vía Policy, y un Resource dedicado.
 */
class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $perPage = min((int) $request->integer('per_page', 15), 100);

        $users = User::query()
            ->with('roles')
            ->latest()
            ->paginate($perPage);

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        $user->load('roles');

        return new UserResource($user);
    }
}
