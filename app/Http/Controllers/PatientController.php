<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Patient::query();
        // Search by name, email, phone
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }
        // Filter by gender
        if (request('gender')) {
            $query->where('gender', request('gender'));
        }
        // Filter by blood group
        if (request('blood_group')) {
            $query->where('blood_group', request('blood_group'));
        }
        $patients = $query->with('user')->paginate(10);
        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email:filter|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:10',
            'address' => 'required|string',
            'emergency_contact' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
        ]);

        $phone = '+855 ' . $request->phone;
        $emergencyPhone = $request->emergency_phone ? '+855 ' . $request->emergency_phone : null;

        DB::transaction(function () use ($request, $phone, $emergencyPhone) {
            // Create user first
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $phone,
                'password' => Hash::make($phone ?? 'password123'), // Default password
                'role' => 'patient',
            ]);

            // Create patient profile
            Patient::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'blood_group' => $request->blood_group,
                'phone' => $phone,
                'email' => $request->email,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $emergencyPhone,
            ]);
        });

        return redirect()->route('patients.index')->with('success', 'Patient registered successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $patient = Patient::with(['user', 'appointments.doctor.user'])->findOrFail($id);
        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $patient = Patient::with('user')->findOrFail($id);
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $patient = Patient::findOrFail($id);
        $user = $patient->user;

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email:filter|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:10',
            'address' => 'required|string',
            'emergency_contact' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $user, $patient) {
            $user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            $patient->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'blood_group' => $request->blood_group,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
            ]);
        });

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::findOrFail($id);
        $user = $patient->user;
        
        DB::transaction(function () use ($user, $patient) {
            $patient->delete();
            $user->delete();
        });

        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully');
    }
}
