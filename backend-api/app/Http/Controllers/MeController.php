<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): JsonResponse
    {
        $payload = $request->attributes->get('jwt_payload');

        return response()->json([
            'sub'                => $payload->sub ?? null,
            'email'              => $payload->email ?? null,
            'preferred_username' => $payload->preferred_username ?? null,
            'name'               => $payload->name ?? null,
            'roles'              => $payload->realm_access->roles ?? [],
            'claims'             => (array) $payload,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
