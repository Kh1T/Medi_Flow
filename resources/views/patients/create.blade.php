<x-admin title="Register Patient">
    <div class="row">
        <div class="col-12 col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">New Patient Registration</h4>
                    <p class="card-description">Create both user account and patient profile</p>
                    <form class="forms-sample" action="{{ route('patients.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="John" required>
                                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Doe" required>
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+855</span>
                                        <input type="tel" class="form-control phone-mask @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="97 123 4567" required>
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob" name="dob" value="{{ old('dob') }}" required>
                                    @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label for="blood_group">Blood Group</label>
                                    <select class="form-control @error('blood_group') is-invalid @enderror" id="blood_group" name="blood_group">
                                        <option value="">Unknown</option>
                                        <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="emergency_contact">Emergency Contact Name</label>
                                    <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="emergency_phone">Emergency Contact Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+855</span>
                                        <input type="tel" class="form-control phone-mask" id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone') }}" placeholder="97 123 4567">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Residential Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mt-3">
                            <p class="text-muted small">Default password will be the patient's phone number or 'password123'.</p>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Register Patient</button>
                        <a href="{{ route('patients.index') }}" class="btn btn-light">Cancel</a>
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
