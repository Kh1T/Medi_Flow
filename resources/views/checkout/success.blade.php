<x-admin title="Payment Success">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success mb-3">Payment Successful</h3>
                    <p>Invoice #{{ $billing->invoice_number }}</p>
                    <p>Total: ${{ number_format($billing->total, 2) }}</p>

                    <a href="{{ route('billing.show', $billing->id) }}" class="btn btn-primary mt-3">
                        View Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin>