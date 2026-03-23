<x-admin title="Edit OPD Visit">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit OPD Visit: #{{ $visit->id }}</h4>
                    <p class="card-description">Update patient visit details</p>
                    
                    <form class="forms-sample" action="{{ route('opd.update', $visit->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Patient</label>
                                    <select class="form-control @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" {{ old('patient_id', $visit->patient_id) == $patient->id ? 'selected' : '' }}>
                                                {{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->phone }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doctor_id">Doctor</label>
                                    <select class="form-control @error('doctor_id') is-invalid @enderror" id="doctor_id" name="doctor_id" required>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" data-fee="{{ $doctor->consultation_fee }}" {{ old('doctor_id', $visit->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                                Dr. {{ $doctor->user->name }} - {{ $doctor->specialization }} (${{ $doctor->consultation_fee }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="visit_date">Visit Date</label>
                                    <input type="date" class="form-control @error('visit_date') is-invalid @enderror" id="visit_date" name="visit_date" value="{{ old('visit_date', $visit->visit_date->format('Y-m-d')) }}" required>
                                    @error('visit_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="visit_type">Visit Type</label>
                                    <select class="form-control @error('visit_type') is-invalid @enderror" id="visit_type" name="visit_type" required>
                                        <option value="New" {{ old('visit_type', $visit->visit_type) == 'New' ? 'selected' : '' }}>New Visit</option>
                                        <option value="Follow-up" {{ old('visit_type', $visit->visit_type) == 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
                                    </select>
                                    @error('visit_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Visit Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="scheduled" {{ old('status', $visit->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="in_progress" {{ old('status', $visit->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status', $visit->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $visit->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="symptoms">Symptoms Description</label>
                            <textarea class="form-control @error('symptoms') is-invalid @enderror" id="symptoms" name="symptoms" rows="4" required>{{ old('symptoms', $visit->symptoms) }}</textarea>
                            @error('symptoms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="diagnosis">Diagnosis / Notes (Optional)</label>
                            <textarea class="form-control @error('diagnosis') is-invalid @enderror" id="diagnosis" name="diagnosis" rows="4">{{ old('diagnosis', $visit->diagnosis) }}</textarea>
                            @error('diagnosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fee">Consultation Fee ($)</label>
                                    <input type="number" step="0.01" class="form-control @error('fee') is-invalid @enderror" id="fee" name="fee" value="{{ old('fee', $visit->fee) }}" required>
                                    @error('fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select class="form-control @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                        <option value="Unpaid" {{ old('payment_status', $visit->payment_status) == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        <option value="Pending" {{ old('payment_status', $visit->payment_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Paid" {{ old('payment_status', $visit->payment_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                    @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="card-title">Prescription Items</h5>
                        <p class="card-description text-muted">Add medicines prescribed to the patient (optional)</p>
                        
                        <input type="hidden" name="removed_items" id="removed_items" value="">
                        
                        <div id="prescription-items-container">
                            @php
                                $prescriptionItems = [];
                                $prescriptionTotal = 0;
                                if ($visit->prescriptions && $visit->prescriptions->isNotEmpty()) {
                                    foreach ($visit->prescriptions as $prescription) {
                                        if ($prescription->prescriptionItems) {
                                            foreach ($prescription->prescriptionItems as $item) {
                                                $prescriptionItems[] = $item;
                                                $prescriptionTotal += $item->quantity * $item->price;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            
                            @if(count($prescriptionItems) > 0)
                                @foreach($prescriptionItems as $index => $item)
                                    <div class="prescription-item-row row mb-3" data-index="{{ $index }}" data-item-id="{{ $item->id }}">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="medicine_{{ $index }}">Medicine Name</label>
                                                <input type="hidden" name="prescription_items[{{ $index }}][id]" value="{{ $item->id }}">
                                                <input type="text" class="form-control" name="prescription_items[{{ $index }}][medicine_name]" value="{{ old('prescription_items.'.$index.'.medicine_name', $item->medicine_name) }}" placeholder="e.g., Paracetamol">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="dosage_{{ $index }}">Dosage</label>
                                                <input type="text" class="form-control" id="dosage_{{ $index }}" name="prescription_items[{{ $index }}][dosage]" value="{{ old('prescription_items.'.$index.'.dosage', $item->dosage) }}" placeholder="e.g., 500mg">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="quantity_{{ $index }}">Quantity</label>
                                                <input type="number" class="form-control prescription-quantity" id="quantity_{{ $index }}" name="prescription_items[{{ $index }}][quantity]" value="{{ old('prescription_items.'.$index.'.quantity', $item->quantity) }}" min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="price_{{ $index }}">Price ($)</label>
                                                <input type="number" step="0.01" class="form-control prescription-price" id="price_{{ $index }}" name="prescription_items[{{ $index }}][price]" value="{{ old('prescription_items.'.$index.'.price', $item->price) }}" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-prescription-item" title="Remove">
                                                <i class="mdi mdi-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="prescription-item-row row mb-3" data-index="0">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="medicine_0">Medicine Name</label>
                                            <input type="text" class="form-control" id="medicine_0" name="prescription_items[0][medicine_name]" placeholder="e.g., Paracetamol">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="dosage_0">Dosage</label>
                                            <input type="text" class="form-control" id="dosage_0" name="prescription_items[0][dosage]" placeholder="e.g., 500mg">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="quantity_0">Quantity</label>
                                            <input type="number" class="form-control prescription-quantity" id="quantity_0" name="prescription_items[0][quantity]" value="1" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="price_0">Price ($)</label>
                                            <input type="number" step="0.01" class="form-control prescription-price" id="price_0" name="prescription_items[0][price]" value="0.00" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-prescription-item" title="Remove">
                                            <i class="mdi mdi-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <button type="button" id="add-prescription-item" class="btn btn-secondary btn-sm mb-3">
                            <i class="mdi mdi-plus"></i> Add Medicine
                        </button>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prescription_total">Prescription Total ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="prescription_total" name="prescription_total" value="{{ old('prescription_total', number_format($prescriptionTotal, 2, '.', '')) }}" readonly>
                                    <small class="text-muted">Auto-calculated from prescription items.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-primary me-2">Update Visit</button>
                        <a href="{{ route('opd.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const doctorSelect = document.getElementById('doctor_id');
            const feeInput = document.getElementById('fee');
            const prescriptionContainer = document.getElementById('prescription-items-container');
            const addBtn = document.getElementById('add-prescription-item');
            const prescriptionTotalInput = document.getElementById('prescription_total');
            const removedItemsInput = document.getElementById('removed_items');
            
            let itemIndex = {{ count($prescriptionItems) > 0 ? count($prescriptionItems) : 1 }};
            let removedItems = [];

            // Handle doctor fee selection
            doctorSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const fee = selectedOption.getAttribute('data-fee');
                
                if (fee) {
                    feeInput.value = parseFloat(fee).toFixed(2);
                }
            });

            // Function to calculate prescription total
            function calculatePrescriptionTotal() {
                let total = 0;
                document.querySelectorAll('.prescription-item-row').forEach(function(row) {
                    const quantityInput = row.querySelector('input[name*="[quantity]"]');
                    const priceInput = row.querySelector('input[name*="[price]"]');
                    const medicineInput = row.querySelector('input[name*="[medicine_name]"]');
                    
                    // Only calculate if medicine name is not empty
                    if (medicineInput && medicineInput.value.trim() !== '') {
                        const quantity = parseFloat(quantityInput ? quantityInput.value : 0) || 0;
                        const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
                        total += quantity * price;
                    }
                });
                prescriptionTotalInput.value = total.toFixed(2);
            }

            // Add new prescription item
            addBtn.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'prescription-item-row row mb-3';
                newRow.setAttribute('data-index', itemIndex);
                newRow.innerHTML = `
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="medicine_${itemIndex}">Medicine Name</label>
                            <input type="text" class="form-control" id="medicine_${itemIndex}" name="prescription_items[${itemIndex}][medicine_name]" placeholder="e.g., Paracetamol">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="dosage_${itemIndex}">Dosage</label>
                            <input type="text" class="form-control" id="dosage_${itemIndex}" name="prescription_items[${itemIndex}][dosage]" placeholder="e.g., 500mg">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="quantity_${itemIndex}">Quantity</label>
                            <input type="number" class="form-control prescription-quantity" id="quantity_${itemIndex}" name="prescription_items[${itemIndex}][quantity]" value="1" min="1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="price_${itemIndex}">Price ($)</label>
                            <input type="number" step="0.01" class="form-control prescription-price" id="price_${itemIndex}" name="prescription_items[${itemIndex}][price]" value="0.00" min="0">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-prescription-item" title="Remove">
                            <i class="mdi mdi-minus"></i>
                        </button>
                    </div>
                `;
                
                prescriptionContainer.appendChild(newRow);
                
                // Add event listeners for the new row
                const quantityInput = newRow.querySelector('input[name*="[quantity]"]');
                const priceInput = newRow.querySelector('input[name*="[price]"]');
                const medicineInput = newRow.querySelector('input[name*="[medicine_name]"]');
                quantityInput.addEventListener('input', calculatePrescriptionTotal);
                priceInput.addEventListener('input', calculatePrescriptionTotal);
                medicineInput.addEventListener('input', calculatePrescriptionTotal);
                
                itemIndex++;
            });

            // Remove prescription item (event delegation)
            prescriptionContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-prescription-item')) {
                    const row = e.target.closest('.prescription-item-row');
                    const itemId = row.getAttribute('data-item-id');
                    
                    // Track removed items for deletion on server
                    if (itemId) {
                        removedItems.push(itemId);
                        removedItemsInput.value = removedItems.join(',');
                    }
                    
                    const rows = document.querySelectorAll('.prescription-item-row');
                    if (rows.length > 1) {
                        row.remove();
                        calculatePrescriptionTotal();
                    } else {
                        // Clear the first row instead of removing it
                        const firstRow = rows[0];
                        firstRow.querySelector('input[name*="[medicine_name]"]').value = '';
                        firstRow.querySelector('input[name*="[dosage]"]').value = '';
                        firstRow.querySelector('input[name*="[quantity]"]').value = '1';
                        firstRow.querySelector('input[name*="[price]"]').value = '0.00';
                        calculatePrescriptionTotal();
                    }
                }
            });

            // Add event listeners for existing rows
            document.querySelectorAll('.prescription-item-row').forEach(function(row) {
                const quantityInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[price]"]');
                const medicineInput = row.querySelector('input[name*="[medicine_name]"]');
                quantityInput.addEventListener('input', calculatePrescriptionTotal);
                priceInput.addEventListener('input', calculatePrescriptionTotal);
                if (medicineInput) {
                    medicineInput.addEventListener('input', calculatePrescriptionTotal);
                }
            });
            
            // Initial calculation
            calculatePrescriptionTotal();
        });
    </script>
    @endpush
</x-admin>
