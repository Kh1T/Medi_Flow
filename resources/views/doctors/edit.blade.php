<x-admin title="Edit Doctor">
    <div class="row">
        <div class="col-md-10 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Doctor: {{ $doctor->user->name }}</h4>
                    <p class="card-description">Update professional profile information</p>
                    <form class="forms-sample" action="{{ route('doctors.update', $doctor->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $doctor->user->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $doctor->user->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="specialization">Specialization</label>
                                    <input type="text" class="form-control @error('specialization') is-invalid @enderror" id="specialization" name="specialization" value="{{ old('specialization', $doctor->specialization) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="license_number">License Number</label>
                                    <input type="text" class="form-control @error('license_number') is-invalid @enderror" id="license_number" name="license_number" value="{{ old('license_number', $doctor->license_number) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="experience_years">Experience (Years)</label>
                                    <input type="number" class="form-control" id="experience_years" name="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="consultation_fee">Consultation Fee ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="consultation_fee" name="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $doctor->user->phone) }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="qualification">Qualification</label>
                            <input type="text" class="form-control" id="qualification" name="qualification" value="{{ old('qualification', $doctor->qualification) }}" placeholder="e.g. MBBS, MD, PhD">
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Update Doctor</button>
                        <a href="{{ route('doctors.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin>
