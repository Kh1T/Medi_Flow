<x-admin title="Discharge Patient">
    <div class="row justify-content-center">
        <div class="col-md-10 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-warning"><i class="mdi mdi-exit-run"></i> Discharge Patient: {{ $ipd->patient->first_name }} {{ $ipd->patient->last_name }}</h4>
                    <p class="card-description">Review billing estimates and finalize discharge summary. Bed {{ $ipd->bed_number }} will be marked as available.</p>

                    <div class="alert alert-info py-2 mb-4">
                        <strong>Admission:</strong> {{ \Carbon\Carbon::parse($ipd->admission_date)->format('d M Y') }} - Present ({{ $days }} Days) <br>
                        <strong>Ward Rate:</strong> {{ $ipd->ward_type }} ({{ number_format($dailyRate, 2) }} / day)
                    </div>

                    <form class="forms-sample" action="{{ route('ipd.discharge.store', $ipd->id) }}" method="POST">
                        @csrf
                        
                        <div class="row border-bottom pb-4 mb-4">
                            <div class="col-md-12 form-group">
                                <label for="discharge_summary"><strong>Clinical Discharge Summary</strong></label>
                                <small class="d-block text-muted mb-2">Provide brief history, findings, procedures performed, condition on discharge, and discharge medications / advice.</small>
                                <textarea class="form-control @error('discharge_summary') is-invalid @enderror" id="discharge_summary" name="discharge_summary" rows="6" required>{{ old('discharge_summary') }}</textarea>
                                @error('discharge_summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <h5 class="mb-3">Final Billing Estimation</h5>
                        <div class="row border p-3 bg-light rounded mb-4">
                            <div class="col-md-4 form-group">
                                <label for="bed_charges">Est. Bed/Ward Charges</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                    <input type="number" step="0.01" class="form-control @error('bed_charges') is-invalid @enderror" id="bed_charges" name="bed_charges" value="{{ old('bed_charges', $bedCharges) }}" required>
                                </div>
                                <small class="text-muted d-block mt-1">{{ $days }} days @ {{ $dailyRate }}</small>
                                @error('bed_charges')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label for="medicine_charges">Est. Medication Charges</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                    <input type="number" step="0.01" class="form-control @error('medicine_charges') is-invalid @enderror" id="medicine_charges" name="medicine_charges" value="{{ old('medicine_charges', $estMedCharges) }}" required>
                                </div>
                                <small class="text-muted d-block mt-1">Calculated from IPD logs</small>
                                @error('medicine_charges')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="misc_charges">Procedures & Doctor Visits</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                    <input type="number" step="0.01" class="form-control @error('misc_charges') is-invalid @enderror" id="misc_charges" name="misc_charges" value="{{ old('misc_charges', 0) }}" required>
                                </div>
                                <small class="text-muted d-block mt-1">Surgical / Consul fees</small>
                                @error('misc_charges')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-danger mb-0"><strong>Note:</strong> Discharging this patient will automatically free Bed {{ $ipd->bed_number }} and draft an Invoice.</p>
                            <div>
                                <a href="{{ route('ipd.show', $ipd->id) }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Finalize Discharge? This will generate a bill and free the bed.');">Confirm Discharge</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
