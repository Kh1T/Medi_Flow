<x-admin title="Generate Invoice">
    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">New Billing Entry</h4>
                    <form class="forms-sample" action="{{ route('billing.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="patient_id">Patient</label>
                            <select class="form-control" name="patient_id" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="appointment_id">Related Appointment (Optional)</label>
                            <select class="form-control" name="appointment_id">
                                <option value="">No Appointment Linked</option>
                                @foreach($appointments as $appointment)
                                    <option value="{{ $appointment->id }}">
                                        {{ $appointment->patient->user->name }} - {{ $appointment->appointment_date }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="charges">Service Charges ($)</label>
                            <input type="number" step="0.01" class="form-control" id="charges" name="charges" placeholder="0.00" required>
                            <small class="text-muted">Base charges for medical services</small>
                        </div>

                        <div class="form-group">
                            <label for="contractual_adjustments">Contractual Adjustments ($)</label>
                            <input type="number" step="0.01" class="form-control" id="contractual_adjustments" name="contractual_adjustments" placeholder="0.00" value="0">
                            <small class="text-muted">Discounts or negotiated reductions</small>
                        </div>

                        <div class="form-group">
                            <label for="insurance_coverage">Insurance Payments ($)</label>
                            <input type="number" step="0.01" class="form-control" id="insurance_coverage" name="insurance_coverage" placeholder="0.00" value="0">
                            <small class="text-muted">Amount covered by insurance</small>
                        </div>

                        <div class="form-group">
                            <label for="insurance_company">Insurance Company (Optional)</label>
                            <input type="text" class="form-control" id="insurance_company" name="insurance_company" placeholder="e.g., Blue Cross">
                        </div>

                        <div class="alert alert-info">
                            <strong>Calculation:</strong> Patient Responsibility = Charges - Contractual Adjustments - Insurance Payments
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Due Date</label>
                                    <input type="date" class="form-control" name="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="overdue">Overdue</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary me-2">Create Invoice</button>
                        <a href="{{ route('billing.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
