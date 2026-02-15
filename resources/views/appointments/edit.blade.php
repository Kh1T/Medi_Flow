<x-admin title="Update Appointment">
    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Reschedule / Update Status</h4>
                    <p class="card-description">Modify appointment for {{ $appointment->patient->user->name }}</p>
                    <form class="forms-sample" action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group text-muted">
                            <label>Doctor: Dr. {{ $appointment->doctor->user->name }}</label>
                        </div>
                        <div class="form-group">
                            <label for="status">Appointment Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Scheduled" {{ $appointment->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="Completed" {{ $appointment->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Cancelled" {{ $appointment->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="No Show" {{ $appointment->status == 'No Show' ? 'selected' : '' }}>No Show</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_date">Date</label>
                                    <input type="date" class="form-control @error('appointment_date') is-invalid @enderror" id="appointment_date" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_time">Time</label>
                                    <input type="time" class="form-control @error('appointment_time') is-invalid @enderror" id="appointment_time" name="appointment_time" value="{{ old('appointment_time', $appointment->appointment_time) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reason">Notes / Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3">{{ old('reason', $appointment->reason) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Update Appointment</button>
                        <a href="{{ route('appointments.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
