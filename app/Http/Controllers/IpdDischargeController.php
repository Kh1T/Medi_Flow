<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IpdAdmission;
use App\Models\Billing;
use App\Models\Bed;
use App\Services\BillingService;
use Carbon\Carbon;

class IpdDischargeController extends Controller
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function create(IpdAdmission $ipd)
    {
        if ($ipd->status != 'admitted') {
            return redirect()->route('ipd.index')->with('error', 'Patient is not currently admitted.');
        }

        $ipd->load(['patient.user', 'bed']);

        // Calculate bed charges using the billing service
        $bedSummary = $this->billingService->calculateIpdBedSummary($ipd);
        $days = $bedSummary['bed_days'];
        $dailyRate = $bedSummary['price_per_day'];
        $bedCharges = $bedSummary['bed_charges'];

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
            'discount' => 'nullable|numeric|min:0',
            'apply_insurance' => 'nullable|boolean',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        // Prepare billing data for the service
        $billingData = [
            'bed_charges' => floatval($request->bed_charges),
            'medicine_charges' => floatval($request->medicine_charges),
            'misc_charges' => floatval($request->misc_charges),
            'discount' => floatval($request->discount ?? 0),
            'apply_insurance' => $request->boolean('apply_insurance'),
            'paid_amount' => floatval($request->paid_amount ?? 0),
        ];

        // Generate Invoice using BillingService
        $invoice = $this->billingService->generateIpdInvoice($ipd, $billingData);

        // Update Admission
        $ipd->update([
            'status' => 'discharged',
            'discharge_date' => now(),
            'discharge_summary' => $request->discharge_summary,
            'total_bill' => $invoice->total
        ]);

        // Free the Bed
        if ($ipd->bed_id) {
            Bed::where('id', $ipd->bed_id)->update(['is_occupied' => false]);
        }

        return redirect()->route('ipd.certificate', $ipd->id)
                         ->with('success', 'Patient successfully discharged. Invoice #' . $invoice->invoice_number . ' generated.');
    }

    public function printCertificate(IpdAdmission $ipd)
    {
        $ipd->load(['patient.user', 'doctor.user']);
        return view('ipd.certificate', compact('ipd'));
    }
}
