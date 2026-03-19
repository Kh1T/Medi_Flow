<x-admin title="OPD Visits">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Outpatient Department (OPD) Visits</h4>
                        <a href="{{ route('opd.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus"></i> New OPD Visit</a>
                    </div>
                    
                    <form action="{{ route('opd.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <input type="text" name="search" class="form-control" placeholder="Search Patient/Phone" value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <select name="doctor_id" class="form-control">
                                        <option value="">All Doctors</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>Dr. {{ $doctor->user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <input type="date" name="visit_date" class="form-control" value="{{ request('visit_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Token</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                <tr>
                                    <td>#{{ $visit->id }}</td>
                                    <td>
                                        <span class="badge badge-dark fs-6">{{ $visit->token_number ? 'T-'.$visit->token_number : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $visit->patient->first_name }} {{ $visit->patient->last_name }}</strong><br>
                                        <small class="text-muted">{{ $visit->patient->phone }}</small>
                                    </td>
                                    <td>Dr. {{ $visit->doctor->user->name }}</td>
                                    <td>{{ $visit->visit_date->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $visit->visit_type == 'New' ? 'primary' : 'info' }}">{{ $visit->visit_type }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'scheduled' => 'warning',
                                                'in_progress' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ][$visit->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">{{ ucfirst($visit->status) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $payClass = [
                                                'Paid' => 'success',
                                                'Unpaid' => 'danger',
                                                'Pending' => 'warning',
                                            ][$visit->payment_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $payClass }}">{{ $visit->payment_status }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('opd.show', $visit->id) }}" class="btn btn-sm btn-info" title="View Details"><i class="mdi mdi-eye"></i></a>
                                            <a href="{{ route('opd.edit', $visit->id) }}" class="btn btn-sm btn-primary" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                            <!-- Additional actions -->
                                            <a href="{{ route('opd.prescription', $visit->id) }}" class="btn btn-sm btn-secondary" title="Print Prescription" target="_blank"><i class="mdi mdi-printer"></i></a>
                                            @if($visit->payment_status !== 'Paid')
                                            <form action="{{ route('opd.invoice', $visit->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning rounded-0" title="Generate Invoice"><i class="mdi mdi-receipt"></i></button>
                                            </form>
                                            @endif
                                            <form action="{{ route('opd.destroy', $visit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this visit?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded-0 rounded-end" title="Delete"><i class="mdi mdi-delete"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No OPD visits found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $visits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
