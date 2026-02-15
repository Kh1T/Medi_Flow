<x-admin title="Dashboard Summary">
    <div class="row">
        <!-- Stats Widgets -->
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Total Patients</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-account-multiple mdi-36px"></i>
                        <h2 class="mb-0">{{ $stats['patients'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Active Doctors</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-doctor mdi-36px"></i>
                        <h2 class="mb-0">{{ $stats['doctors'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Appointments</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-calendar-check mdi-36px"></i>
                        <h2 class="mb-0">{{ $stats['appointments'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Total Revenue</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-currency-usd mdi-36px"></i>
                        <h2 class="mb-0">${{ number_format($stats['revenue'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
     
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Appointments</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_appointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->patient->user->name ?? 'N/A' }}</td>
                                        <td>Dr. {{ $appointment->doctor->user->name ?? 'N/A' }}</td>
                                        <td>{{ $appointment->appointment_date }}</td>
                                        <td>
                                            <label class="badge badge-info">{{ ucfirst($appointment->status) }}</label>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No recent appointments.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
