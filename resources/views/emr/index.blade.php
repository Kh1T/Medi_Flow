<x-admin title="Medical Records">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="card-title">Electronic Medical Records</h4>
                            <p class="card-description">History of patient diagnoses and treatments</p>
                        </div>
                        <div>
                            <a href="{{ route('emr.create') }}" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-plus"></i>Add Clinical Note</a>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Treatment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($record->record_date)->format('d M Y') }}</td>
                                        <td>{{ $record->patient->user->name }}</td>
                                        <td>Dr. {{ $record->doctor->user->name }}</td>
                                        <td>{{ Str::limit($record->diagnosis, 30) }}</td>
                                        <td>{{ Str::limit($record->treatment, 30) }}</td>
                                        <td>
                                            <a href="{{ route('emr.show', $record->id) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No medical records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $records->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
