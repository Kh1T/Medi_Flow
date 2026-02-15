<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;

class BillingController extends Controller
{
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'charges' => 'required|numeric|min:0',
            'contractual_adjustments' => 'nullable|numeric|min:0',
            'insurance_coverage' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,paid,partially_paid,overdue,cancelled',
            'due_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['invoice_number'] = 'INV-' . strtoupper(str()->random(8));
        
        // Calculate Patient Responsibility
        // Formula: Charges - Contractual Adjustments - Insurance Payments = Patient Responsibility
        $charges = floatval($request->charges ?? 0);
        $contractual_adjustments = floatval($request->contractual_adjustments ?? 0);
        $insurance_coverage = floatval($request->insurance_coverage ?? 0);
        
        $data['charges'] = $charges;
        $data['contractual_adjustments'] = $contractual_adjustments;
        $data['insurance_coverage'] = $insurance_coverage;
        $data['patient_amount'] = $charges - $contractual_adjustments - $insurance_coverage;
        $data['total'] = $data['patient_amount']; // Total is what patient owes
        $data['subtotal'] = $charges; // Subtotal is the base charges

        Billing::create($data);

        return redirect()->route('billing.index')->with('success', 'Invoice generated successfully');
    }

    /**
     * Display the specified invoice.
     */
    public function show(string $id)
    {
        $bill = Billing::with(['patient.user', 'appointment.doctor.user'])->findOrFail($id);
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
}
