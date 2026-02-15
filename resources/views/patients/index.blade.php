<x-admin title="Patients">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="card-title">Patient Management</h4>
                            <p class="card-description">List of all registered patients in the system</p>
                        </div>
                        <div>
                            <a href="{{ route('patients.create') }}" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-account-plus"></i>Add New Patient</a>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('patients.index') }}" class="mb-4">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search name, email, phone" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="gender" class="form-select">
                                    <option value="">All Genders</option>
                                    <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ request('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="blood_group" class="form-select">
                                    <option value="">All Blood Groups</option>
                                    <option value="A+" {{ request('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ request('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ request('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ request('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ request('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ request('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ request('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ request('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Email / Phone</th>
                                    <th>Gender</th>
                                    <th>Blood Group</th>
                                    <th>Reg. Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patients as $patient)
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div>
                                                    <p class="mb-0">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                                    <small class="text-muted">DOB: {{ $patient->dob }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $patient->email }}<br>
                                            <small>{{ $patient->phone ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $patient->gender }}</td>
                                        <td><label class="badge badge-info">{{ $patient->blood_group ?? 'N/A' }}</label></td>
                                        <td>{{ $patient->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-outline-primary btn-sm">View</a>
                                            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this patient and their user account?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No patients found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $patients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
