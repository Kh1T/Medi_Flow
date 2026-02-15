<x-admin title="Medical Record Details">
    <div class="row">
        <div class="col-md-10 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Clinical Record: {{ $record->patient->user->name }}</h4>
                        <span class="badge badge-info">{{ $record->visit_date }}</span>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Patient Information</h6>
                            <p><strong>Name:</strong> {{ $record->patient->user->name }}</p>
                            <p><strong>Age:</strong> {{ \Carbon\Carbon::parse($record->patient->date_of_birth)->age }} Years</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Doctor Information</h6>
                            <p><strong>Name:</strong> Dr. {{ $record->doctor->user->name }}</p>
                            <p><strong>Specialization:</strong> {{ $record->doctor->specialization }}</p>
                        </div>
                    </div>

                    <div class="admin-clinical-notes mb-4">
                        <h6 class="text-muted">Diagnosis</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $record->diagnosis }}
                        </div>
                    </div>

                    <div class="admin-clinical-notes mb-4">
                        <h6 class="text-muted">Treatment Plan</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $record->treatment }}
                        </div>
                    </div>

                    <div class="admin-notes mb-4">
                        <h6 class="text-muted">Internal Notes</h6>
                        <p>{{ $record->notes ?? 'No additional notes provided.' }}</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('emr.index') }}" class="btn btn-secondary">Back to Records</a>
                        <button onclick="window.print()" class="btn btn-info">Print Record</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
