<x-admin title="Generate Invoice">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="card-title">Create New Invoice</h4>
                            <p class="card-description text-muted mb-0">Generate billing for OPD visits or IPD admissions</p>
                        </div>
                    </div>

                    <form id="billing-form" action="{{ route('billing.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="patient_id" id="selected-patient-id">
                        <input type="hidden" name="opd_visit_id" id="selected-opd-visit-id">
                        <input type="hidden" name="ipd_admission_id" id="selected-ipd-admission-id">

                        <div class="row g-4">
                            <!-- Left Column - Patient & Visit Selection -->
                            <div class="col-lg-7">
                                <!-- Step 1: Patient Selection -->
                                <div class="card border">
                                    <div class="card-header bg-white py-3">
                                        <h6 class="mb-0"><i class="mdi mdi-account-search me-2"></i>Step 1: Select Patient</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Search Patient</label>
                                            <div class="search-input-wrapper">
                                                <div class="search-icon">
                                                    <i class="mdi mdi-magnify"></i>
                                                </div>
                                                <select id="patient-select" class="form-select ps-5" style="width: 100%;">
                                                    <option value="">Type patient name or phone...</option>
                                                </select>
                                            </div>
                                            <div class="search-hints mt-2">
                                                <small class="text-muted">Tip: Search by patient name, phone number, or ID</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Patient Info Card -->
                                        <div id="patient-info" class="alert alert-light border d-none">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                        <i class="mdi mdi-account text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1" id="patient-name"></h6>
                                                    <p class="mb-0 text-muted small">
                                                        <i class="mdi mdi-phone me-1"></i><span id="patient-phone"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Visit Selection -->
                                <div id="visit-section" class="card border mt-4 d-none">
                                    <div class="card-header bg-white py-3">
                                        <h6 class="mb-0"><i class="mdi mdi-clipboard-list me-2"></i>Step 2: Select Visit/Admission</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="nav nav-pills mb-3" id="billingTypeTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="opd-tab" data-bs-toggle="tab" data-bs-target="#opd-panel" type="button" role="tab">
                                                    <i class="mdi mdi-walk me-1"></i>OPD Visits
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="ipd-tab" data-bs-toggle="tab" data-bs-target="#ipd-panel" type="button" role="tab">
                                                    <i class="mdi mdi-bed me-1"></i>IPD Admissions
                                                </button>
                                            </li>
                                        </ul>
                                        
                                        <div class="tab-content" id="billingTypeTabsContent">
                                            <div class="tab-pane fade show active" id="opd-panel" role="tabpanel">
                                                <div id="opd-visits-list" class="list-group list-group-flush">
                                                    <p class="text-muted py-3 text-center mb-0">No pending OPD visits found.</p>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="ipd-panel" role="tabpanel">
                                                <div id="ipd-admissions-list" class="list-group list-group-flush">
                                                    <p class="text-muted py-3 text-center mb-0">No active IPD admissions found.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Billing Details -->
                            <div class="col-lg-5">
                                <div id="billing-form-section" class="card border d-none">
                                    <div class="card-header bg-white py-3">
                                        <h6 class="mb-0"><i class="mdi mdi-receipt me-2"></i>Step 3: Billing Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- OPD Billing Fields -->
                                        <div id="opd-billing-fields" class="d-none">
                                            <div class="mb-3">
                                                <label class="form-label">Consultation Fee</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control billing-calc" id="consultation_fee" name="consultation_fee" value="0">
                                                </div>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Lab Charges</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" step="0.01" class="form-control billing-calc" id="lab_charges" name="lab_charges" value="0">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Medicine Cost</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" step="0.01" class="form-control billing-calc" id="medicine_cost" name="medicine_cost" value="0">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Procedure Charges</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control billing-calc" id="procedure_charges" name="procedure_charges" value="0">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- IPD Billing Fields -->
                                        <div id="ipd-billing-fields" class="d-none">
                                            <div class="mb-3">
                                                <label class="form-label">Total IPD Charges</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" id="ipd_charges" name="charges" value="0">
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <!-- Discount Section -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-6">
                                                <label class="form-label">Discount</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control billing-calc" id="discount" name="discount" value="0">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Due Date</label>
                                                <input type="date" class="form-control" name="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                                            </div>
                                        </div>

                                        <!-- Insurance Toggle -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="apply_insurance" name="apply_insurance" value="1">
                                            <label class="form-check-label" for="apply_insurance">Apply Insurance Coverage</label>
                                        </div>

                                        <div id="insurance-info" class="alert alert-warning d-none mb-0">
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-shield-check me-2"></i>
                                                <div>
                                                    <small><strong id="insurance-provider"></strong></small><br>
                                                    <small><span id="insurance-coverage"></span>% Coverage</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Invoice Summary Card -->
                                <div id="summary-card" class="card border mt-4 d-none">
                                    <div class="card-header bg-primary text-white py-3">
                                        <h6 class="mb-0"><i class="mdi mdi-calculator me-2"></i>Invoice Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Subtotal</span>
                                            <span>$<span id="summary-subtotal">0.00</span></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Discount</span>
                                            <span class="text-danger">-$<span id="summary-discount">0.00</span></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Insurance</span>
                                            <span class="text-success">-$<span id="summary-insurance">0.00</span></span>
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-0">Total Amount</h5>
                                            <h5 class="mb-0 text-primary">$<span id="summary-total">0.00</span></h5>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <div class="alert alert-info mb-0 py-2">
                                            <small><i class="mdi mdi-information me-1"></i>Payment will be processed separately after invoice creation.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2 mt-4" id="action-buttons" style="display: none !important;">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="mdi mdi-content-save me-2"></i>Create Invoice
                                    </button>
                                    <a href="{{ route('billing.index') }}" class="btn btn-light">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .search-input-wrapper {
            position: relative;
        }
        .search-input-wrapper .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
            pointer-events: none;
        }
        .search-input-wrapper .form-select {
            padding-left: 42px !important;
        }
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            padding-left: 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-container--default .select2-selection--single:focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }
        .select2-dropdown {
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }
        .select2-results__option {
            padding: 10px 12px !important;
        }
        .select2-results__option--highlighted {
            background-color: #e7f1ff !important;
            color: #0d6efd !important;
        }
    </style>
    @endpush

    @push('scripts')
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
                placeholder: 'Search patient by name or phone...',
                allowClear: true
            });

            // Handle patient selection
            $('#patient-select').on('change', function() {
                var patientId = $(this).val();
                if (patientId) {
                    loadPatientData(patientId);
                } else {
                    resetForm();
                }
            });

            function resetForm() {
                $('#patient-info').addClass('d-none');
                $('#visit-section').addClass('d-none');
                $('#billing-form-section').addClass('d-none');
                $('#summary-card').addClass('d-none');
                $('#action-buttons').hide();
            }

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
                                opdHtml += '<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3" style="cursor:pointer;">';
                                opdHtml += '<div><strong>Token #' + visit.token + '</strong><br><small class="text-muted">Dr. ' + visit.doctor + ' | ' + visit.date + '</small></div>';
                                opdHtml += '<div class="text-end"><span class="badge bg-primary rounded-pill">$' + visit.fee + '</span></div>';
                                opdHtml += '<input type="radio" class="visually-hidden" name="visit_selection" value="' + visit.id + '" data-type="opd" data-fee="' + visit.fee + '">';
                                opdHtml += '</div>';
                            });
                            opdHtml = '<div class="list-group" style="cursor:pointer;">' + opdHtml + '</div>';
                        } else {
                            opdHtml = '<p class="text-muted py-3 text-center mb-0">No pending OPD visits found.</p>';
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
                                        ipdHtml += '<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3" style="cursor:pointer;">';
                                        ipdHtml += '<div><strong>Bed #' + admission.bed + '</strong><br><small class="text-muted">Ward: ' + admission.ward + ' | ' + admission.admission_date + '</small></div>';
                                        ipdHtml += '<div class="text-end"><span class="badge bg-success rounded-pill">Active</span></div>';
                                        ipdHtml += '<input type="radio" class="visually-hidden" name="visit_selection" value="' + admission.id + '" data-type="ipd">';
                                        ipdHtml += '</div>';
                                    });
                                    ipdHtml = '<div class="list-group" style="cursor:pointer;">' + ipdHtml + '</div>';
                                } else {
                                    ipdHtml = '<p class="text-muted py-3 text-center mb-0">No active IPD admissions found.</p>';
                                }
                                $('#ipd-admissions-list').html(ipdHtml);
                            }
                        });

                        // Insurance info
                        if (response.insurance) {
                            $('#insurance-info').removeClass('d-none');
                            $('#insurance-provider').text(response.insurance.provider_name);
                            $('#insurance-coverage').text(response.insurance.coverage_percentage);
                            $('#apply_insurance').prop('disabled', false);
                        } else {
                            $('#insurance-info').addClass('d-none');
                            $('#apply_insurance').prop('disabled', true).prop('checked', false);
                        }
                    }
                });
            }

            // Handle visit/admission selection
            $(document).on('click', '#opd-visits-list .list-group-item, #ipd-admissions-list .list-group-item', function() {
                var radio = $(this).find('input[type="radio"]');
                if (radio.length) {
                    radio.prop('checked', true);
                    radio.trigger('change');
                }
            });

            $(document).on('change', 'input[name="visit_selection"]', function() {
                // Remove active state from all items
                $('.list-group-item').removeClass('active');
                // Add active state to selected item
                $(this).closest('.list-group-item').addClass('active');
                
                var type = $(this).data('type');
                var id = $(this).val();
                var fee = $(this).data('fee') || 0;

                $('#billing-form-section').removeClass('d-none');
                $('#summary-card').removeClass('d-none');
                $('#action-buttons').show();
                
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
            $(document).on('input change', '.billing-calc, #discount, #ipd_charges, #apply_insurance', function() {
                calculateTotal();
            });

            function calculateTotal() {
                var consultation = parseFloat($('#consultation_fee').val()) || 0;
                var lab = parseFloat($('#lab_charges').val()) || 0;
                var medicine = parseFloat($('#medicine_cost').val()) || 0;
                var procedure = parseFloat($('#procedure_charges').val()) || 0;
                var ipdCharges = parseFloat($('#ipd_charges').val()) || 0;
                var discount = parseFloat($('#discount').val()) || 0;

                // Check which billing type is active
                var isIpd = !$('#ipd-billing-fields').hasClass('d-none');
                
                var subtotal;
                if (isIpd) {
                    subtotal = ipdCharges;
                } else {
                    subtotal = consultation + lab + medicine + procedure;
                }
                
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
            }
        });
    </script>
    @endpush
</x-admin>
