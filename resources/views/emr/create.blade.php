<x-admin title="Add Clinical Note">
    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">New Medical Record</h4>
                    <p class="card-description">Record diagnosis and treatment details</p>
                    <form class="forms-sample" action="{{ route('emr.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="patient_id">Patient</label>
                            <select class="form-control" name="patient_id" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="doctor_id">Doctor</label>
                            <select class="form-control" name="doctor_id" required>
                                <option value="">Select Doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">Dr. {{ $doctor->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="record_date">Date</label>
                            <input type="date" class="form-control" name="record_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="diagnosis">Diagnosis</label>
                            <textarea class="form-control" name="diagnosis" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="treatment">Treatment Plan</label>
                            <textarea class="form-control" name="treatment" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Save Record</button>
                        <a href="{{ route('emr.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
