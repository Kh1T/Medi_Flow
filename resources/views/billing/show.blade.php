<x-admin title="Invoice Details">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h4 class="card-title mb-1">Invoice {{ $bill->invoice_number }}</h4>
                            <p class="text-muted mb-0">Created {{ $bill->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <span class="badge @if($bill->status == 'paid') badge-success @elseif($bill->status == 'pending') badge-warning @else badge-danger @endif" style="font-size: 0.9rem;">
                            {{ ucfirst($bill->status) }}
                        </span>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-12 col-sm-6">
                            <h6 class="text-muted">Billed To</h6>
                            <p class="mb-1"><strong>{{ $bill->patient->user->name }}</strong></p>
                            <p class="mb-1">{{ $bill->patient->email }}</p>
                            <p class="mb-0">{{ $bill->patient->phone }}</p>
                        </div>
                        <div class="col-12 col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <h6 class="text-muted">Invoice Info</h6>
                            <p class="mb-1"><strong>Invoice #:</strong> {{ $bill->invoice_number }}</p>
                            <p class="mb-1"><strong>Due Date:</strong> {{ $bill->due_date }}</p>
                            @if($bill->appointment)
                                <p class="mb-0"><strong>Appointment:</strong> {{ $bill->appointment->appointment_date }}</p>
                            @endif
                        </div>
                    </div>

                    @if($bill->appointment && $bill->appointment->doctor)
                    <div class="mb-4">
                        <h6 class="text-muted">Attending Doctor</h6>
                        <p class="mb-0">Dr. {{ $bill->appointment->doctor->user->name }} — {{ $bill->appointment->doctor->specialization }}</p>
                    </div>
                    @endif

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Service Charges</strong></td>
                                    <td class="text-end">${{ number_format($bill->charges, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Less: Contractual Adjustments</td>
                                    <td class="text-end text-danger">- ${{ number_format($bill->contractual_adjustments, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Less: Insurance Payments</td>
                                    <td class="text-end text-success">- ${{ number_format($bill->insurance_coverage, 2) }}</td>
                                </tr>
                                @if($bill->insurance_company)
                                <tr>
                                    <td colspan="2" class="text-muted"><small><em>Insurance: {{ $bill->insurance_company }}</em></small></td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold table-primary">
                                    <td><strong>Patient Responsibility (Total Due)</strong></td>
                                    <td class="text-end">${{ number_format($bill->patient_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Paid Amount</td>
                                    <td class="text-end text-success">${{ number_format($bill->paid_amount, 2) }}</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Balance Due</td>
                                    <td class="text-end text-danger">${{ number_format($bill->patient_amount - $bill->paid_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between no-print">
                        <a href="{{ route('billing.index') }}" class="btn btn-secondary">Back to Invoices</a>
                        <button onclick="window.print()" class="btn btn-info"><i class="mdi mdi-printer"></i> Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
