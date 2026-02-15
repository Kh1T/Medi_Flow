<x-admin title="Medical Records">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Electronic Medical Records</h4>
                    <p class="card-description">Search and view patient medical records</p>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form class="d-flex" action="{{ url('/records') }}" method="GET">
                                <input type="text" class="form-control me-2" name="search" placeholder="Search by patient name or ID..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Diagnosis</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->patient->first_name }} {{ $record->patient->last_name }}</td>
                                    <td>{{ $record->diagnosis }}</td>
                                    <td>Dr. {{ $record->doctor->last_name }}</td>
                                    <td>{{ $record->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ url('/records/'.$record->id) }}" class="btn btn-info btn-sm"><i class="mdi mdi-eye"></i></a>
                                    </td>
                                </tr>
                                @endforeach --}}
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No medical records found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
