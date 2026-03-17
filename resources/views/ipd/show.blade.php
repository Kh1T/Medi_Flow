<x-admin title="IPD Patient Dashboard - {{ $ipd->patient->first_name }}">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>IPD Patient: {{ $ipd->patient->first_name }} {{ $ipd->patient->last_name }}</h2>
                <div>
                    <a href="{{ route('ipd.index') }}" class="btn btn-light"><i class="mdi mdi-arrow-left"></i> Back to List</a>
                    @if($ipd->status == 'admitted')
                        <a href="{{ route('ipd.discharge.create', $ipd->id) }}" class="btn btn-warning"><i class="mdi mdi-exit-run"></i> Discharge Patient</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="mdi mdi-account-circle"></i> Patient Details</h5>
                    <p class="mb-1"><strong>ID:</strong> #{{ $ipd->patient->id }}</p>
                    <p class="mb-1"><strong>Age / Gender:</strong> {{ \Carbon\Carbon::parse($ipd->patient->dob)->age }} / {{ $ipd->patient->gender }}</p>
                    <p class="mb-1"><strong>Blood Group:</strong> {{ $ipd->patient->blood_group ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Contact:</strong> {{ $ipd->patient->phone }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-info"><i class="mdi mdi-hospital-building"></i> Admission Details</h5>
                    <p class="mb-1"><strong>Ward/Bed:</strong> {{ $ipd->ward_type }} ({{ $ipd->bed_number }})</p>
                    <p class="mb-1"><strong>Admission Date:</strong> {{ \Carbon\Carbon::parse($ipd->admission_date)->format('d M Y') }}</p>
                    <p class="mb-1"><strong>Type:</strong> <span class="badge badge-secondary">{{ $ipd->admission_type }}</span></p>
                    <p class="mb-0"><strong>Status:</strong> <span class="badge badge-{{ $ipd->status == 'admitted' ? 'success' : 'dark' }}">{{ ucfirst($ipd->status) }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-left-primary">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="mdi mdi-stethoscope"></i> Clinical Overview</h5>
                    <p class="mb-1"><strong>Primary Dr.:</strong> Dr. {{ $ipd->doctor->user->name }}</p>
                    <p class="mb-1"><strong>Issue:</strong> {{ Str::limit($ipd->admission_reason, 40) }}</p>
                    <p class="mb-0"><strong>Diagnosis:</strong> {{ $ipd->diagnosis ?? 'Pending Evaluation' }}</p>
                </div>
            </div>
        </div>
    </div>

    @php
        // Fetch relations manually since we did not add them to model formally yet 
        $notes = DB::table('ipd_notes')->join('users', 'ipd_notes.author_id', '=', 'users.id')->where('ipd_admission_id', $ipd->id)->select('ipd_notes.*', 'users.name as author_name')->orderByDesc('created_at')->get();
        $meds = DB::table('ipd_medications')->join('users', 'ipd_medications.administered_by', '=', 'users.id')->where('ipd_admission_id', $ipd->id)->select('ipd_medications.*', 'users.name as administered_by_name')->orderByDesc('administered_at')->get();
        $labs = DB::table('ipd_lab_requests')->where('ipd_admission_id', $ipd->id)->orderByDesc('requested_at')->get();
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="notes-tab" data-bs-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="true">Clinical Notes & Charts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="meds-tab" data-bs-toggle="tab" href="#meds" role="tab" aria-controls="meds" aria-selected="false">Medication Chart</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="labs-tab" data-bs-toggle="tab" href="#labs" role="tab" aria-controls="labs" aria-selected="false">Lab Requests</a>
                        </li>
                    </ul>

                    <div class="tab-content border border-top-0 p-3" id="myTabContent">
                        
                        <!-- NOTES TAB -->
                        <div class="tab-pane fade show active" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                            @if($ipd->status == 'admitted')
                            <form action="{{ route('ipd.notes.store', $ipd->id) }}" method="POST" class="mb-4 bg-light p-3 rounded">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>Note Type</label>
                                        <select name="note_type" class="form-control" required>
                                            <option value="nurse_chart">Nurse Chart</option>
                                            <option value="doctor_visit">Doctor Visit Note</option>
                                        </select>
                                    </div>
                                    <div class="col-md-7 form-group">
                                        <label>Notes / Observations</label>
                                        <input type="text" name="notes" class="form-control" required placeholder="Vitals, observations, updates...">
                                    </div>
                                    <div class="col-md-2 form-group d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">Add Note</button>
                                    </div>
                                </div>
                            </form>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead><tr><th>Date & Time</th><th>Type</th><th>Author</th><th>Notes</th></tr></thead>
                                    <tbody>
                                        @forelse($notes as $note)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($note->created_at)->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge badge-{{ $note->note_type == 'doctor_visit' ? 'info' : 'primary' }}">{{ str_replace('_', ' ', $note->note_type) }}</span></td>
                                            <td>{{ $note->author_name }}</td>
                                            <td>{{ $note->notes }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted">No notes recorded yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- MEDICATION TAB -->
                        <div class="tab-pane fade" id="meds" role="tabpanel" aria-labelledby="meds-tab">
                            @if($ipd->status == 'admitted')
                            <form action="{{ route('ipd.meds.store', $ipd->id) }}" method="POST" class="mb-4 bg-light p-3 rounded">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>Medicine Name</label>
                                        <input type="text" name="medicine_name" class="form-control" required placeholder="Name of medicine">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Dosage</label>
                                        <input type="text" name="dosage" class="form-control" required placeholder="e.g. 500mg, 1 tablet">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Time Administered</label>
                                        <input type="datetime-local" name="administered_at" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                                    </div>
                                    <div class="col-md-2 form-group d-flex align-items-end">
                                        <button type="submit" class="btn btn-success w-100">Log Med</button>
                                    </div>
                                </div>
                            </form>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead><tr><th>Time Administered</th><th>Medicine</th><th>Dosage</th><th>Administered By</th></tr></thead>
                                    <tbody>
                                        @forelse($meds as $med)
                                        <tr>
                                            <td><strong>{{ \Carbon\Carbon::parse($med->administered_at)->format('d/m/Y H:i') }}</strong></td>
                                            <td>{{ $med->medicine_name }}</td>
                                            <td>{{ $med->dosage }}</td>
                                            <td>{{ $med->administered_by_name }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted">No medications logged yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- LABS TAB -->
                        <div class="tab-pane fade" id="labs" role="tabpanel" aria-labelledby="labs-tab">
                            @if($ipd->status == 'admitted')
                            <form action="{{ route('ipd.labs.store', $ipd->id) }}" method="POST" class="mb-4 bg-light p-3 rounded">
                                @csrf
                                <div class="row">
                                    <div class="col-md-9 form-group">
                                        <label>Test Name</label>
                                        <input type="text" name="test_name" class="form-control" required placeholder="e.g. Complete Blood Count (CBC)">
                                    </div>
                                    <div class="col-md-3 form-group d-flex align-items-end">
                                        <button type="submit" class="btn btn-info w-100 text-white">Create Request</button>
                                    </div>
                                </div>
                            </form>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead><tr><th>Request Time</th><th>Test Name</th><th>Status</th><th>Result Notes</th></tr></thead>
                                    <tbody>
                                        @forelse($labs as $lab)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($lab->requested_at)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $lab->test_name }}</td>
                                            <td>
                                                @php $lColors = ['pending'=>'warning','completed'=>'success','cancelled'=>'danger']; @endphp
                                                <span class="badge badge-{{ $lColors[$lab->status] }}">{{ $lab->status }}</span>
                                            </td>
                                            <td>{{ $lab->result_notes ?: '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted">No lab requests generated.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
