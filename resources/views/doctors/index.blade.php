<x-admin title="Doctors">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="card-title">Doctor Management</h4>
                            <p class="card-description">List of all registered healthcare providers</p>
                        </div>
                        <div>
                            <a href="{{ route('doctors.create') }}" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-account-plus"></i>Add New Doctor</a>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Doctor Name</th>
                                    <th>Specialization</th>
                                    <th>Email / Phone</th>
                                    <th>Consultation Fee</th>
                                    <th>Experience</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($doctors as $doctor)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $doctor->user->profile_photo ? asset('storage/' . $doctor->user->profile_photo) : asset('assets/images/faces/face1.jpg') }}" alt="profile" class="rounded-circle me-2" style="width:36px; height:36px;"/>
                                                <div>
                                                    <p class="mb-0 fw-bold">Dr. {{ $doctor->user->name }}</p>
                                                    <small class="text-muted">Lic: {{ $doctor->license_number }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><label class="badge badge-outline-primary">{{ $doctor->specialization }}</label></td>
                                        <td>
                                            {{ $doctor->user->email }}<br>
                                            <small>{{ $doctor->user->phone ?? 'N/A' }}</small>
                                        </td>
                                        <td>${{ number_format($doctor->consultation_fee, 2) }}</td>
                                        <td>{{ $doctor->experience_years }} Years</td>
                                        <td>
                                            <a href="{{ route('doctors.show', $doctor->id) }}" class="btn btn-outline-primary btn-sm">View</a>
                                            <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                                            <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this doctor record?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No doctors found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $doctors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
