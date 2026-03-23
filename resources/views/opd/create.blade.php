<x-admin title="Register OPD Visit">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">New OPD Registration</h4>
                    <p class="card-description">Register a patient for an outpatient visit</p>
                    
                    <form class="forms-sample" action="{{ route('opd.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Select Patient</label>
                                    <select class="form-control @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                        <option value="">-- Select Patient --</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                                {{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->phone }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doctor_id">Select Doctor</label>
                                    <select class="form-control @error('doctor_id') is-invalid @enderror" id="doctor_id" name="doctor_id" required>
                                        <option value="">-- Select Doctor --</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" data-fee="{{ $doctor->consultation_fee }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
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
                                    <input type="date" class="form-control @error('visit_date') is-invalid @enderror" id="visit_date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required>
                                    @error('visit_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="visit_type">Visit Type</label>
                                    <select class="form-control @error('visit_type') is-invalid @enderror" id="visit_type" name="visit_type" required>
                                        <option value="New" {{ old('visit_type') == 'New' ? 'selected' : '' }}>New Visit</option>
                                        <option value="Follow-up" {{ old('visit_type') == 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
                                    </select>
                                    @error('visit_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Visit Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="symptoms">Symptoms Description</label>
                            <textarea class="form-control @error('symptoms') is-invalid @enderror" id="symptoms" name="symptoms" rows="4" required placeholder="Describe the patient's symptoms...">{{ old('symptoms') }}</textarea>
                            @error('symptoms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fee">Consultation Fee ($)</label>
                                    <input type="number" step="0.01" class="form-control @error('fee') is-invalid @enderror" id="fee" name="fee" value="{{ old('fee', '0.00') }}" required readonly>
                                    @error('fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Auto-filled based on selected doctor.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <input type="text" class="form-control" value="Unpaid" readonly>
                                    <input type="hidden" name="payment_status" value="Unpaid">
                                    <small class="text-muted">Payment is processed via billing after visit.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="card-title">Prescription Items</h5>
                        <p class="card-description text-muted">Add medicines prescribed to the patient (optional)</p>
                        
                        <div id="prescription-items-container">
                            <div class="prescription-item-row row mb-3" data-index="0">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="medicine_0">Medicine Name</label>
                                        <input type="text" class="form-control" id="medicine_0" name="prescription_items[0][medicine_name]" placeholder="e.g., Paracetamol">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="dosage_0">Dosage</label>
                                        <input type="text" class="form-control" id="dosage_0" name="prescription_items[0][dosage]" placeholder="e.g., 500mg">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="quantity_0">Quantity</label>
                                        <input type="number" class="form-control" id="quantity_0" name="prescription_items[0][quantity]" value="1" min="1">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="price_0">Price ($)</label>
                                        <input type="number" step="0.01" class="form-control" id="price_0" name="prescription_items[0][price]" value="0.00" min="0">
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-prescription-item" title="Remove">
                                        <i class="mdi mdi-minus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="add-prescription-item" class="btn btn-secondary btn-sm mb-3">
                            <i class="mdi mdi-plus"></i> Add Medicine
                        </button>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prescription_total">Prescription Total ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="prescription_total" name="prescription_total" value="0.00" readonly>
                                    <small class="text-muted">Auto-calculated from prescription items.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-primary me-2">Register Visit</button>
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
            
            let itemIndex = 1;

            // Handle doctor fee selection
            doctorSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const fee = selectedOption.getAttribute('data-fee');
                
                if (fee) {
                    feeInput.value = parseFloat(fee).toFixed(2);
                } else {
                    feeInput.value = '0.00';
                }
            });

            // Trigger change if a doctor is already selected
            if (doctorSelect.value) {
                doctorSelect.dispatchEvent(new Event('change'));
            }

            // Function to calculate prescription total
            function calculatePrescriptionTotal() {
                let total = 0;
                document.querySelectorAll('.prescription-item-row').forEach(function(row) {
                    const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
                    const price = parseFloat(row.querySelector('input[name*="[price]"]').value) || 0;
                    total += quantity * price;
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="dosage_${itemIndex}">Dosage</label>
                            <input type="text" class="form-control" id="dosage_${itemIndex}" name="prescription_items[${itemIndex}][dosage]" placeholder="e.g., 500mg">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="quantity_${itemIndex}">Quantity</label>
                            <input type="number" class="form-control" id="quantity_${itemIndex}" name="prescription_items[${itemIndex}][quantity]" value="1" min="1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="price_${itemIndex}">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" id="price_${itemIndex}" name="prescription_items[${itemIndex}][price]" value="0.00" min="0">
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-prescription-item" title="Remove">
                            <i class="mdi mdi-minus"></i>
                        </button>
                    </div>
                `;
                
                prescriptionContainer.appendChild(newRow);
                
                // Add event listeners for the new row
                const quantityInput = newRow.querySelector('input[name*="[quantity]"]');
                const priceInput = newRow.querySelector('input[name*="[price]"]');
                quantityInput.addEventListener('input', calculatePrescriptionTotal);
                priceInput.addEventListener('input', calculatePrescriptionTotal);
                
                itemIndex++;
            });

            // Remove prescription item (event delegation)
            prescriptionContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-prescription-item')) {
                    const rows = document.querySelectorAll('.prescription-item-row');
                    if (rows.length > 1) {
                        e.target.closest('.prescription-item-row').remove();
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
                quantityInput.addEventListener('input', calculatePrescriptionTotal);
                priceInput.addEventListener('input', calculatePrescriptionTotal);
            });
        });
    </script>
    @endpush
</x-admin>
