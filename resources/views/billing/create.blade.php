<x-admin title="Generate Invoice">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">New Billing Entry</h4>
                    
                    <!-- Step 1: Patient Selection -->
                    <div class="mb-4">
                        <label class="form-label">Select Patient</label>
                        <select id="patient-select" class="form-select" style="width: 100%;">
                            <option value="">Search patient by name or phone...</option>
                        </select>
                    </div>

                    <!-- Patient Info Display -->
                    <div id="patient-info" class="alert alert-info d-none">
                        <h5>Patient: <span id="patient-name"></span></h5>
                        <p class="mb-0">Phone: <span id="patient-phone"></span></p>
                    </div>

                    <!-- Step 2: Visit/Admission Selection -->
                    <div id="visit-section" class="d-none">
                        <hr>
                        <h5 class="mb-3">Select Visit/Admission</h5>
                        
                        <ul class="nav nav-tabs" id="billingTypeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="opd-tab" data-bs-toggle="tab" data-bs-target="#opd-panel" type="button" role="tab">
                                    OPD Visits
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ipd-tab" data-bs-toggle="tab" data-bs-target="#ipd-panel" type="button" role="tab">
                                    IPD Admissions
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content mt-3" id="billingTypeTabsContent">
                            <!-- OPD Panel -->
                            <div class="tab-pane fade show active" id="opd-panel" role="tabpanel">
                                <div id="opd-visits-list">
                                    <p class="text-muted">No pending OPD visits found.</p>
                                </div>
                            </div>
                            <!-- IPD Panel -->
                            <div class="tab-pane fade" id="ipd-panel" role="tabpanel">
                                <div id="ipd-admissions-list">
                                    <p class="text-muted">No active IPD admissions found.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Billing Details Form -->
                    <div id="billing-form-section" class="d-none">
                        <hr>
                        <h5 class="mb-3">Billing Details</h5>
                        
                        <form class="forms-sample" id="billing-form" action="{{ route('billing.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="patient_id" id="selected-patient-id">
                            <input type="hidden" name="opd_visit_id" id="selected-opd-visit-id">
                            <input type="hidden" name="ipd_admission_id" id="selected-ipd-admission-id">
                            
                            <!-- OPD Billing Fields -->
                            <div id="opd-billing-fields" class="d-none">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_fee">Consultation Fee ($)</label>
                                            <input type="number" step="0.01" class="form-control billing-calc" id="consultation_fee" name="consultation_fee" placeholder="0.00" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lab_charges">Lab Charges ($)</label>
                                            <input type="number" step="0.01" class="form-control billing-calc" id="lab_charges" name="lab_charges" placeholder="0.00" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="medicine_cost">Medicine Cost ($)</label>
                                            <input type="number" step="0.01" class="form-control billing-calc" id="medicine_cost" name="medicine_cost" placeholder="0.00" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="procedure_charges">Procedure Charges ($)</label>
                                            <input type="number" step="0.01" class="form-control billing-calc" id="procedure_charges" name="procedure_charges" placeholder="0.00" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- IPD Billing Fields -->
                            <div id="ipd-billing-fields" class="d-none">
                                <div class="form-group">
                                    <label for="ipd_charges">Total IPD Charges ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="ipd_charges" name="charges" placeholder="0.00" value="0">
                                </div>
                            </div>

                            <!-- Discount and Insurance -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discount">Discount ($)</label>
                                        <input type="number" step="0.01" class="form-control billing-calc" id="discount" name="discount" placeholder="0.00" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="apply_insurance">
                                            <input type="checkbox" id="apply_insurance" name="apply_insurance" value="1"> 
                                            Apply Insurance
                                        </label>
                                        <div id="insurance-info" class="alert alert-warning d-none mt-2">
                                            <small>Insurance: <strong id="insurance-provider"></strong> (<span id="insurance-coverage"></span>%)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="alert alert-info mt-3">
                                <div class="row">
                                    <div class="col-md-3">Subtotal: $<span id="summary-subtotal">0.00</span></div>
                                    <div class="col-md-3">Discount: -$<span id="summary-discount">0.00</span></div>
                                    <div class="col-md-3">Insurance: -$<span id="summary-insurance">0.00</span></div>
                                    <div class="col-md-3"><strong>Total: $<span id="summary-total">0.00</span></strong></div>
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label>
                                        <input type="date" class="form-control" name="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Note about payment -->
                            <div class="alert alert-info mt-3">
                                <strong>Note:</strong> Payment will be processed separately using "Mark as Paid" button after invoice creation.
                            </div>

                            <button type="submit" class="btn btn-primary me-2">Create Invoice</button>
                            <a href="{{ route('billing.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize patient select2
            $('#patient-select').select2({
                ajax: {
                    url: '{{ route("billing.patients.search") }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.map(function(patient) {
                                return {
                                    id: patient.id,
                                    text: patient.text
                                };
                            })
                        };
                    }
                },
                minimumInputLength: 1,
                placeholder: 'Search patient by name or phone...'
            });

            // Handle patient selection
            $('#patient-select').on('change', function() {
                var patientId = $(this).val();
                if (patientId) {
                    loadPatientData(patientId);
                } else {
                    $('#patient-info').addClass('d-none');
                    $('#visit-section').addClass('d-none');
                    $('#billing-form-section').addClass('d-none');
                }
            });

            function loadPatientData(patientId) {
                $.ajax({
                    url: '{{ route("billing.patient.data") }}',
                    data: { patient_id: patientId, type: 'opd' },
                    success: function(response) {
                        // Show patient info
                        $('#patient-info').removeClass('d-none');
                        $('#patient-name').text(response.patient.name);
                        $('#patient-phone').text(response.patient.phone);
                        $('#selected-patient-id').val(patientId);

                        // Show visits/admissions
                        $('#visit-section').removeClass('d-none');
                        
                        // OPD Visits
                        var opdHtml = '';
                        if (response.visits && response.visits.length > 0) {
                            response.visits.forEach(function(visit) {
                                opdHtml += '<div class="form-check mb-2">';
                                opdHtml += '<input class="form-check-input" type="radio" name="visit_selection" id="visit-' + visit.id + '" value="' + visit.id + '" data-type="opd" data-fee="' + visit.fee + '">';
                                opdHtml += '<label class="form-check-label" for="visit-' + visit.id + '">';
                                opdHtml += '<strong>Token #' + visit.token + '</strong> - ' + visit.date + ' | Dr. ' + visit.doctor + ' | Fee: $' + visit.fee;
                                opdHtml += '</label></div>';
                            });
                        } else {
                            opdHtml = '<p class="text-muted">No pending OPD visits found.</p>';
                        }
                        $('#opd-visits-list').html(opdHtml);

                        // IPD Admissions
                        $.ajax({
                            url: '{{ route("billing.patient.data") }}',
                            data: { patient_id: patientId, type: 'ipd' },
                            success: function(ipdResponse) {
                                var ipdHtml = '';
                                if (ipdResponse.admissions && ipdResponse.admissions.length > 0) {
                                    ipdResponse.admissions.forEach(function(admission) {
                                        ipdHtml += '<div class="form-check mb-2">';
                                        ipdHtml += '<input class="form-check-input" type="radio" name="visit_selection" id="admission-' + admission.id + '" value="' + admission.id + '" data-type="ipd">';
                                        ipdHtml += '<label class="form-check-label" for="admission-' + admission.id + '">';
                                        ipdHtml += '<strong>Bed #' + admission.bed + '</strong> - ' + admission.admission_date + ' | Ward: ' + admission.ward;
                                        ipdHtml += '</label></div>';
                                    });
                                } else {
                                    ipdHtml = '<p class="text-muted">No active IPD admissions found.</p>';
                                }
                                $('#ipd-admissions-list').html(ipdHtml);
                            }
                        });

                        // Insurance info
                        if (response.insurance) {
                            $('#insurance-info').removeClass('d-none');
                            $('#insurance-provider').text(response.insurance.provider_name);
                            $('#insurance-coverage').text(response.insurance.coverage_percentage);
                        } else {
                            $('#insurance-info').addClass('d-none');
                            $('#apply_insurance').prop('disabled', true);
                        }
                    }
                });
            }

            // Handle visit/admission selection
            $(document).on('change', 'input[name="visit_selection"]', function() {
                var type = $(this).data('type');
                var id = $(this).val();
                var fee = $(this).data('fee') || 0;

                $('#billing-form-section').removeClass('d-none');
                
                if (type === 'opd') {
                    $('#selected-opd-visit-id').val(id);
                    $('#selected-ipd-admission-id').val('');
                    $('#opd-billing-fields').removeClass('d-none');
                    $('#ipd-billing-fields').addClass('d-none');
                    $('#consultation_fee').val(fee);
                } else {
                    $('#selected-opd-visit-id').val('');
                    $('#selected-ipd-admission-id').val(id);
                    $('#opd-billing-fields').addClass('d-none');
                    $('#ipd-billing-fields').removeClass('d-none');
                }
                
                calculateTotal();
            });

            // Handle billing calculation
            $('.billing-calc, #discount, #apply_insurance').on('input change', function() {
                calculateTotal();
            });

            function calculateTotal() {
                var consultation = parseFloat($('#consultation_fee').val()) || 0;
                var lab = parseFloat($('#lab_charges').val()) || 0;
                var medicine = parseFloat($('#medicine_cost').val()) || 0;
                var procedure = parseFloat($('#procedure_charges').val()) || 0;
                var discount = parseFloat($('#discount').val()) || 0;

                var subtotal = consultation + lab + medicine + procedure;
                var afterDiscount = subtotal - discount;

                $('#summary-subtotal').text(subtotal.toFixed(2));
                $('#summary-discount').text(discount.toFixed(2));

                var insuranceCoverage = 0;
                if ($('#apply_insurance').is(':checked')) {
                    var coveragePercent = parseFloat($('#insurance-coverage').text()) || 0;
                    insuranceCoverage = (afterDiscount * coveragePercent) / 100;
                }

                var total = Math.max(0, afterDiscount - insuranceCoverage);
                
                $('#summary-insurance').text(insuranceCoverage.toFixed(2));
                $('#summary-total').text(total.toFixed(2));
                
                // Auto-fill paid amount if full payment
                $('#paid_amount').val(total.toFixed(2));
            }
        });
    </script>
    @endpush
</x-admin>
