<x-admin title="Appointments">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="card-title">Appointment Schedule</h4>
                            <p class="card-description">Manage doctor-patient consultations</p>
                        </div>
                        <div>
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-lg text-white mb-0 me-0"><i class="mdi mdi-calendar-plus"></i>Schedule New</a>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appt)
                                    <tr>
                                        <td>
                                            <p class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }}</p>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</small>
                                        </td>
                                        <td>{{ $appt->patient->user->name }}</td>
                                        <td>Dr. {{ $appt->doctor->user->name }}</td>
                                        <td>{{ Str::limit($appt->reason, 30) }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($appt->status) {
                                                    'Scheduled' => 'badge-info',
                                                    'Completed' => 'badge-success',
                                                    'Cancelled' => 'badge-danger',
                                                    'No Show' => 'badge-warning',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <label class="badge {{ $statusClass }}">{{ $appt->status }}</label>
                                        </td>
                                        <td>
                                            <a href="{{ route('appointments.edit', $appt->id) }}" class="btn btn-outline-warning btn-sm">Reschedule / Status</a>
                                            <form action="{{ route('appointments.destroy', $appt->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Cancel this appointment?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No appointments scheduled.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
