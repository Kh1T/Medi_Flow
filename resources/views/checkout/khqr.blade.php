<x-admin title="KHQR Payment">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4 text-center">
                <h4 class="mb-2">Invoice #{{ $billing->invoice_number }}</h4>
                <p class="text-muted">
                    Amount: <strong>${{ number_format($billing->total, 2) }}</strong>
                </p>

                @if($qr)
                    <div class="bg-white border rounded p-3 d-inline-block mx-auto mb-3">
                        <div id="qrcode"></div>
                    </div>
                @else
                    <div class="alert alert-danger">
                        Unable to generate QR code.
                    </div>
                @endif

                <div id="statusBox" class="mt-3 text-muted">
                    Waiting for payment...
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

        <script>
            const qrString = @json($qr ?? null);
            const md5 = @json($md5 ?? null);
            const billingId = @json($billing->id);
            const verifyUrl = @json(route('checkout.verify'));
            const successUrl = @json(route('checkout.success', $billing->id));
            const csrfToken = @json(csrf_token());

            // Generate QR
            if (qrString) {
                new QRCode(document.getElementById('qrcode'), {
                    text: qrString,
                    width: 220,
                    height: 220,
                });
            }

            let checking = false;

            function setStatus(message, type = 'info') {
                document.getElementById('statusBox').innerHTML =
                    `<div class="alert alert-${type} mb-0">${message}</div>`;
            }

            async function verifyPayment() {
                if (checking || !md5) return;

                checking = true;

                try {
                    const response = await fetch(verifyUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            md5: md5,
                            billing_id: billingId
                        })
                    });

                    const data = await response.json();
                    console.log('Verify response:', data);

                    if (!response.ok) {
                        // Server error (like missing BAKONG_TOKEN)
                        setStatus(data.message || 'Server error. Please check configuration.', 'danger');
                        return; // Don't keep retrying on server errors
                    }

                    if (data.paid) {
                        setStatus(data.message || 'Payment successful! Redirecting...', 'success');

                        setTimeout(() => {
                            window.location.href = data.redirect || successUrl;
                        }, 1200);

                        return; // stop loop
                    } else {
                        setStatus(data.message || 'Waiting for payment...', 'warning');
                    }
                } catch (error) {
                    console.error(error);
                    setStatus('Error checking payment...', 'danger');
                } finally {
                    checking = false;
                }

                // check again after 5s
                setTimeout(verifyPayment, 5000);
            }

            // AUTO START checking
            verifyPayment();
        </script>
    @endpush
</x-admin>