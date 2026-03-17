<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IpdAdmission;
use App\Models\Invoice;
use App\Models\Bed;
use Carbon\Carbon;

class IpdDischargeController extends Controller
{
    public function create(IpdAdmission $ipd)
    {
        if ($ipd->status != 'admitted') {
            return redirect()->route('ipd.index')->with('error', 'Patient is not currently admitted.');
        }

        $ipd->load(['patient.user']);

        // Estimate current bill (Bed Days)
        $days = Carbon::parse($ipd->admission_date)->diffInDays(now()) ?: 1; // min 1 day
        
        // Let's assume some base rates for demonstration
        $rates = [
            'General' => 500,
            'Private' => 1500,
            'ICU' => 5000
        ];
        $dailyRate = $rates[$ipd->ward_type] ?? 1000;
        $bedCharges = $days * $dailyRate;

        // Count meds as approx estimate (Demo purposes)
        $medCount = DB::table('ipd_medications')->where('ipd_admission_id', $ipd->id)->count();
        $estMedCharges = $medCount * 50; 

        return view('ipd.discharge', compact('ipd', 'days', 'dailyRate', 'bedCharges', 'estMedCharges'));
    }

    public function store(Request $request, IpdAdmission $ipd)
    {
        $request->validate([
            'discharge_summary' => 'required|string',
            'bed_charges' => 'required|numeric|min:0',
            'medicine_charges' => 'required|numeric|min:0',
            'misc_charges' => 'required|numeric|min:0',
        ]);

        $totalBill = $request->bed_charges + $request->medicine_charges + $request->misc_charges;

        // Generate Invoice
        $invoice = Invoice::create([
            'patient_id' => $ipd->patient_id,
            'ipd_admission_id' => $ipd->id,
            'subtotal' => $totalBill,
            'tax' => $totalBill * 0.05, // 5% tax example
            'total_amount' => $totalBill * 1.05,
            'due_date' => now()->addDays(3),
            'status' => 'Pending'
        ]);

        // Update Admission
        $ipd->update([
            'status' => 'discharged',
            'discharge_date' => now(),
            'discharge_summary' => $request->discharge_summary,
            'total_bill' => $totalBill
        ]);

        // Free the Bed
        if ($ipd->bed_id) {
            Bed::where('id', $ipd->bed_id)->update(['is_occupied' => false]);
        }

        return redirect()->route('ipd.certificate', $ipd->id)
                         ->with('success', 'Patient successfully discharged. Invoice #' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT) . ' generated.');
    }

    public function printCertificate(IpdAdmission $ipd)
    {
        $ipd->load(['patient.user', 'doctor.user']);
        return view('ipd.certificate', compact('ipd'));
    }
}
