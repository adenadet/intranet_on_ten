<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Models\ConsultantPractice\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function destroy(string $id)
    {
        $specialty = Specialty::find($id);

        if (!$specialty) {
            return response()->json(['message' => 'Specialty not found.'], 404);
        }

        $specialty->status == Specialty::StatusActive ? $specialty->update([
            'status' => Specialty::StatusInactive,
            'updated_by' => auth('api')->id() ?? auth()->id(),
            'deleted_by' => auth('api')->id() ?? auth()->id(),
            'deleted_at' => now(),
        ]) : $specialty->update([
            'status' => Specialty::StatusActive,
            'updated_by' => auth('api')->id() ?? auth()->id(),
            'deleted_by' => null,
            'deleted_at' => null,
        ]);

        return response()->json([
            'specialty' => $specialty
        ]);
    }

    public function index()
    {
        $specialties = Specialty::query()->orderBy('name', 'ASC')->paginate(20);
        return response()->json([
            'specialties' => $specialties
        ]);
    }

    public function show(string $id)
    {
        $specialty = Specialty::find($id);

        if (!$specialty) {
            return response()->json(['message' => 'Specialty not found.'], 404);
        }

        return response()->json([
            'specialty' => $specialty
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:0,1',
        ]);

        $specialty = Specialty::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? Specialty::StatusActive,
            'created_by' => auth('api')->id() ?? auth()->id(),
            'updated_by' => auth('api')->id() ?? auth()->id(),
        ]);
        return response()->json([
            'specialty' => $specialty
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:0,1',
        ]);

        $specialty = Specialty::find($id);

        if (!$specialty) {
            return response()->json(['message' => 'Specialty not found.'], 404);
        }

        $specialty->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? Specialty::StatusActive,
            'updated_by' => auth('api')->id() ?? auth()->id(),
        ]);

        return response()->json([
            'specialty' => $specialty
        ]);
    }
}
