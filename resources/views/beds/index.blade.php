<x-admin title="Bed Management">
    <div class="row mb-4">
        <div class="col-md-4 stretch-card grid-margin grid-margin-md-0">
            <div class="card data-icon-card-primary">
                <div class="card-body">
                    <p class="card-title text-white">Total Beds</p>                      
                    <div class="row">
                        <div class="col-8 text-white">
                            <h3>{{ $stats['total'] }}</h3>
                        </div>
                        <div class="col-4 background-icon">
                            <i class="mdi mdi-hospital-bed"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin grid-margin-md-0">
            <div class="card bg-danger">
                <div class="card-body">
                    <p class="card-title text-white">Occupied</p>                      
                    <div class="row">
                        <div class="col-8 text-white">
                            <h3>{{ $stats['occupied'] }}</h3>
                        </div>
                        <div class="col-4 background-icon">
                            <i class="mdi mdi-account-sleep text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin grid-margin-md-0">
            <div class="card bg-success">
                <div class="card-body">
                    <p class="card-title text-white">Available</p>                      
                    <div class="row">
                        <div class="col-8 text-white">
                            <h3>{{ $stats['available'] }}</h3>
                        </div>
                        <div class="col-4 background-icon">
                            <i class="mdi mdi-check-circle-outline text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Hospital Beds / Wards</h4>
                        <a href="{{ route('beds.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus"></i> Add New Bed</a>
                    </div>
                    
                    <div class="row mt-4">
                        @php $groupedBeds = $beds->groupBy('ward_type'); @endphp
                        
                        @forelse($groupedBeds as $wardName => $wardBeds)
                            <div class="col-12 mb-4">
                                <h5 class="border-bottom pb-2">{{ $wardName }} Ward</h5>
                                <div class="row mt-3">
                                    @foreach($wardBeds as $bed)
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card {{ $bed->is_occupied ? 'border-danger' : 'border-success' }} shadow-sm">
                                            <div class="card-body p-3 text-center">
                                                <i class="mdi mdi-hospital-bed fs-1 {{ $bed->is_occupied ? 'text-danger' : 'text-success' }}"></i>
                                                <h5 class="mt-2">{{ $bed->bed_number }}</h5>
                                                <p class="text-muted small mb-2">Floor: {{ $bed->floor }}</p>
                                                
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if($bed->is_occupied)
                                                        <span class="badge badge-danger">Occupied</span>
                                                    @else
                                                        <span class="badge badge-success">Available</span>
                                                    @endif
                                                    <a href="{{ route('beds.edit', $bed->id) }}" class="btn btn-sm btn-light border p-1" title="Edit"><i class="mdi mdi-pencil m-0"></i></a>
                                                    
                                                    @if(!$bed->is_occupied)
                                                    <form action="{{ route('beds.destroy', $bed->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this bed?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light border text-danger p-1" title="Delete"><i class="mdi mdi-delete m-0"></i></button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">
                                <p>No beds found. Start by adding a new bed.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin>
