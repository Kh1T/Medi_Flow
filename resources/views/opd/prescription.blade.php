<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - OPD #{{ $visit->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; background: #fff; }
        .prescription-container { max-width: 800px; margin: 40px auto; padding: 40px; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .header { border-bottom: 2px solid #0d6efd; padding-bottom: 20px; align-items: center; }
        .clinic-name { color: #0d6efd; font-weight: bold; margin-bottom: 5px; }
        .rx-symbol { font-size: 4rem; color: #ced4da; font-family: 'Times New Roman', serif; line-height: 1; }
        .patient-info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; border: 1px solid #e9ecef; }
        .section-title { font-weight: bold; color: #495057; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px; margin-top: 30px; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .footer { margin-top: 60px; padding-top: 20px; border-top: 1px dashed #ccc; font-size: 0.85rem; color: #6c757d; }
        .signature-line { border-top: 1px solid #000; width: 200px; margin-top: 60px; text-align: center; padding-top: 5px; }
        @media print {
            body { background: #fff; margin: 0; padding: 0; }
            .prescription-container { border: none; box-shadow: none; margin: 0; padding: 0; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container pb-5">
        <div class="text-end mb-3 no-print mt-3">
            <button onclick="window.print()" class="btn btn-primary"><i class="mdi mdi-printer"></i> Print Prescription</button>
            <a href="{{ route('opd.index') }}" class="btn btn-secondary">Back to OPD List</a>
        </div>

        <div class="prescription-container bg-white">
            <div class="row header">
                <div class="col-8">
                    <h2 class="clinic-name h3">MediFlow Clinic</h2>
                    <p class="mb-0 text-muted small">123 Healthcare Avenue, Medical District</p>
                    <p class="mb-0 text-muted small">Phone: +855 23 456 789 | Email: contact@mediflow.com</p>
                </div>
                <div class="col-4 text-end">
                    <h5 class="mb-0">Dr. {{ $visit->doctor->user->name }}</h5>
                    <p class="mb-0 text-muted small">{{ $visit->doctor->specialization }}</p>
                    <p class="mb-0 text-muted small">{{ $visit->doctor->qualification ?? 'Medical Professional' }}</p>
                    <p class="mb-0 text-muted small">No: {{ $visit->doctor->license_number }}</p>
                </div>
            </div>

            <div class="row patient-info mt-4">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Patient Name:</strong> {{ $visit->patient->first_name }} {{ $visit->patient->last_name }}</p>
                    <p class="mb-1"><strong>Age/Gender:</strong> {{ \Carbon\Carbon::parse($visit->patient->dob)->age }} Yrs / {{ $visit->patient->gender }}</p>
                    <p class="mb-0"><strong>Phone:</strong> {{ $visit->patient->phone }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-1"><strong>Date:</strong> {{ $visit->visit_date->format('d M Y') }}</p>
                    <p class="mb-1"><strong>Visit ID:</strong> #OPD-{{ str_pad($visit->id, 5, '0', STR_PAD_LEFT) }}</p>
                    <p class="mb-0"><strong>Token No:</strong> {{ $visit->token_number ? 'T-'.$visit->token_number : 'N/A' }}</p>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-2">
                    <div class="rx-symbol">Rx</div>
                </div>
                <div class="col-10 position-relative">
                    <div class="section-title mt-0">Clinical Notes</div>
                    <div class="mb-4">
                        <h6><strong>Symptoms:</strong></h6>
                        <p class="text-muted">{{ $visit->symptoms ?: 'None recorded' }}</p>
                    </div>

                    <div class="mb-4">
                        <h6><strong>Diagnosis:</strong></h6>
                        <p class="text-muted">{{ $visit->diagnosis ?: 'Pending evaluation' }}</p>
                    </div>

                    <div class="section-title">Prescribed Medication</div>
                    <!-- Placeholder logic for future prescription items extension -->
                    <div style="min-height: 200px;">
                        <table class="table table-borderless table-sm">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-muted fst-italic py-4">No medicines prescribed in this digital record yet. Please refer to written notes if applicable.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-5 pt-3">
                <div class="col-6">
                    <p class="text-muted small">Next Visit: <strong>{{ $visit->visit_type == 'New' ? 'After 7 Days (Optional)' : 'As needed' }}</strong></p>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <div class="signature-line">
                        <strong>Signature</strong><br>
                        Dr. {{ $visit->doctor->user->name }}
                    </div>
                </div>
            </div>

            <div class="row footer text-center">
                <div class="col-12">
                    <p class="mb-0">Keep this document safe for future references. Not a valid medico-legal document without signature.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
