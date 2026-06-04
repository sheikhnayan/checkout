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
                                    <label for="manualCode" class="form-label fw-semibold">Manual Ticket Code <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter or paste a ticket code manually to look up purchase details without using the camera scanner."></i></label>
                                    <div class="input-group mb-3">
                                        <input type="text" id="manualCode" class="form-control" placeholder="Paste or type ticket code">
                                        <button class="btn btn-outline-secondary" type="button" id="manualLookupBtn">Verify</button>
                                    </div>

                                    <div id="scanStatus" class="small text-muted mb-3">Waiting for scan...</div>

                                    <div id="ticketResult" class="d-none">
                                        <!-- Check-In Photo (if available) -->
                                        <div id="checkinPhotoSection" class="d-none mb-3">
                                            <div class="rounded-3 p-3" style="background:#f0fdf4;border:1px solid #86efac;">
                                                <div class="fw-semibold text-success mb-2">
                                                    <i class="fas fa-camera-alt"></i> Check-In Photo
                                                </div>
                                                <img id="checkinPhotoImg" style="width:100%;max-height:300px;object-fit:cover;border-radius:8px;">
                                                <small class="text-muted d-block mt-2">Captured during check-in verification</small>
                                            </div>
                                        </div>

                                        <div class="rounded-3 p-3 mb-3" style="background:#0f172a;border:1px solid #334155;color:#e2e8f0;">
                                            <div class="fw-semibold mb-2">Purchase Details</div>
                                            <div class="small" id="ticketDetails" style="color:#e2e8f0;"></div>
                                        </div>

                                        <!-- Photo Capture Section (MANDATORY - Front & Back ID) -->
                                        <div class="mb-3 p-3 rounded-3" style="background:#0f172a;border:2px solid #3b82f6;color:#e2e8f0;">
                                            <div class="fw-semibold mb-3" style="color:#60a5fa;">
                                                <i class="fas fa-camera-alt"></i> Identity Verification Photos <span style="color:#ff4444;">*</span>
                                            </div>
                                            <p class="mb-3" style="font-size:14px;line-height:1.6;">After scanning the package QR code, take photos of both the front and back of the guest's valid ID for identity verification and fraud prevention. ID images are securely uploaded to an encrypted server and are never stored locally on the scanning device.</p>

                                            <div id="photoCaptureSection" style="margin-top:15px;">
                                                <!-- Photo Progress Indicator -->
                                                <div class="d-flex gap-2 mb-3">
                                                    <div class="flex-grow-1 p-2 rounded-2" style="background:#1a3a1a;border:2px solid #22c55e;">
                                                        <small style="color:#86efac;"><strong><i class="fas fa-id-card"></i> Front of ID</strong></small>
                                                        <div id="frontPhotoStatus" style="color:#60a5fa;font-size:12px;margin-top:4px;">Pending</div>
                                                    </div>
                                                    <div class="flex-grow-1 p-2 rounded-2" style="background:#1a2a3a;border:2px solid #64b5f6;">
                                                        <small style="color:#90caf9;"><strong><i class="fas fa-id-card"></i> Back of ID</strong></small>
                                                        <div id="backPhotoStatus" style="color:#60a5fa;font-size:12px;margin-top:4px;">Pending</div>
                                                    </div>
                                                </div>

                                                <div class="border-2 rounded-2 p-2 mb-3" style="min-height:200px;max-height:300px;overflow:hidden;background:#1a1f2e;border-color:#3b82f6;position:relative;">
                                                    <video id="photoCameraFeed" style="width:100%;height:100%;object-fit:cover;display:none;background:#000;"></video>
                                                    <canvas id="photoCanvas" style="width:100%;height:100%;display:none;"></canvas>
                                                    <div id="noCameraMsg" class="text-center p-5" style="color:#60a5fa;">
                                                        <i class="fas fa-camera fa-3x mb-3 d-block opacity-75"></i>
                                                        <div style="font-size:14px;">Camera will appear here</div>
                                                    </div>
                                                </div>

                                                <div class="btn-group w-100 mb-3" role="group">
                                                    <button type="button" id="startPhotoCameraBtn" class="btn btn-primary" style="flex:1;">Start Camera</button>
                                                    <button type="button" id="capturePhotoBtn" class="btn btn-success d-none" style="flex:1;"><span id="capturePhotoText">Capture Photo</span></button>
                                                    <button type="button" id="stopPhotoCameraBtn" class="btn btn-danger d-none" style="flex:1;">Stop Camera</button>
                                                </div>

                                                <!-- Front ID Preview -->
                                                <div id="frontPhotoPreviewContainer" class="d-none mb-3">
                                                    <div class="fw-semibold mb-2" style="color:#86efac;"><i class="fas fa-check-circle"></i> Front of ID Captured</div>
                                                    <img id="frontPhotoPreview" style="width:100%;max-height:200px;object-fit:cover;border-radius:8px;border:2px solid #22c55e;">
                                                </div>

                                                <!-- Back ID Preview -->
                                                <div id="backPhotoPreviewContainer" class="d-none mb-3">
                                                    <div class="fw-semibold mb-2" style="color:#90caf9;"><i class="fas fa-check-circle"></i> Back of ID Captured</div>
                                                    <img id="backPhotoPreview" style="width:100%;max-height:200px;object-fit:cover;border-radius:8px;border:2px solid #3b82f6;">
                                                    <small class="text-success d-block mt-2"><i class="fas fa-check-double"></i> Both photos ready to submit</small>
                                                    <button type="button" id="retakePhotosBtn" class="btn btn-warning btn-sm mt-3">
                                                        <i class="fas fa-camera"></i> Retake Photos
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <form method="POST" action="{{ route('admin.transaction.scan.check-in') }}" id="checkInForm" class="d-flex flex-wrap gap-2">
                                            @csrf
                                            <input type="hidden" name="ticket_qr_code" id="checkInCode">
                                            <input type="hidden" name="photo_data_front" id="frontPhotoData">
                                            <input type="hidden" name="photo_data_back" id="backPhotoData">
                                            <button type="submit" id="checkInBtn" class="btn btn-success px-4">Check In</button>
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

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

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
        const checkinPhotoSection = document.getElementById('checkinPhotoSection');
        const checkinPhotoImg = document.getElementById('checkinPhotoImg');

        // Display check-in photo if available
        if (transaction.checkin_photo_path) {
            const photoUrl = '{{ route("admin.transaction.checkin-photo", ["id" => "TRANSACTION_ID"]) }}'.replace('TRANSACTION_ID', transaction.id);
            checkinPhotoImg.src = photoUrl;
            checkinPhotoSection.classList.remove('d-none');
        } else {
            checkinPhotoSection.classList.add('d-none');
        }

        const packageDetails = Array.isArray(transaction.package_details) ? transaction.package_details : [];
        const packageListHtml = packageDetails.length
            ? '<div><strong>Packages:</strong></div><ul class="mb-2 ps-3">' + packageDetails.map(function (item) {
                const guests = Number(item.guests || 0) || 1;
                const guestLabel = guests === 1 ? 'person' : 'people';
                const addons = Array.isArray(item.addons) ? item.addons.filter(Boolean) : [];
                const addonsHtml = addons.length
                    ? '<div class="mt-1"><strong>Add-ons:</strong> ' + addons.map(function (addonName) {
                        return escapeHtml(addonName);
                    }).join(', ') + '</div>'
                    : '<div class="mt-1"><strong>Add-ons:</strong> None</div>';
                return '<li>' + escapeHtml(item.package_name || 'Package') + ' - ' + guests + ' ' + guestLabel + addonsHtml + '</li>';
            }).join('') + '</ul>'
            : '<div><strong>Packages:</strong> -</div>';
        const checkedInText = transaction.checked_in_status
            ? '<div style="color:#ff2b2b;font-weight:700;"><strong>Check-In:</strong> ALREADY CHECKED IN at ' + (transaction.checked_in_at_pacific || '-') + ' PT</div>'
            : '<div><strong>Check-In:</strong> Not checked in</div>';

        ticketDetails.innerHTML = [
            '<div><strong>Order:</strong> #' + transaction.id + '</div>',
            '<div><strong>Transaction ID:</strong> ' + (transaction.transaction_id || '-') + '</div>',
            '<div><strong>Name:</strong> ' + (transaction.guest_name || '-') + '</div>',
            '<div><strong>Email:</strong> ' + (transaction.package_email || '-') + '</div>',
            '<div><strong>Phone:</strong> ' + (transaction.package_phone || '-') + '</div>',
            '<div><strong>Website:</strong> ' + (transaction.website_name || '-') + '</div>',
            '<div><strong>Event:</strong> ' + (transaction.event_name || '-') + '</div>',
            '<div><strong>Total:</strong> $' + (transaction.total || '0.00') + '</div>',
            '<div><strong>Total Guests:</strong> ' + (transaction.total_guests || '-') + '</div>',
            '<div><strong>Use Date:</strong> ' + (transaction.package_use_date || '-') + '</div>',
            packageListHtml,
            checkedInText
        ].join('');

        checkInCode.value = transaction.ticket_qr_code || '';
        checkInBtn.disabled = !!transaction.checked_in_status;
        if (transaction.checked_in_status) {
            checkInBtn.textContent = 'Already Checked In';
            checkInBtn.classList.remove('btn-success', 'btn-secondary');
            checkInBtn.classList.add('btn-danger');
        } else {
            checkInBtn.textContent = 'Check In';
            checkInBtn.classList.add('btn-success');
            checkInBtn.classList.remove('btn-secondary', 'btn-danger');
        }

        ticketResult.classList.remove('d-none');
    }

    function resetResult() {
        ticketResult.classList.add('d-none');
        ticketDetails.innerHTML = '';
        checkInCode.value = '';
        manualCodeInput.value = '';
        stopPhotoCamera();
        frontPhotoPreviewContainer.classList.add('d-none');
        backPhotoPreviewContainer.classList.add('d-none');
        frontPhotoData.value = '';
        backPhotoData.value = '';
        frontPhotoCaptured = false;
        backPhotoCaptured = false;
        capturingFrontPhoto = true;
        capturePhotoText.textContent = 'Capture Front of ID';
        frontPhotoStatus.textContent = 'Pending';
        frontPhotoStatus.style.color = '#60a5fa';
        backPhotoStatus.textContent = 'Pending';
        backPhotoStatus.style.color = '#60a5fa';
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

    // ========== PHOTO CAPTURE FUNCTIONALITY ==========
    const photoCaptureSection = document.getElementById('photoCaptureSection');
    const photoCameraFeed = document.getElementById('photoCameraFeed');
    const photoCanvas = document.getElementById('photoCanvas');
    const noCameraMsg = document.getElementById('noCameraMsg');
    const startPhotoCameraBtn = document.getElementById('startPhotoCameraBtn');
    const capturePhotoBtn = document.getElementById('capturePhotoBtn');
    const capturePhotoText = document.getElementById('capturePhotoText');
    const stopPhotoCameraBtn = document.getElementById('stopPhotoCameraBtn');
    const frontPhotoPreviewContainer = document.getElementById('frontPhotoPreviewContainer');
    const frontPhotoPreview = document.getElementById('frontPhotoPreview');
    const backPhotoPreviewContainer = document.getElementById('backPhotoPreviewContainer');
    const backPhotoPreview = document.getElementById('backPhotoPreview');
    const frontPhotoData = document.getElementById('frontPhotoData');
    const backPhotoData = document.getElementById('backPhotoData');
    const photoDataInput = document.getElementById('photoDataInput');
    const frontPhotoStatus = document.getElementById('frontPhotoStatus');
    const backPhotoStatus = document.getElementById('backPhotoStatus');

    let photoCameraStream = null;
    let photoCtx = null;
    let frontPhotoCaptured = false;
    let backPhotoCaptured = false;
    let capturingFrontPhoto = true; // Start with front photo
    let currentFacingMode = 'environment'; // Default to back camera

    startPhotoCameraBtn.addEventListener('click', async function() {
        try {
            // Stop QR scanner to prevent interference
            if (scannerStarted && html5QrCode) {
                await stopScanner();
                startScannerBtn.disabled = true;
            }

            photoCameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: currentFacingMode, width: { ideal: 1280 }, height: { ideal: 720 } }
            });

            photoCameraFeed.srcObject = photoCameraStream;
            photoCameraFeed.play();
            photoCameraFeed.style.display = 'block';
            noCameraMsg.style.display = 'none';

            startPhotoCameraBtn.classList.add('d-none');
            capturePhotoBtn.classList.remove('d-none');
            stopPhotoCameraBtn.classList.remove('d-none');

            // Show camera switch button if camera is running
            if (cameraSwitchBtn) {
                cameraSwitchBtn.classList.remove('d-none');
            }
        } catch (error) {
            alert('Unable to access camera. Check browser permissions.');
            console.error('Camera error:', error);
        }
    });

    // Camera switch functionality
    const cameraSwitchBtn = document.createElement('button');
    cameraSwitchBtn.type = 'button';
    cameraSwitchBtn.id = 'cameraSwitchBtn';
    cameraSwitchBtn.className = 'btn btn-info d-none';
    cameraSwitchBtn.style.position = 'absolute';
    cameraSwitchBtn.style.top = '10px';
    cameraSwitchBtn.style.right = '10px';
    cameraSwitchBtn.style.zIndex = '10';
    cameraSwitchBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Switch Camera';

    // Insert camera switch button into the camera container
    const photoCameraContainer = photoCameraFeed.parentElement;
    photoCameraContainer.style.position = 'relative';
    photoCameraContainer.appendChild(cameraSwitchBtn);

    cameraSwitchBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        if (!photoCameraStream) return;

        // Stop current stream
        photoCameraStream.getTracks().forEach(track => track.stop());
        photoCameraStream = null;

        // Switch facing mode
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';

        try {
            photoCameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: currentFacingMode, width: { ideal: 1280 }, height: { ideal: 720 } }
            });

            photoCameraFeed.srcObject = photoCameraStream;
            photoCameraFeed.play();
        } catch (error) {
            alert('Unable to switch camera.');
            console.error('Camera switch error:', error);
        }
    });

    capturePhotoBtn.addEventListener('click', function() {
        if (!photoCameraStream || !photoCameraFeed.srcObject) {
            alert('Camera is not ready.');
            return;
        }

        // Get canvas context
        photoCtx = photoCanvas.getContext('2d');
        photoCanvas.width = photoCameraFeed.videoWidth;
        photoCanvas.height = photoCameraFeed.videoHeight;

        // Draw current frame from video to canvas
        photoCtx.drawImage(photoCameraFeed, 0, 0, photoCanvas.width, photoCanvas.height);

        // Get image data as base64
        const imageData = photoCanvas.toDataURL('image/jpeg', 0.9);

        if (capturingFrontPhoto) {
            // Capture front of ID
            frontPhotoData.value = imageData;
            frontPhotoPreview.src = imageData;
            frontPhotoPreviewContainer.classList.remove('d-none');
            frontPhotoCaptured = true;
            frontPhotoStatus.textContent = '✓ Captured';
            frontPhotoStatus.style.color = '#86efac';

            // Move to back photo
            capturingFrontPhoto = false;
            capturePhotoText.textContent = 'Capture Back of ID';
            alert('Front of ID captured. Now capture the back of the ID.');
        } else {
            // Capture back of ID
            backPhotoData.value = imageData;
            backPhotoPreview.src = imageData;
            backPhotoPreviewContainer.classList.remove('d-none');
            backPhotoCaptured = true;
            backPhotoStatus.textContent = '✓ Captured';
            backPhotoStatus.style.color = '#90caf9';

            // All photos captured
            capturePhotoText.textContent = 'Both photos captured!';
            stopPhotoCamera();
        }
    });

    function stopPhotoCamera() {
        if (photoCameraStream) {
            photoCameraStream.getTracks().forEach(track => track.stop());
            photoCameraStream = null;
        }

        photoCameraFeed.style.display = 'none';
        noCameraMsg.style.display = 'block';

        startPhotoCameraBtn.classList.remove('d-none');
        capturePhotoBtn.classList.add('d-none');
        stopPhotoCameraBtn.classList.add('d-none');

        // Hide camera switch button when camera is stopped
        if (cameraSwitchBtn) {
            cameraSwitchBtn.classList.add('d-none');
        }

        // Re-enable QR scanner button
        startScannerBtn.disabled = false;
    }

    stopPhotoCameraBtn.addEventListener('click', stopPhotoCamera);

    // Retake Photos functionality
    const retakePhotosBtn = document.getElementById('retakePhotosBtn');
    if (retakePhotosBtn) {
        retakePhotosBtn.addEventListener('click', function() {
            // Reset photo states
            frontPhotoCaptured = false;
            backPhotoCaptured = false;
            capturingFrontPhoto = true;

            // Clear photo data and previews
            frontPhotoData.value = '';
            backPhotoData.value = '';
            frontPhotoPreviewContainer.classList.add('d-none');
            backPhotoPreviewContainer.classList.add('d-none');

            // Reset status indicators
            frontPhotoStatus.textContent = 'Pending';
            frontPhotoStatus.style.color = '#60a5fa';
            backPhotoStatus.textContent = 'Pending';
            backPhotoStatus.style.color = '#60a5fa';

            // Reset capture button text
            capturePhotoText.textContent = 'Capture Front of ID';

            // Show photo capture section
            photoCaptureSection.style.display = '';
        });
    }

    // Validate both photos captured before form submission
    document.getElementById('checkInForm').addEventListener('submit', function(e) {
        if (!frontPhotoCaptured) {
            e.preventDefault();
            alert('Front of ID photo is required. Please capture the front side of the guest\'s ID.');
            return false;
        }
        if (!backPhotoCaptured) {
            e.preventDefault();
            alert('Back of ID photo is required. Please capture the back side of the guest\'s ID.');
            return false;
        }
        stopPhotoCamera();
    });
});
</script>
@endsection
