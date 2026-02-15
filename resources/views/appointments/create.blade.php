<x-admin title="Schedule Appointment">
    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Book New Appointment</h4>
                    <p class="card-description">Select patient and doctor to schedule a visit</p>
                    <form class="forms-sample" action="{{ route('appointments.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="patient_id">Patient</label>
                            <select class="form-control select2 @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->user->name }} ({{ $patient->user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="doctor_id">Doctor</label>
                            <select class="form-control @error('doctor_id') is-invalid @enderror" id="doctor_id" name="doctor_id" required>
                                <option value="">Select Doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->user->name }} - {{ $doctor->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_date">Appointment Date</label>
                                    <input type="date" class="form-control @error('appointment_date') is-invalid @enderror" id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" required>
                                    @error('appointment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_time">Preferred Time</label>
                                    <input type="time" class="form-control @error('appointment_time') is-invalid @enderror" id="appointment_time" name="appointment_time" value="{{ old('appointment_time') }}" required>
                                    @error('appointment_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reason">Reason for Visit</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Symptoms or purpose...">{{ old('reason') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Schedule Appointment</button>
                        <a href="{{ route('appointments.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
