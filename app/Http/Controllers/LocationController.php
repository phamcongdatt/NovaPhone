<?php

namespace App\Http\Controllers;

use App\Models\AdministrativeProvince;
use App\Models\AdministrativeWard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function provinces(): JsonResponse
    {
        $provinces = AdministrativeProvince::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['code', 'name', 'type']);

        return response()->json(['data' => $provinces]);
    }

    public function wards(Request $request, string $provinceCode): JsonResponse
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        abort_unless(
            AdministrativeProvince::query()
                ->where('code', $provinceCode)
                ->where('is_active', true)
                ->exists(),
            404
        );

        $query = AdministrativeWard::query()
            ->where('province_code', $provinceCode)
            ->where('is_active', true);

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        return response()->json([
            'data' => $query
                ->orderBy('name')
                ->get(['code', 'name', 'type']),
        ]);
    }
}
