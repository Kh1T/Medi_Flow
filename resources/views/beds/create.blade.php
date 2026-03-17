<x-admin title="Add New Bed">
    <div class="row justify-content-center">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add New Bed</h4>
                    <p class="card-description">Create a new hospital bed capacity manually.</p>
                    
                    <form class="forms-sample" action="{{ route('beds.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="ward_type">Ward Type</label>
                                <select class="form-control @error('ward_type') is-invalid @enderror" id="ward_type" name="ward_type" required>
                                    <option value="">Select Ward</option>
                                    <option value="General" {{ old('ward_type') == 'General' ? 'selected' : '' }}>General Ward</option>
                                    <option value="Private" {{ old('ward_type') == 'Private' ? 'selected' : '' }}>Private Ward</option>
                                    <option value="ICU" {{ old('ward_type') == 'ICU' ? 'selected' : '' }}>ICU</option>
                                </select>
                                @error('ward_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label for="floor">Floor / Level</label>
                                <input type="text" class="form-control @error('floor') is-invalid @enderror" id="floor" name="floor" value="{{ old('floor', '1st Floor') }}" required>
                                @error('floor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bed_number">Bed Number / Identifier</label>
                            <input type="text" class="form-control @error('bed_number') is-invalid @enderror" id="bed_number" name="bed_number" placeholder="e.g., GEN-01, ICU-05" value="{{ old('bed_number') }}" required>
                            @error('bed_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary me-2">Save Bed</button>
                        <a href="{{ route('beds.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
