<x-admin title="Billing Management">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Invoices</h4>
                        <a href="{{ route('billing.create') }}" class="btn btn-primary btn-sm">
                            Create New Invoice
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Inv #</th>
                                    <th>Patient</th>
                                    <th>Total Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th width="220">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bills as $bill)
                                    <tr>
                                        <td>{{ $bill->invoice_number }}</td>
                                        <td>{{ $bill->patient->user->name ?? 'No Patient' }}</td>
                                        <td>${{ number_format($bill->total, 2) }}</td>
                                        <td>{{ $bill->due_date }}</td>
                                        <td>
                                            <span class="badge
                                                @if($bill->status == 'paid')
                                                    badge-success
                                                @elseif($bill->status == 'pending')
                                                    badge-warning
                                                @else
                                                    badge-danger
                                                @endif">
                                                {{ ucfirst($bill->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('billing.show', $bill->id) }}"
                                               class="btn btn-outline-info btn-sm me-1">
                                                <i class="mdi mdi-eye"></i> View
                                            </a>

                                            @if($bill->status != 'paid')
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-success btn-sm btn-pay"
                                                    data-id="{{ $bill->id }}"
                                                    data-invoice="{{ $bill->invoice_number }}"
                                                    data-total="{{ $bill->total }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#paymentModal">
                                                    Mark Paid
                                                </button>
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

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Select Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-center">
                    <p class="mb-2">
                        <strong>Invoice:</strong>
                        <span id="modalInvoice">-</span>
                    </p>

                    <p class="mb-3">
                        <strong>Total:</strong>
                        $<span id="modalTotal">0.00</span>
                    </p>

                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <form id="cashForm" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                Pay with Cash
                            </button>
                        </form>

                        <a id="khqrBtn" href="#" class="btn btn-primary">
                            Pay with KHQR
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const buttons = document.querySelectorAll('.btn-pay');
                const modalInvoice = document.getElementById('modalInvoice');
                const modalTotal = document.getElementById('modalTotal');
                const cashForm = document.getElementById('cashForm');
                const khqrBtn = document.getElementById('khqrBtn');

                buttons.forEach(button => {
                    button.addEventListener('click', function () {
                        const id = this.dataset.id;
                        const invoice = this.dataset.invoice;
                        const total = this.dataset.total;

                        modalInvoice.textContent = invoice;
                        modalTotal.textContent = parseFloat(total).toFixed(2);

                        cashForm.action = "{{ url('/checkout') }}/" + id + "/cash";
                        khqrBtn.href = "{{ url('/checkout') }}/" + id + "/khqr";
                    });
                });
            });
        </script>
    @endpush
</x-admin>