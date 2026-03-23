<x-admin title="Doctor Details">
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="text-center pb-4">
                        <img src="{{ $doctor->user->profile_photo ? asset('storage/' . $doctor->user->profile_photo) : asset('assets/images/faces/face1.jpg') }}" alt="profile" class="img-lg rounded-circle mb-3"/>
                        <div class="mb-3">
                            <h3>Dr. {{ $doctor->user->name }}</h3>
                            <div class="d-flex align-items-center justify-content-center">
                                <h5 class="mb-0 me-2 text-muted">Lic: {{ $doctor->license_number }}</h5>
                            </div>
                        </div>
                        <p class="w-75 mx-auto mb-3">{{ $doctor->specialization }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-primary btn-sm">Edit Profile</a>
                        </div>
                    </div>
                    <div class="py-4">
                        <p class="clearfix">
                            <span class="float-left">Status</span>
                            <span class="float-right text-muted">{{ $doctor->is_available ? 'Available' : 'Unavailable' }}</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Phone</span>
                            <span class="float-right text-muted">{{ $doctor->user->phone ?? 'N/A' }}</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Email</span>
                            <span class="float-right text-muted">{{ $doctor->user->email }}</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Experience</span>
                            <span class="float-right text-muted">{{ $doctor->experience_years }} Years</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Consultation Fee</span>
                            <span class="float-right text-muted">${{ number_format($doctor->consultation_fee, 2) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Doctor Information</h4>
                    <ul class="nav nav-tabs tab-solid tab-solid-primary" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="visits-tab" data-bs-toggle="tab" href="#visits" role="tab" aria-controls="visits" aria-selected="true">OPD Visits</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="availability-tab" data-bs-toggle="tab" href="#availability" role="tab" aria-controls="availability" aria-selected="false">Availability</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Profile Info</a>
                        </li>
                    </ul>
                    <div class="tab-content tab-content-solid">
                        <div class="tab-pane fade show active" id="visits" role="tabpanel" aria-labelledby="visits-tab">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Token</th>
                                            <th>Patient</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($doctor->opdVisits()->latest()->take(10)->get() as $visit)
                                            <tr>
                                                <td>{{ $visit->visit_date->format('M d, Y') }}</td>
                                                <td>#{{ $visit->token_number }}</td>
                                                <td>{{ $visit->patient->first_name }} {{ $visit->patient->last_name }}</td>
                                                <td>{{ $visit->visit_type }}</td>
                                                <td><label class="badge badge-{{ $visit->status == 'completed' ? 'success' : ($visit->status == 'in_progress' ? 'info' : 'secondary') }}">{{ ucfirst(str_replace('_', ' ', $visit->status)) }}</label></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No OPD visits recorded.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="availability" role="tabpanel" aria-labelledby="availability-tab">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($doctor->availabilities as $availability)
                                            <tr>
                                                <td>{{ $availability->day_of_week }}</td>
                                                <td>{{ $availability->start_time }}</td>
                                                <td>{{ $availability->end_time }}</td>
                                                <td><label class="badge {{ $availability->is_available ? 'badge-success' : 'badge-danger' }}">{{ $availability->is_available ? 'Available' : 'Unavailable' }}</label></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No availability set.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <h6>Qualification</h6>
                            <p>{{ $doctor->qualification ?? 'Not specified' }}</p>
                            <hr>
                            <h6>License Number</h6>
                            <p>{{ $doctor->license_number }}</p>
                            <hr>
                            <h6>Specialization</h6>
                            <p>{{ $doctor->specialization }}</p>
                            <hr>
                            <h6>Member Since</h6>
                            <p>{{ $doctor->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
