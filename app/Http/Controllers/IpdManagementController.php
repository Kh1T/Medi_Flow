<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IpdAdmission;

class IpdManagementController extends Controller
{
    public function storeNote(Request $request, IpdAdmission $ipd)
    {
        $request->validate([
            'note_type' => 'required|in:nurse_chart,doctor_visit',
            'notes' => 'required|string',
        ]);

        DB::table('ipd_notes')->insert([
            'ipd_admission_id' => $ipd->id,
            'author_id' => auth()->id(),
            'note_type' => $request->note_type,
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Note added successfully.');
    }

    public function storeMedication(Request $request, IpdAdmission $ipd)
    {
        $request->validate([
            'medicine_name' => 'required|string',
            'dosage' => 'required|string',
            'administered_at' => 'required|date',
        ]);

        DB::table('ipd_medications')->insert([
            'ipd_admission_id' => $ipd->id,
            'medicine_name' => $request->medicine_name,
            'dosage' => $request->dosage,
            'administered_at' => $request->administered_at,
            'administered_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Medication logged successfully.');
    }

    public function storeLabRequest(Request $request, IpdAdmission $ipd)
    {
        $request->validate([
            'test_name' => 'required|string',
        ]);

        DB::table('ipd_lab_requests')->insert([
            'ipd_admission_id' => $ipd->id,
            'test_name' => $request->test_name,
            'status' => 'pending',
            'requested_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Lab test requested.');
    }
}
