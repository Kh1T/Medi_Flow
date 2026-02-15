<x-admin title="Register Doctor">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">New Doctor Registration</h4>
                    <p class="card-description">Create doctor account and professional profile</p>
                    <form class="forms-sample" action="{{ route('doctors.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Dr. Jane Smith" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="jane@clinic.com" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+855</span>
                                        <input type="tel" class="form-control phone-mask" id="phone" name="phone" value="{{ old('phone') }}" placeholder="97 123 4567">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="qualification">Qualification</label>
                                    <input type="text" class="form-control" id="qualification" name="qualification" value="{{ old('qualification') }}" placeholder="e.g. MBBS, MD, PhD">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="specialization">Specialization</label>
                                    <input type="text" class="form-control @error('specialization') is-invalid @enderror" id="specialization" name="specialization" value="{{ old('specialization') }}" placeholder="e.g. Cardiology" required>
                                    @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="license_number">Medical License Number</label>
                                    <input type="text" class="form-control @error('license_number') is-invalid @enderror" id="license_number" name="license_number" value="{{ old('license_number') }}" placeholder="ML-12345" required>
                                    @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="experience_years">Years of Experience</label>
                                    <input type="number" class="form-control @error('experience_years') is-invalid @enderror" id="experience_years" name="experience_years" value="{{ old('experience_years') }}" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="consultation_fee">Consultation Fee ($)</label>
                                    <input type="number" step="0.01" class="form-control @error('consultation_fee') is-invalid @enderror" id="consultation_fee" name="consultation_fee" value="{{ old('consultation_fee') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-muted small">
                            <p>An account will be created automatically. Default password is the doctor's phone number or 'doctor123'.</p>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Register Doctor</button>
                        <a href="{{ route('doctors.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.phone-mask').forEach(function(input) {
            input.addEventListener('input', function(e) {
                let val = e.target.value.replace(/\D/g, '');
                if (val.length > 9) val = val.substring(0, 9);
                if (val.length >= 5) {
                    e.target.value = val.substring(0, 2) + ' ' + val.substring(2, 5) + ' ' + val.substring(5);
                } else if (val.length >= 2) {
                    e.target.value = val.substring(0, 2) + ' ' + val.substring(2);
                } else {
                    e.target.value = val;
                }
            });
        });
    </script>
    @endpush
</x-admin>
