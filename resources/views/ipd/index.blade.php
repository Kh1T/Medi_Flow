<x-admin title="IPD Admissions">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Inpatient Department (IPD)</h4>
                        <a href="{{ route('ipd.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus"></i> New Admission</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Admission Date</th>
                                    <th>Ward / Bed</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admissions as $ipd)
                                <tr>
                                    <td>
                                        <strong>{{ $ipd->patient->first_name }} {{ $ipd->patient->last_name }}</strong><br>
                                        <small class="text-muted">{{ $ipd->patient->phone }}</small>
                                    </td>
                                    <td>Dr. {{ $ipd->doctor->user->name }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($ipd->admission_date)->format('d M Y') }}<br>
                                        <small class="text-muted badge badge-{{ $ipd->admission_type == 'Emergency' ? 'danger' : 'info' }}">{{ $ipd->admission_type }}</small>
                                    </td>
                                    <td>
                                        {{ $ipd->ward_type }}<br>
                                        <strong>{{ $ipd->bed_number }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'admitted' => 'success',
                                                'discharged' => 'secondary',
                                                'transferred' => 'info',
                                                'cancelled' => 'danger'
                                            ];
                                            $color = $statusColors[$ipd->status] ?? 'dark';
                                        @endphp
                                        <span class="badge badge-{{ $color }}">{{ ucfirst($ipd->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('ipd.show', $ipd->id) }}" class="btn btn-sm btn-info" title="Manage Record"><i class="mdi mdi-eye"></i></a>
                                            <a href="{{ route('ipd.edit', $ipd->id) }}" class="btn btn-sm btn-primary" title="Edit details"><i class="mdi mdi-pencil"></i></a>
                                            @if($ipd->status == 'admitted')
                                                <a href="{{ route('ipd.discharge.create', $ipd->id) }}" class="btn btn-sm btn-warning" title="Discharge Patient"><i class="mdi mdi-exit-run"></i></a>
                                            @endif
                                            <form action="{{ route('ipd.destroy', $ipd->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this admission? The bed will be freed.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="mdi mdi-delete"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No patients currently admitted to IPD.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $admissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
