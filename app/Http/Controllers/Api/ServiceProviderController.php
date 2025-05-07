<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class ServiceProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ServiceProvider::query();

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $providers = $query->select(['id', 'name', 'slug', 'short_description', 'logo', 'category_id'])
            ->with('category:id,name,slug')
            ->paginate($request->input('per_page', 12));

        if ($providers->isEmpty()) {
            return response()->json([
                'message' => 'No service providers found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Service providers retrieved successfully.',
            'data' => $providers->items(),

            'meta' => [
                'current_page' => $providers->currentPage(),
                'from' => $providers->firstItem(),
                'last_page' => $providers->lastPage(),
                'links' => $providers->linkCollection()->toArray(),
                'path' => $providers->path(),
                'per_page' => $providers->perPage(),
                'to' => $providers->lastItem(),
                'total' => $providers->total(),
            ],
        ], 200);
    }


    public function show(ServiceProvider $provider)
    {
        $data = $provider->load('category');

        if (!$data) {
            return response()->json([
                'message' => 'No service provider found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Service provider retrieved successfully.',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
