<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Service::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $service = Service::create([
            'user_id' => $request->user()->id,
            'foto_persona' => $request->foto_persona
        ]);

        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Service::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);
        
        // Para simplificar, en update permitimos string simple, o podrías reusar el Request
        $request->validate(['foto_persona' => 'string']);

        $service->update($request->only('foto_persona'));

        return response()->json($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return response()->json(['message' => 'Servicio eliminado']);
    }
}
