<x-admin title="Doctor Availability">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Doctor Availability Schedule</h4>
                    <p class="card-description">View and manage doctor availability slots</p>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Monday</th>
                                    <th>Tuesday</th>
                                    <th>Wednesday</th>
                                    <th>Thursday</th>
                                    <th>Friday</th>
                                    <th>Saturday</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach($doctors as $doctor)
                                <tr>
                                    <td>Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}</td>
                                    <td>{{ $doctor->availability['monday'] ?? '-' }}</td>
                                    <td>{{ $doctor->availability['tuesday'] ?? '-' }}</td>
                                    <td>{{ $doctor->availability['wednesday'] ?? '-' }}</td>
                                    <td>{{ $doctor->availability['thursday'] ?? '-' }}</td>
                                    <td>{{ $doctor->availability['friday'] ?? '-' }}</td>
                                    <td>{{ $doctor->availability['saturday'] ?? '-' }}</td>
                                    <td>
                                        <a href="{{ url('/doctors/'.$doctor->id.'/edit') }}" class="btn btn-warning btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
                                    </td>
                                </tr>
                                @endforeach --}}
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No availability data yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
