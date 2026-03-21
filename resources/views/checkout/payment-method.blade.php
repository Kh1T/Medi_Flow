<x-admin title="Select Payment Method">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="mb-3">Select Payment Method</h4>

                    <p><strong>Invoice:</strong> {{ $billing->invoice_number }}</p>
                    <p><strong>Total:</strong> ${{ number_format($billing->total, 2) }}</p>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <form action="{{ route('checkout.pay.cash', $billing->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                Pay with Cash
                            </button>
                        </form>

                        <a href="{{ route('checkout.pay.khqr', $billing->id) }}" class="btn btn-primary">
                            Pay with KHQR
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>