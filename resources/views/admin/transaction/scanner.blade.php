@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius:16px;">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <h5 class="mb-1">Ticket QR Scanner</h5>
                                <p class="text-muted mb-0">Scan customer QR tickets and confirm check-in at the door.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button id="startScannerBtn" class="btn btn-primary">Start Camera</button>
                                <button id="stopScannerBtn" class="btn btn-outline-danger" disabled>Stop Camera</button>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <div class="border rounded-3 p-2 bg-dark-subtle" style="min-height:280px;">
                                    <div id="reader" style="width:100%;"></div>
                                </div>
                                <small class="text-muted d-block mt-2">Tip: hold the QR in frame for 1-2 seconds.</small>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <label for="manualCode" class="form-label fw-semibold">Manual Ticket Code</label>
                                    <div class="input-group mb-3">
                                        <input type="text" id="manualCode" class="form-control" placeholder="Paste or type ticket code">
                                        <button class="btn btn-outline-secondary" type="button" id="manualLookupBtn">Verify</button>
                                    </div>

                                    <div id="scanStatus" class="small text-muted mb-3">Waiting for scan...</div>

                                    <div id="ticketResult" class="d-none">
                                        <div class="rounded-3 p-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                            <div class="fw-semibold mb-2">Purchase Details</div>
                                            <div class="small" id="ticketDetails"></div>
                                        </div>

                                        <form method="POST" action="{{ route('admin.transaction.scan.check-in') }}" id="checkInForm" class="d-flex flex-wrap gap-2">
                                            @csrf
                                            <input type="hidden" name="ticket_qr_code" id="checkInCode">
                                            <button type="submit" id="checkInBtn" class="btn btn-success px-4">Checked In</button>
                                            <button type="button" id="cancelBtn" class="btn btn-outline-secondary px-4">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startScannerBtn = document.getElementById('startScannerBtn');
    const stopScannerBtn = document.getElementById('stopScannerBtn');
    const manualLookupBtn = document.getElementById('manualLookupBtn');
    const manualCodeInput = document.getElementById('manualCode');
    const scanStatus = document.getElementById('scanStatus');
    const ticketResult = document.getElementById('ticketResult');
    const ticketDetails = document.getElementById('ticketDetails');
    const checkInCode = document.getElementById('checkInCode');
    const checkInBtn = document.getElementById('checkInBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    let html5QrCode = null;
    let scannerStarted = false;

    function setStatus(message, isError) {
        scanStatus.textContent = message;
        scanStatus.classList.toggle('text-danger', !!isError);
        scanStatus.classList.toggle('text-muted', !isError);
    }

    async function startScanner() {
        if (scannerStarted) {
            return;
        }

        if (typeof Html5Qrcode === 'undefined') {
            setStatus('Scanner library failed to load. Refresh and try again.', true);
            return;
        }

        html5QrCode = new Html5Qrcode('reader');

        try {
            await html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                function () {}
            );
            scannerStarted = true;
            setStatus('Camera active. Scan a QR ticket.', false);
            startScannerBtn.textContent = 'Camera Running';
            startScannerBtn.disabled = true;
            stopScannerBtn.disabled = false;
        } catch (error) {
            setStatus('Unable to access camera. Use manual code verification.', true);
        }
    }

    async function stopScanner() {
        if (!scannerStarted || !html5QrCode) {
            return;
        }

        try {
            await html5QrCode.stop();
        } catch (error) {
            // Ignore stop errors and continue reset.
        }

        scannerStarted = false;
        startScannerBtn.textContent = 'Start Camera';
        startScannerBtn.disabled = false;
        stopScannerBtn.disabled = true;
        setStatus('Camera stopped. You can restart anytime.', false);
    }

    function renderDetails(data) {
        const transaction = data.transaction;
        const checkedInText = transaction.checked_in_status
            ? '<div><strong>Check-In:</strong> Already checked in at ' + (transaction.checked_in_at_pacific || '-') + ' PT</div>'
            : '<div><strong>Check-In:</strong> Not checked in</div>';

        ticketDetails.innerHTML = [
            '<div><strong>Order:</strong> #' + transaction.id + '</div>',
            '<div><strong>Transaction ID:</strong> ' + (transaction.transaction_id || '-') + '</div>',
            '<div><strong>Name:</strong> ' + (transaction.guest_name || '-') + '</div>',
            '<div><strong>Email:</strong> ' + (transaction.package_email || '-') + '</div>',
            '<div><strong>Phone:</strong> ' + (transaction.package_phone || '-') + '</div>',
            '<div><strong>Website:</strong> ' + (transaction.website_name || '-') + '</div>',
            '<div><strong>Total:</strong> $' + (transaction.total || '0.00') + '</div>',
            '<div><strong>Use Date:</strong> ' + (transaction.package_use_date || '-') + '</div>',
            checkedInText
        ].join('');

        checkInCode.value = transaction.ticket_qr_code || '';
        checkInBtn.disabled = !!transaction.checked_in_status;
        if (transaction.checked_in_status) {
            checkInBtn.textContent = 'Already Checked In';
            checkInBtn.classList.remove('btn-success');
            checkInBtn.classList.add('btn-secondary');
        } else {
            checkInBtn.textContent = 'Checked In';
            checkInBtn.classList.add('btn-success');
            checkInBtn.classList.remove('btn-secondary');
        }

        ticketResult.classList.remove('d-none');
    }

    function resetResult() {
        ticketResult.classList.add('d-none');
        ticketDetails.innerHTML = '';
        checkInCode.value = '';
        manualCodeInput.value = '';
        setStatus('Ready for next ticket scan.', false);
    }

    async function verifyCode(rawCode) {
        const code = (rawCode || '').trim();
        if (!code) {
            setStatus('Ticket code is required.', true);
            return;
        }

        setStatus('Verifying ticket...', false);

        try {
            const url = '{{ route('admin.transaction.scan.lookup') }}?ticket_qr_code=' + encodeURIComponent(code);
            const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();

            if (!response.ok || !data.success) {
                setStatus(data.message || 'Ticket validation failed.', true);
                ticketResult.classList.add('d-none');
                return;
            }

            renderDetails(data);
            setStatus('Ticket verified. Confirm check-in or cancel.', false);
            await stopScanner();
        } catch (error) {
            setStatus('Unable to verify ticket right now.', true);
            ticketResult.classList.add('d-none');
        }
    }

    function onScanSuccess(decodedText) {
        verifyCode(decodedText);
    }

    startScannerBtn.addEventListener('click', startScanner);
    stopScannerBtn.addEventListener('click', stopScanner);

    manualLookupBtn.addEventListener('click', function () {
        verifyCode(manualCodeInput.value);
    });

    manualCodeInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            verifyCode(manualCodeInput.value);
        }
    });

    cancelBtn.addEventListener('click', async function () {
        resetResult();
        await startScanner();
    });
});
</script>
@endsection
