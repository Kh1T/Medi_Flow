<x-admin title="Patient Details">
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="text-center pb-4">
                        <img src="{{ $patient->user->profile_photo ? asset('storage/' . $patient->user->profile_photo) : asset('assets/images/faces/face8.jpg') }}" alt="profile" class="img-lg rounded-circle mb-3"/>
                        <div class="mb-3">
                            <h3>{{ $patient->first_name }} {{ $patient->last_name }}</h3>
                            <div class="d-flex align-items-center justify-content-center">
                                <h5 class="mb-0 me-2 text-muted">ID: PAT-{{ str_pad($patient->id, 5, '0', STR_PAD_LEFT) }}</h5>
                            </div>
                        </div>
                        <p class="w-75 mx-auto mb-3">Patient since {{ $patient->created_at->format('M Y') }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-primary btn-sm">Edit Profile</a>
                        </div>
                    </div>
                    <div class="py-4">
                        <p class="clearfix">
                            <span class="float-left">Status</span>
                            <span class="float-right text-muted">Active</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Phone</span>
                            <span class="float-right text-muted">{{ $patient->user->phone ?? 'N/A' }}</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Mail</span>
                            <span class="float-right text-muted">{{ $patient->user->email }}</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Gender</span>
                            <span class="float-right text-muted">{{ $patient->gender }}</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Blood Group</span>
                            <span class="float-right text-muted">{{ $patient->blood_group ?? 'Unknown' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Medical Information</h4>
                    <ul class="nav nav-tabs tab-solid tab-solid-primary" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="history-tab" data-bs-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="true">Appointments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="records-tab" data-bs-toggle="tab" href="#records" role="tab" aria-controls="records" aria-selected="false">Clinical Notes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="false">Profile Info</a>
                        </li>
                    </ul>
                    <div class="tab-content tab-content-solid">
                        <div class="tab-pane fade show active" id="history" role="tabpanel" aria-labelledby="history-tab">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Doctor</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($patient->appointments as $appt)
                                            <tr>
                                                <td>{{ $appt->appointment_date }}</td>
                                                <td>Dr. {{ $appt->doctor->user->name }}</td>
                                                <td>{{ $appt->reason }}</td>
                                                <td><label class="badge badge-info">{{ $appt->status }}</label></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No appointment history.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
                            <h6>Full Address</h6>
                            <p>{{ $patient->address }}</p>
                            <hr>
                            <h6>Emergency Contact</h6>
                            <p><strong>Name:</strong> {{ $patient->emergency_contact ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $patient->emergency_phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
