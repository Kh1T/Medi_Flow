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
                        </div>
                    </div>


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
                        <div>
                            @if($bill->status != 'paid')
                                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="mdi mdi-cash"></i> Mark as Paid
                                </button>
                            @endif
                            <button onclick="window.print()" class="btn btn-info"><i class="mdi mdi-printer"></i> Print</button>
                        </div>
                    </div>

                    <!-- Payment Modal -->
                    @if($bill->status != 'paid')
                    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="paymentModalLabel">Process Payment - {{ $bill->invoice_number }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('billing.processPayment', $bill->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Payment Method</label>
                                            <select name="method" class="form-select" required>
                                                <option value="cash">Cash</option>
                                                <option value="card">Card</option>
                                                <option value="insurance">Insurance</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Amount ($)</label>
                                            <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ $bill->patient_amount - $bill->paid_amount }}" required>
                                            <small class="text-muted">Balance due: ${{ number_format($bill->patient_amount - $bill->paid_amount, 2) }}</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success">Process Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin>
