<x-admin title="OPD Visit Details">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">OPD Visit: #{{ $visit->id }}</h4>
                        <div>
                            <a href="{{ route('opd.edit', $visit->id) }}" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
                            <a href="{{ route('opd.index') }}" class="btn btn-light btn-sm">Back</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="text-primary border-bottom pb-2">Patient Information</h5>
                            <p><strong>Name:</strong> {{ $visit->patient->first_name }} {{ $visit->patient->last_name }}</p>
                            <p><strong>Phone:</strong> {{ $visit->patient->phone }}</p>
                            <p><strong>DOB:</strong> {{ \Carbon\Carbon::parse($visit->patient->dob)->format('d M Y') }} ({{ \Carbon\Carbon::parse($visit->patient->dob)->age }} years)</p>
                            <p><strong>Blood Group:</strong> {{ $visit->patient->blood_group ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="text-primary border-bottom pb-2">Doctor Information</h5>
                            <p><strong>Assigned Doctor:</strong> Dr. {{ $visit->doctor->user->name }}</p>
                            <p><strong>Specialization:</strong> {{ $visit->doctor->specialization }}</p>
                            <p><strong>Contact:</strong> {{ $visit->doctor->user->phone ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <h5 class="text-primary border-bottom pb-2">Visit Details</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <p><strong>Date:</strong><br/> {{ $visit->visit_date->format('d M Y') }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Visit Type:</strong><br/> <span class="badge badge-{{ $visit->visit_type == 'New' ? 'primary' : 'info' }}">{{ $visit->visit_type }}</span></p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Status:</strong><br/> 
                                        @php
                                            $statusClass = [
                                                'scheduled' => 'warning',
                                                'in_progress' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ][$visit->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">{{ ucfirst($visit->status) }}</span>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Payment:</strong><br/> 
                                        <span class="badge badge-{{ $visit->payment_status == 'Paid' ? 'success' : ($visit->payment_status == 'Unpaid' ? 'danger' : 'warning') }}">{{ $visit->payment_status }}</span>
                                        (${{ number_format($visit->fee, 2) }})
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <h5 class="text-primary border-bottom pb-2">Clinical Notes</h5>
                            <div class="card bg-light">
                                <div class="card-body py-3">
                                    <h6>Reported Symptoms:</h6>
                                    <p class="mb-4">{{ $visit->symptoms ?: 'No symptoms recorded.' }}</p>
                                    
                                    <h6>Diagnosis / Doctor Notes:</h6>
                                    <p class="mb-0">{{ $visit->diagnosis ?: 'No diagnosis recorded yet.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($visit->prescriptions && $visit->prescriptions->isNotEmpty())
                    <div class="row">
                        <div class="col-12 mb-4">
                            <h5 class="text-primary border-bottom pb-2">Prescriptions</h5>
                            @foreach($visit->prescriptions as $prescription)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong>Prescription #{{ $prescription->id }}</strong>
                                    <span class="text-muted ms-2">Created: {{ $prescription->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="card-body py-2">
                                    @if($prescription->prescriptionItems && $prescription->prescriptionItems->isNotEmpty())
                                    <table class="table table-sm table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Medicine</th>
                                                <th>Dosage</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($prescription->prescriptionItems as $item)
                                            <tr>
                                                <td>{{ $item->medicine_name }}</td>
                                                <td>{{ $item->dosage ?? 'N/A' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->price, 2) }}</td>
                                                <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <td colspan="4" class="text-end"><strong>Prescription Total:</strong></td>
                                                <td><strong>${{ number_format($prescription->getTotalCost(), 2) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    @else
                                    <p class="text-muted mb-0">No items in this prescription.</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-admin>
