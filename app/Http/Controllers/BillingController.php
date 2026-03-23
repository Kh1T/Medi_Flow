<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\OpdVisit;
use App\Models\IpdAdmission;
use App\Services\BillingService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bills = Billing::with(['patient.user', 'appointment'])->latest()->paginate(10);
        return view('billing.index', compact('bills'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::with('user')->get();
        $appointments = Appointment::whereDoesntHave('billing')->get();
        return view('billing.create', compact('patients', 'appointments'));
    }

    /**
     * Search patients by name (AJAX)
     */
    public function searchPatients(Request $request)
    {
        $search = $request->get('q', '');
        
        $patients = Patient::with('user')
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'text' => $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->phone . ')',
                    'name' => $patient->first_name . ' ' . $patient->last_name,
                    'phone' => $patient->phone,
                ];
            });
        
        return response()->json($patients);
    }

    /**
     * Get patient visits/admissions for billing (AJAX)
     */
    public function getPatientBillingData(Request $request)
    {
        $patientId = $request->get('patient_id');
        $type = $request->get('type', 'opd'); // 'opd' or 'ipd'
        
        $patient = Patient::with('user', 'insurances')->findOrFail($patientId);
        
        if ($type === 'opd') {
            // Get unpaid OPD visits
            $visits = OpdVisit::with('doctor.user')
                ->where('patient_id', $patientId)
                ->whereIn('payment_status', ['Unpaid', 'Pending'])
                ->where('status', '!=', 'cancelled')
                ->get()
                ->map(function ($visit) {
                    return [
                        'id' => $visit->id,
                        'date' => $visit->visit_date->format('Y-m-d'),
                        'doctor' => $visit->doctor->user->name ?? 'N/A',
                        'token' => $visit->token_number,
                        'fee' => $visit->fee,
                    ];
                });
            
            return response()->json([
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->first_name . ' ' . $patient->last_name,
                    'phone' => $patient->phone,
                ],
                'visits' => $visits,
                'insurance' => $patient->insurances()
                    ->where('valid_until', '>=', now()->toDateString())
                    ->first(),
            ]);
        } else {
            // Get active IPD admissions
            $admissions = IpdAdmission::with('bed')
                ->where('patient_id', $patientId)
                ->where('status', 'admitted')
                ->get()
                ->map(function ($admission) {
                    return [
                        'id' => $admission->id,
                        'admission_date' => $admission->admission_date->format('Y-m-d'),
                        'bed' => $admission->bed->bed_number ?? 'N/A',
                        'ward' => $admission->bed->ward->name ?? 'N/A',
                    ];
                });
            
            return response()->json([
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->first_name . ' ' . $patient->last_name,
                    'phone' => $patient->phone,
                ],
                'admissions' => $admissions,
                'insurance' => $patient->insurances()
                    ->where('valid_until', '>=', now()->toDateString())
                    ->first(),
            ]);
        }
    }

    /**
     * Show billing form with patient type selection (OPD/IPD)
     */
    public function createWithPatientType()
    {
        return view('billing.create_by_type');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'charges' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'apply_insurance' => 'nullable|boolean',
            'due_date' => 'nullable|date',
            'consultation_fee' => 'nullable|numeric|min:0',
            'lab_charges' => 'nullable|numeric|min:0',
            'medicine_charges' => 'nullable|numeric|min:0',
            'procedure_charges' => 'nullable|numeric|min:0',
            'prescription_charges' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['invoice_number'] = 'INV-' . strtoupper(str()->random(8));
        
        // Calculate totals
        $charges = floatval($request->charges ?? 0);
        $discount = floatval($request->discount ?? 0);
        $applyInsurance = $request->boolean('apply_insurance');
        
        $data['contractual_adjustments'] = $discount;
        $data['subtotal'] = $charges - $discount;
        
        // Calculate insurance coverage if applied
        $insuranceCoverage = 0;
        $insuranceCompany = null;
        $insuranceClaim = false;
        
        if ($applyInsurance) {
            $patient = Patient::find($request->patient_id);
            $insurance = $patient->insurances()
                ->where('valid_until', '>=', now()->toDateString())
                ->first();
            
            if ($insurance) {
                $insuranceCoverage = ($data['subtotal'] * $insurance->coverage_percentage) / 100;
                $insuranceCompany = $insurance->provider_name;
                $insuranceClaim = true;
            }
        }
        
        $data['insurance_coverage'] = $insuranceCoverage;
        $data['insurance_company'] = $insuranceCompany;
        $data['insurance_claim'] = $insuranceClaim;
        
        // Patient amount = subtotal - insurance coverage
        $patientAmount = max(0, $data['subtotal'] - $insuranceCoverage);
        $data['patient_amount'] = $patientAmount;
        $data['total'] = $patientAmount;
        
        // Set status to pending by default (payment is done separately via "Mark as Paid")
        $data['status'] = 'pending';
        $data['paid_amount'] = 0;

        // If OPD visit linked, update payment status
        if ($request->opd_visit_id) {
            $opdVisit = OpdVisit::find($request->opd_visit_id);
            if ($opdVisit) {
                $opdVisit->update(['payment_status' => 'Pending']);
            }
        }

        Billing::create($data);

        return redirect()->route('billing.index')->with('success', 'Invoice generated successfully');
    }

    /**
     * Display the specified invoice.
     */
    public function show(string $id)
    {
        $bill = Billing::with(['patient.user', 'appointment.doctor.user', 'opdVisit.doctor.user'])->findOrFail($id);
        return view('billing.show', compact('bill'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bill = Billing::findOrFail($id);
        $bill->update($request->only(['status', 'paid_amount']));

        return redirect()->route('billing.index')->with('success', 'Payment updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bill = Billing::findOrFail($id);
        $bill->delete();
        return redirect()->route('billing.index')->with('success', 'Invoice deleted');
    }

    /**
     * Show OPD billing form for a specific visit
     */
    public function createOpdBilling(string $visitId)
    {
        $visit = OpdVisit::with(['patient.user', 'doctor.user'])->findOrFail($visitId);
        
        // Check if invoice already exists
        $existingInvoice = Billing::where('opd_visit_id', $visitId)->first();
        
        // Get patient's active insurance
        $patient = $visit->patient;
        $insurance = $patient->insurances()
            ->where('valid_until', '>=', now()->toDateString())
            ->first();
        
        return view('billing.opd_create', compact('visit', 'existingInvoice', 'insurance'));
    }

    /**
     * Calculate billing summary (for AJAX)
     */
    public function calculateSummary(Request $request)
    {
        $billingData = $request->all();
        $summary = $this->billingService->calculateBillingSummary($billingData);
        
        return response()->json($summary);
    }

    /**
     * Store OPD billing invoice
     */
    public function storeOpdBilling(Request $request, string $visitId)
    {
        $visit = OpdVisit::findOrFail($visitId);

        $request->validate([
            'consultation_fee' => 'required|numeric|min:0',
            'lab_charges' => 'nullable|numeric|min:0',
            'medicine_cost' => 'nullable|numeric|min:0',
            'procedure_charges' => 'nullable|numeric|min:0',
            'prescription_charges' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'apply_insurance' => 'nullable|boolean',
        ]);

        $billingData = $request->all();
        $invoice = $this->billingService->generateOpdInvoice($visit, $billingData);

        return redirect()->route('billing.show', $invoice->id)
            ->with('success', 'OPD Invoice generated successfully');
    }

    /**
     * Process payment for an invoice
     */
    public function processPayment(Request $request, string $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,insurance',
        ]);

        $invoice = Billing::findOrFail($id);
        $invoice = $this->billingService->processPayment(
            $invoice,
            $request->amount,
            $request->method
        );

        return redirect()->route('billing.show', $id)
            ->with('success', 'Payment processed successfully. Paid: $' . number_format($invoice->paid_amount, 2));
    }

    /**
     * Show payment form for an invoice
     */
    public function showPaymentForm(string $id)
    {
        $invoice = Billing::with('patient.user')->findOrFail($id);
        return view('billing.payment', compact('invoice'));
    }
}
