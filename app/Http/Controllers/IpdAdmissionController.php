<?php

namespace App\Http\Controllers;

use App\Models\IpdAdmission;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Bed;
use App\Models\OpdVisit;
use Illuminate\Http\Request;

class IpdAdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admissions = IpdAdmission::with(['patient.user', 'doctor.user', 'bed'])->latest()->paginate(15);
        return view('ipd.index', compact('admissions'));
    }

    public function create(Request $request)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->where('is_available', true)->get();
        
        // If transitioning from OPD, pre-fill some datan
        $opdVisit = null;
        if ($request->has('opd_visit_id')) {
            $opdVisit = OpdVisit::find($request->opd_visit_id);
        }

        return view('ipd.create', compact('patients', 'doctors', 'opdVisit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'admission_type' => 'required|in:Emergency,Planned',
            'ward_type' => 'required|in:General,Private,ICU',
            'doctor_id' => 'required|exists:doctors,id',
            'admission_date' => 'required|date',
            'admission_reason' => 'required|string',
            'symptoms' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['status'] = 'admitted';

        // Bed Auto-Assignment Logic based on Ward Selection
        $availableBed = Bed::where('ward_type', $request->ward_type)
                            ->where('is_occupied', false)
                            ->first();

        if (!$availableBed) {
            return back()->with('error', 'No available beds in the selected '. $request->ward_type .' ward. Please free up a bed or choose a different ward.')->withInput();
        }

        // Assign bed and update map
        $data['bed_id'] = $availableBed->id;
        $data['bed_number'] = $availableBed->bed_number;
        
        $admission = IpdAdmission::create($data);
        
        // Mark bed as occupied
        $availableBed->update(['is_occupied' => true]);

        return redirect()->route('ipd.index')->with('success', 'Patient Admitted Successfully to Bed: ' . $availableBed->bed_number);
    }

    public function show(IpdAdmission $ipd)
    {
        $ipd->load(['patient.user', 'doctor.user', 'bed']);
        
        // Placeholder for Notes, Medications, and Lab Requests relations coming soon
        // $ipd->load(['notes.author', 'medications.administrator', 'labRequests']);
        
        return view('ipd.show', compact('ipd'));
    }

    public function edit(IpdAdmission $ipd)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->where('is_available', true)->get();
        return view('ipd.edit', compact('ipd', 'patients', 'doctors'));
    }

    public function update(Request $request, IpdAdmission $ipd)
    {
        // Allow updating ward type, but handling bed change is complex. Let's restrict it to just medical data for now to avoid moving errors.
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'admission_reason' => 'required|string',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
        ]);

        $ipd->update($request->only(['doctor_id', 'admission_reason', 'symptoms', 'diagnosis']));

        return redirect()->route('ipd.show', $ipd->id)->with('success', 'Admission details updated.');
    }

    public function destroy(IpdAdmission $ipd)
    {
        // Free the bed if destroying active admission
        if ($ipd->status == 'admitted' && $ipd->bed_id) {
            Bed::where('id', $ipd->bed_id)->update(['is_occupied' => false]);
        }
        
        $ipd->delete();
        return redirect()->route('ipd.index')->with('success', 'Admission record deleted.');
    }
}
