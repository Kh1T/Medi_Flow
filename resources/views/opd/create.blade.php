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
                                    <select class="form-control @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                        <option value="Unpaid" {{ old('payment_status') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        <option value="Pending" {{ old('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Paid" {{ old('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                    @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

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

            doctorSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const fee = selectedOption.getAttribute('data-fee');
                
                if (fee) {
                    feeInput.value = parseFloat(fee).toFixed(2);
                } else {
                    feeInput.value = '0.00';
                }
            });

            // Trigger change if a doctor is already selected (e.g. on old validation failure)
            if (doctorSelect.value) {
                doctorSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endpush
</x-admin>
