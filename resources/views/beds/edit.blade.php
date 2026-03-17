<x-admin title="Edit Bed">
    <div class="row justify-content-center">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Bed: {{ $bed->bed_number }}</h4>
                    
                    <form class="forms-sample" action="{{ route('beds.update', $bed->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="ward_type">Ward Type</label>
                                <select class="form-control @error('ward_type') is-invalid @enderror" id="ward_type" name="ward_type" required>
                                    <option value="General" {{ old('ward_type', $bed->ward_type) == 'General' ? 'selected' : '' }}>General Ward</option>
                                    <option value="Private" {{ old('ward_type', $bed->ward_type) == 'Private' ? 'selected' : '' }}>Private Ward</option>
                                    <option value="ICU" {{ old('ward_type', $bed->ward_type) == 'ICU' ? 'selected' : '' }}>ICU</option>
                                </select>
                                @error('ward_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label for="floor">Floor / Level</label>
                                <input type="text" class="form-control @error('floor') is-invalid @enderror" id="floor" name="floor" value="{{ old('floor', $bed->floor) }}" required>
                                @error('floor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="bed_number">Bed Number / Identifier</label>
                                <input type="text" class="form-control @error('bed_number') is-invalid @enderror" id="bed_number" name="bed_number" value="{{ old('bed_number', $bed->bed_number) }}" required>
                                @error('bed_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>Occupancy Status</label>
                                <div class="form-check mt-2">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="is_occupied" value="1" {{ old('is_occupied', $bed->is_occupied) ? 'checked' : '' }}>
                                        Force Occupied Status
                                    </label>
                                    <small class="text-muted d-block mt-1">Usually managed automatically by Admissions/Discharges.</small>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary me-2">Update Bed</button>
                        <a href="{{ route('beds.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
