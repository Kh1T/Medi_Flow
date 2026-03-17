<?php

namespace App\Http\Controllers;

use App\Models\OpdVisit;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpdVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = OpdVisit::with(['patient.user', 'doctor.user'])->latest();
        $doctors = Doctor::with('user')->where('is_available', true)->get();

        if (request('search')) {
            $search = request('search');
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        if (request('doctor_id')) {
            $query->where('doctor_id', request('doctor_id'));
        }
        
        if(request('visit_date')) {
            $query->whereDate('visit_date', request('visit_date'));
        }

        $visits = $query->paginate(15);
        
        return view('opd.index', compact('visits', 'doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::with('user')->get();
        // Get doctors with fee information formatted for JS
        $doctors = Doctor::with('user')->where('is_available', true)->get();
        
        return view('opd.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'visit_date' => 'required|date',
            'symptoms' => 'required|string',
            'visit_type' => 'required|in:New,Follow-up',
            'fee' => 'required|numeric|min:0',
            'payment_status' => 'required|in:Paid,Unpaid,Pending',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $data = $request->all();

        // Generate Token Number for the given day and doctor
        $latestToken = OpdVisit::where('doctor_id', $request->doctor_id)
            ->whereDate('visit_date', $request->visit_date)
            ->max('token_number');
            
        $data['token_number'] = $latestToken ? $latestToken + 1 : 1;

        OpdVisit::create($data);

        return redirect()->route('opd.index')->with('success', 'OPD Visit registered successfully with Token #'.$data['token_number']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $visit = OpdVisit::with(['patient.user', 'doctor.user'])->findOrFail($id);
        return view('opd.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $visit = OpdVisit::findOrFail($id);
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->where('is_available', true)->get();

        return view('opd.edit', compact('visit', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $visit = OpdVisit::findOrFail($id);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'visit_date' => 'required|date',
            'symptoms' => 'required|string',
            'diagnosis' => 'nullable|string',
            'visit_type' => 'required|in:New,Follow-up',
            'fee' => 'required|numeric|min:0',
            'payment_status' => 'required|in:Paid,Unpaid,Pending',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $visit->update($request->all());

        return redirect()->route('opd.index')->with('success', 'OPD Visit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $visit = OpdVisit::findOrFail($id);
        $visit->delete();

        return redirect()->route('opd.index')->with('success', 'OPD Visit deleted successfully.');
    }

    /**
     * Convert OPD visit to a Billing/Invoice.
     */
    public function generateInvoice(string $id)
    {
        $visit = OpdVisit::with(['patient', 'doctor'])->findOrFail($id);

        // Check if an invoice already exists for this visit
        $existingInvoice = Billing::where('opd_visit_id', $visit->id)->first();
        if ($existingInvoice) {
            return redirect()->route('billing.show', $existingInvoice->id)
                ->with('info', 'An invoice already exists for this visit.');
        }

        // Create new invoice for the OPD visit
        $invoice = Billing::create([
            'patient_id' => $visit->patient_id,
            'opd_visit_id' => $visit->id,
            'invoice_number' => 'INV-OPD-' . strtoupper(str()->random(6)),
            'charges' => $visit->fee,
            'subtotal' => $visit->fee,
            'total' => $visit->fee, // Assuming no tax or insurance applied immediately here
            'patient_amount' => $visit->fee,
            'status' => $visit->payment_status == 'Paid' ? 'paid' : 'pending',
            'paid_amount' => $visit->payment_status == 'Paid' ? $visit->fee : 0,
            'due_date' => now()->addDays(7)->toDateString(),
        ]);

        return redirect()->route('billing.show', $invoice->id)->with('success', 'Invoice generated from OPD visit.');
    }

    /**
     * Show a print view for the Prescription/Notes.
     */
    public function printPrescription(string $id)
    {
        $visit = OpdVisit::with(['patient.user', 'doctor.user'])->findOrFail($id);
        
        return view('opd.prescription', compact('visit'));
    }
}
