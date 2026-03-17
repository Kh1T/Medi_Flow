<x-admin title="New IPD Admission">
    <div class="row justify-content-center">
        <div class="col-md-10 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">New Patient Admission (IPD)</h4>
                    <p class="card-description">Admit a patient and auto-assign a bed.</p>
                    
                    <form class="forms-sample" action="{{ route('ipd.store') }}" method="POST">
                        @csrf
                        
                        <div class="row border-bottom pb-4 mb-4">
                            <div class="col-md-6 form-group">
                                <label for="patient_id">Patient</label>
                                <select class="form-control @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id', $opdVisit?->patient_id) == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->phone }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="doctor_id">Primary Physician (Assigned Doctor)</label>
                                <select class="form-control @error('doctor_id') is-invalid @enderror" id="doctor_id" name="doctor_id" required>
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_id', $opdVisit?->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                            Dr. {{ $doctor->user->name }} ({{ $doctor->specialization }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="admission_date">Admission Date</label>
                                <input type="date" class="form-control @error('admission_date') is-invalid @enderror" id="admission_date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required>
                                @error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="admission_type">Type of Admission</label>
                                <select class="form-control @error('admission_type') is-invalid @enderror" id="admission_type" name="admission_type" required>
                                    <option value="Planned" {{ old('admission_type') == 'Planned' ? 'selected' : '' }}>Planned</option>
                                    <option value="Emergency" {{ old('admission_type') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                                </select>
                                @error('admission_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="ward_type">Target Ward</label>
                                <select class="form-control @error('ward_type') is-invalid @enderror" id="ward_type" name="ward_type" required>
                                    <option value="General" {{ old('ward_type') == 'General' ? 'selected' : '' }}>General Ward</option>
                                    <option value="Private" {{ old('ward_type') == 'Private' ? 'selected' : '' }}>Private Ward</option>
                                    <option value="ICU" {{ old('ward_type') == 'ICU' ? 'selected' : '' }}>ICU</option>
                                </select>
                                <small class="text-muted d-block mt-1">Bed number will be automatically assigned.</small>
                                @error('ward_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="admission_reason">Pre-Diagnosis / Reason for Admission</label>
                            <textarea class="form-control @error('admission_reason') is-invalid @enderror" id="admission_reason" name="admission_reason" rows="2" required>{{ old('admission_reason', $opdVisit?->diagnosis) }}</textarea>
                            @error('admission_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="symptoms">Noted Symptoms</label>
                            <textarea class="form-control @error('symptoms') is-invalid @enderror" id="symptoms" name="symptoms" rows="2">{{ old('symptoms', $opdVisit?->symptoms) }}</textarea>
                            @error('symptoms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Admit Patient</button>
                            <a href="{{ route('ipd.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
