<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discharge Certificate - {{ $ipd->patient->first_name }} {{ $ipd->patient->last_name }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; color: #333; }
        .certificate-container { max-width: 800px; margin: 0 auto; padding: 40px; border: 2px solid #233446; border-radius: 8px; position: relative; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #233446; padding-bottom: 20px; margin-bottom: 30px; }
        .hospital-info h1 { margin: 0 0 5px 0; color: #1e3a8a; }
        .hospital-info p { margin: 0; color: #64748b; font-size: 14px; }
        .cert-title { text-align: right; }
        .cert-title h2 { margin: 0; color: #233446; text-transform: uppercase; letter-spacing: 2px; }
        .cert-title p { margin: 5px 0 0 0; color: #64748b; }
        .row { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .col { flex: 1; }
        .label { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .value { font-size: 16px; margin: 0; font-weight: 500; }
        .section-title { font-size: 18px; color: #1e3a8a; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px; margin: 30px 0 15px 0; }
        .content-box { background: #f8fafc; padding: 20px; border-radius: 4px; border: 1px solid #e2e8f0; min-height: 150px; white-space: pre-wrap; line-height: 1.6; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 250px; }
        .signature-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 10px; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
            .certificate-container { border: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px; max-width: 800px; margin-left: auto; margin-right: auto;">
        <a href="{{ route('ipd.index') }}" style="text-decoration: none; padding: 8px 16px; background: #64748b; color: white; border-radius: 4px; margin-right: 10px;">Back to IPD</a>
        <button onclick="window.print()" style="padding: 8px 16px; background: #1e3a8a; color: white; border: none; border-radius: 4px; cursor: pointer;">Print Certificate</button>
    </div>

    <div class="certificate-container">
        <div class="header">
            <div class="hospital-info">
                <h1>Medi_Flow Hospital</h1>
                <p>123 Health Avenue, Medical District</p>
                <p>Contact: +1 (555) 123-4567 | info@mediflow.com</p>
            </div>
            <div class="cert-title">
                <h2>Discharge Summary</h2>
                <p>Date: {{ date('d M Y') }}</p>
                <p>Admission No: #{{ str_pad($ipd->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="label">Patient Name</div>
                <div class="value">{{ $ipd->patient->first_name }} {{ $ipd->patient->last_name }}</div>
            </div>
            <div class="col">
                <div class="label">Age / Gender</div>
                <div class="value">{{ \Carbon\Carbon::parse($ipd->patient->dob)->age }} / {{ $ipd->patient->gender }}</div>
            </div>
            <div class="col">
                <div class="label">Primary Physician</div>
                <div class="value">Dr. {{ $ipd->doctor->user->name }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="label">Admission Date</div>
                <div class="value">{{ \Carbon\Carbon::parse($ipd->admission_date)->format('d M Y') }}</div>
            </div>
            <div class="col">
                <div class="label">Discharge Date</div>
                <div class="value">{{ \Carbon\Carbon::parse($ipd->discharge_date)->format('d M Y') }}</div>
            </div>
            <div class="col">
                <div class="label">Ward / Bed</div>
                <div class="value">{{ $ipd->ward_type }} ({{ $ipd->bed_number }})</div>
            </div>
        </div>

        <div class="section-title">Final Diagnosis</div>
        <p>{{ $ipd->diagnosis ?: 'As per clinical summary below.' }}</p>

        <div class="section-title">Clinical Discharge Summary</div>
        <div class="content-box">{{ $ipd->discharge_summary }}</div>

        <div class="footer">
            <div class="signature-box" style="visibility: hidden;">
                <!-- Placeholder for balance -->
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="value">Dr. {{ $ipd->doctor->user->name }}</div>
                    <div class="label">Consulting Physician</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
