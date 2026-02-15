<x-admin title="Billing Management">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Invoices</h4>
                        <a href="{{ route('billing.create') }}" class="btn btn-primary btn-sm">Create New Invoice</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Inv #</th>
                                    <th>Patient</th>
                                    <th>Total Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bills as $bill)
                                    <tr>
                                        <td>{{ $bill->invoice_number }}</td>
                                        <td>{{ $bill->patient->user->name }}</td>
                                        <td>${{ number_format($bill->total, 2) }}</td>
                                        <td>{{ $bill->due_date }}</td>
                                        <td>
                                            <span class="badge @if($bill->status == 'paid') badge-success @elseif($bill->status == 'pending') badge-warning @else badge-danger @endif">
                                                {{ ucfirst($bill->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('billing.show', $bill->id) }}" class="btn btn-outline-info btn-sm me-1"><i class="mdi mdi-eye"></i> View</a>
                                            @if($bill->status != 'paid')
                                            <form action="{{ route('billing.update', $bill->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="paid">
                                                <button type="submit" class="btn btn-outline-success btn-sm">Mark Paid</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No invoices found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $bills->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
