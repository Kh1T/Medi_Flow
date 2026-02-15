<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = MedicalRecord::with(['patient.user', 'doctor.user'])->latest()->paginate(10);
        return view('emr.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('emr.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'record_date' => 'required|date',
        ]);

        MedicalRecord::create($request->all());

        return redirect()->route('emr.index')->with('success', 'Medical record created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = MedicalRecord::with(['patient.user', 'doctor.user', 'prescriptions'])->findOrFail($id);
        return view('emr.show', compact('record'));
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
