<x-admin title="Dashboard Summary">
    <div class="row">
        <!-- Stats Widgets -->
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Total Patients</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-account-multiple mdi-36px"></i>
                        <h2 class="mb-0">{{ $stats['total_patients'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Today's OPD Visits</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-stethoscope mdi-36px"></i>
                        <h2 class="mb-0">{{ $stats['today_opd_visits'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Current IPD Patients</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-bed-empty mdi-36px"></i>
                        <h2 class="mb-0">{{ $stats['current_ipd_patients'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h4 class="card-title text-white">Active Doctors</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-doctor mdi-36px"></i>
                        <h2 class="mb-0">{{ $stats['doctors'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Monthly Revenue (6 Months)</h4>
                    <div style="position: relative; height: 300px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bed Occupancy Doughnut -->
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Bed Occupancy</h4>
                    <div style="position: relative; height: 300px;">
                        <canvas id="bedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Doctor Stats -->
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Top 5 Doctors by Patient Load</h4>
                    <div style="position: relative; height: 250px;">
                        <canvas id="doctorChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Popular Diagnoses -->
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Most Common Diagnoses</h4>
                    @if(count($diagnosesRaw) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($diagnosesRaw as $diag)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ Str::title($diag->diagnosis) }}
                                <span class="badge badge-primary badge-pill">{{ $diag->count }} Cases</span>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center py-4">Not enough diagnosis data available yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Recent Appointments</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
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
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y h:i A') }}</td>
                                        <td>
                                            @php $aCols = ['scheduled'=>'info','completed'=>'success','cancelled'=>'danger']; @endphp
                                            <label class="badge badge-{{ $aCols[$appointment->status] ?? 'secondary' }}">{{ ucfirst($appointment->status) }}</label>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No recent appointments.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inject Chart.js -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Line/Bar Chart
            const ctxRev = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctxRev, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($revenueData['labels']) !!},
                    datasets: [
                        {
                            label: 'OPD Revenue',
                            data: {!! json_encode($revenueData['opd']) !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'IPD Revenue',
                            data: {!! json_encode($revenueData['ipd']) !!},
                            backgroundColor: 'rgba(255, 99, 132, 0.7)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, stacked: true },
                        x: { stacked: true }
                    }
                }
            });

            // Bed Occupancy Doughnut
            const ctxBed = document.getElementById('bedChart').getContext('2d');
            new Chart(ctxBed, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($bedStats['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($bedStats['data']) !!},
                        backgroundColor: ['#dc3545', '#28a745'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Doctor Stats Horizontal Bar
            const ctxDoc = document.getElementById('doctorChart').getContext('2d');
            new Chart(ctxDoc, {
                type: 'bar', // Using horizontal bar format
                data: {
                    labels: {!! json_encode($doctorStats['labels']) !!},
                    datasets: [{
                        label: 'Total Patients Treated',
                        data: {!! json_encode($doctorStats['data']) !!},
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', // Makes it horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin>
