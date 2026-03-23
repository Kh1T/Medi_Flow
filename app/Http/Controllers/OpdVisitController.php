<?php

namespace App\Http\Controllers;

use App\Models\OpdVisit;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Billing;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Services\BillingService;
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
            'prescription_items' => 'nullable|array',
            'prescription_items.*.medicine_name' => 'nullable|string|max:255',
            'prescription_items.*.dosage' => 'nullable|string|max:100',
            'prescription_items.*.quantity' => 'nullable|integer|min:1',
            'prescription_items.*.price' => 'nullable|numeric|min:0',
            'prescription_total' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();

        // Generate Token Number for the given day and doctor
        $latestToken = OpdVisit::where('doctor_id', $request->doctor_id)
            ->whereDate('visit_date', $request->visit_date)
            ->max('token_number');
            
        $data['token_number'] = $latestToken ? $latestToken + 1 : 1;

        // Create OPD Visit
        $visit = OpdVisit::create($data);

        // Handle prescription items if any
        if ($request->has('prescription_items') && !empty($request->prescription_items)) {
            $this->createPrescription($visit, $request);
        }

        return redirect()->route('opd.index')->with('success', 'OPD Visit registered successfully with Token #'.$data['token_number']);
    }

    /**
     * Create prescription with items for the visit
     */
    private function createPrescription(OpdVisit $visit, Request $request)
    {
        $prescriptionItems = $request->input('prescription_items', []);
        
        // Filter out empty items (items without medicine name)
        $validItems = array_filter($prescriptionItems, function($item) {
            return !empty($item['medicine_name']);
        });

        if (empty($validItems)) {
            return null;
        }

        // Create prescription linked to the OPD visit
        $prescription = Prescription::create([
            'patient_id' => $visit->patient_id,
            'doctor_id' => $visit->doctor_id,
            'opd_visit_id' => $visit->id,
            'visit_type' => 'opd',
            'diagnosis' => $visit->diagnosis ?? null,
            'status' => 'active',
        ]);

        // Create prescription items
        foreach ($validItems as $item) {
            PrescriptionItem::create([
                'prescription_id' => $prescription->id,
                'medicine_name' => $item['medicine_name'],
                'dosage' => $item['dosage'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'price' => $item['price'] ?? 0,
            ]);
        }

        return $prescription;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $visit = OpdVisit::with(['patient.user', 'doctor.user', 'prescriptions.prescriptionItems'])->findOrFail($id);
        return view('opd.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $visit = OpdVisit::with(['prescriptions.prescriptionItems'])->findOrFail($id);
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
            'prescription_items' => 'nullable|array',
            'prescription_items.*.id' => 'nullable|exists:prescription_items,id',
            'prescription_items.*.medicine_name' => 'nullable|string|max:255',
            'prescription_items.*.dosage' => 'nullable|string|max:100',
            'prescription_items.*.quantity' => 'nullable|integer|min:1',
            'prescription_items.*.price' => 'nullable|numeric|min:0',
            'removed_items' => 'nullable|string',
            'prescription_total' => 'nullable|numeric|min:0',
        ]);

        // Update OPD Visit
        $visit->update($request->all());

        // Handle prescription items update
        $this->updatePrescription($visit, $request);

        return redirect()->route('opd.index')->with('success', 'OPD Visit updated successfully.');
    }

    /**
     * Update prescription with items for the visit
     */
    private function updatePrescription(OpdVisit $visit, Request $request)
    {
        $prescriptionItems = $request->input('prescription_items', []);
        $removedItems = $request->input('removed_items') ? explode(',', $request->input('removed_items')) : [];
        
        // Filter out empty items (items without medicine name)
        $validItems = array_filter($prescriptionItems, function($item) {
            return !empty($item['medicine_name']);
        });

        // Delete removed items
        if (!empty($removedItems)) {
            PrescriptionItem::whereIn('id', $removedItems)->delete();
        }

        if (empty($validItems)) {
            // If no valid items, delete the prescription if it exists
            if ($visit->prescriptions && $visit->prescriptions->isNotEmpty()) {
                foreach ($visit->prescriptions as $prescription) {
                    $prescription->prescriptionItems()->delete();
                    $prescription->delete();
                }
            }
            return null;
        }

        // Get existing prescription or create new one
        $prescription = $visit->prescriptions && $visit->prescriptions->isNotEmpty() 
            ? $visit->prescriptions->first() 
            : Prescription::create([
                'patient_id' => $visit->patient_id,
                'doctor_id' => $visit->doctor_id,
                'opd_visit_id' => $visit->id,
                'visit_type' => 'opd',
                'diagnosis' => $visit->diagnosis ?? null,
                'status' => 'active',
            ]);

        // Process each item
        foreach ($validItems as $item) {
            if (!empty($item['id'])) {
                // Update existing item
                $prescriptionItem = PrescriptionItem::where('id', $item['id'])
                    ->where('prescription_id', $prescription->id)
                    ->first();
                
                if ($prescriptionItem) {
                    $prescriptionItem->update([
                        'medicine_name' => $item['medicine_name'],
                        'dosage' => $item['dosage'] ?? null,
                        'quantity' => $item['quantity'] ?? 1,
                        'price' => $item['price'] ?? 0,
                    ]);
                }
            } else {
                // Create new item
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_name' => $item['medicine_name'],
                    'dosage' => $item['dosage'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                ]);
            }
        }

        return $prescription;
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
        $visit = OpdVisit::with(['patient', 'doctor', 'prescriptions.prescriptionItems'])->findOrFail($id);

        // Check if an invoice already exists for this visit
        $existingInvoice = Billing::where('opd_visit_id', $visit->id)->first();
        if ($existingInvoice) {
            return redirect()->route('billing.show', $existingInvoice->id)
                ->with('info', 'An invoice already exists for this visit.');
        }

        // Calculate prescription charges from the visit's prescriptions
        $prescriptionCharges = 0;
        $prescriptionIds = [];
        
        if ($visit->prescriptions && $visit->prescriptions->isNotEmpty()) {
            foreach ($visit->prescriptions as $prescription) {
                $prescriptionIds[] = $prescription->id;
                if ($prescription->prescriptionItems) {
                    foreach ($prescription->prescriptionItems as $item) {
                        $prescriptionCharges += $item->quantity * $item->price;
                    }
                }
            }
        }

        // Calculate consultation fee
        $consultationFee = $visit->fee ?? 0;
        
        // Calculate subtotal
        $subtotal = $consultationFee + $prescriptionCharges;
        
        // Calculate tax (5%)
        $tax = $subtotal * 0.05;
        $total = $subtotal + $tax;

        // Create new invoice for the OPD visit
        $invoice = Billing::create([
            'patient_id' => $visit->patient_id,
            'opd_visit_id' => $visit->id,
            'invoice_number' => 'INV-OPD-' . strtoupper(str()->random(6)),
            'charges' => $subtotal,
            'consultation_fee' => $consultationFee,
            'prescription_charges' => $prescriptionCharges,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'patient_amount' => $total,
            'status' => $visit->payment_status == 'Paid' ? 'paid' : 'pending',
            'paid_amount' => $visit->payment_status == 'Paid' ? $total : 0,
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
