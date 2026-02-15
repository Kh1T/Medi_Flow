<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::with('user')->paginate(10);
        return view('doctors.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:filter|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|unique:doctors',
            'experience_years' => 'required|integer|min:0',
            'consultation_fee' => 'required|numeric|min:0',
            'qualification' => 'nullable|string|max:255',
        ]);

        $phone = $request->phone ? '+855 ' . $request->phone : null;

        DB::transaction(function () use ($request, $phone) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $phone,
                'password' => Hash::make($phone ?? 'doctor123'),
                'role' => 'doctor',
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'specialization' => $request->specialization,
                'qualification' => $request->qualification,
                'license_number' => $request->license_number,
                'experience_years' => $request->experience_years,
                'consultation_fee' => $request->consultation_fee,
                'is_available' => true,
            ]);
        });

        return redirect()->route('doctors.index')->with('success', 'Doctor registered successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = Doctor::with(['user', 'appointments.patient.user', 'availabilities'])->findOrFail($id);
        return view('doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        return view('doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $doctor = Doctor::findOrFail($id);
        $user = $doctor->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:filter|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|unique:doctors,license_number,' . $doctor->id,
            'experience_years' => 'required|integer|min:0',
            'consultation_fee' => 'required|numeric|min:0',
            'qualification' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $user, $doctor) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            $doctor->update([
                'specialization' => $request->specialization,
                'qualification' => $request->qualification,
                'license_number' => $request->license_number,
                'experience_years' => $request->experience_years,
                'consultation_fee' => $request->consultation_fee,
            ]);
        });

        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $doctor = Doctor::findOrFail($id);
        $user = $doctor->user;

        DB::transaction(function () use ($user, $doctor) {
            $doctor->delete();
            $user->delete();
        });

        return redirect()->route('doctors.index')->with('success', 'Doctor deleted successfully');
    }
}
