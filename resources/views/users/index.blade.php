<x-admin title="Manage Users">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Users</h4>
                    </div>
                    <p class="card-description">Manage system users and roles</p>
                    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label for="search">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name or email...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="role">Role</label>
                                    <select class="form-control" id="role" name="role">
                                        <option value="">All Roles</option>
                                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="doctor" {{ request('role') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                                        <option value="patient" {{ request('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-sm me-2"><i class="mdi mdi-magnify"></i> Search</button>
                                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">Clear</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-opacity-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'doctor' ? 'success' : 'info') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No users found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
