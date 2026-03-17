<x-admin title="Edit Admission">
    <div class="row justify-content-center">
        <div class="col-md-10 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Admission: #{{ str_pad($ipd->id, 5, '0', STR_PAD_LEFT) }}</h4>
                    <p class="card-description">Patient: {{ $ipd->patient->first_name }} {{ $ipd->patient->last_name }}</p>
                    
                    <form class="forms-sample" action="{{ route('ipd.update', $ipd->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row border-bottom pb-4 mb-4">
                            <div class="col-md-6 form-group">
                                <label>Current Ward & Bed</label>
                                <input type="text" class="form-control" value="{{ $ipd->ward_type }} - {{ $ipd->bed_number }}" readonly disabled>
                                <small class="text-muted d-block mt-1">Bed transfers must be handled separately.</small>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="doctor_id">Primary Physician (Assigned Doctor)</label>
                                <select class="form-control @error('doctor_id') is-invalid @enderror" id="doctor_id" name="doctor_id" required>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_id', $ipd->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                            Dr. {{ $doctor->user->name }} ({{ $doctor->specialization }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="admission_reason">Pre-Diagnosis / Reason for Admission</label>
                            <textarea class="form-control @error('admission_reason') is-invalid @enderror" id="admission_reason" name="admission_reason" rows="3" required>{{ old('admission_reason', $ipd->admission_reason) }}</textarea>
                            @error('admission_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="symptoms">Noted Symptoms</label>
                            <textarea class="form-control @error('symptoms') is-invalid @enderror" id="symptoms" name="symptoms" rows="2">{{ old('symptoms', $ipd->symptoms) }}</textarea>
                            @error('symptoms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="diagnosis">Final / Working Diagnosis</label>
                            <textarea class="form-control @error('diagnosis') is-invalid @enderror" id="diagnosis" name="diagnosis" rows="2">{{ old('diagnosis', $ipd->diagnosis) }}</textarea>
                            @error('diagnosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update Admission</button>
                            <a href="{{ route('ipd.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
