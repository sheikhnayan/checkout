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
                            <div class="d-flex gap-2" id="qrCameraControlsBtn">
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

                        <!-- QR Scanner & Manual Input Section (Hidden when transaction found) -->
                        <div class="row g-3" id="scannerSection">
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
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Results Section (Shown when transaction found) -->
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
                                            <p class="mb-3" style="font-size:14px;line-height:1.6;">After scanning the package QR code, take a photo of the guest's valid ID for identity verification and fraud prevention. ID images are securely uploaded to an encrypted server and are never stored locally on the scanning device.</p>

                                            <div id="photoCaptureSection" style="margin-top:15px;">
                                                <!-- Photo Progress Indicator with Auto-Switch Message -->
                                                <div class="d-flex gap-2 mb-3">
                                                    <div class="flex-grow-1 p-3 rounded-2" id="frontPhotoIndicator" style="background:#1a3a1a;border:2px solid #22c55e;transition:all 0.3s ease;">
                                                        <small style="color:#86efac;"><strong><i class="fas fa-id-card"></i> Front of ID</strong></small>
                                                        <div id="frontPhotoStatus" style="color:#60a5fa;font-size:12px;margin-top:4px;">Pending</div>
                                                    </div>
                                                    <div class="flex-grow-1 p-3 rounded-2" id="backPhotoIndicator" style="background:#1a2a3a;border:2px solid #64b5f6;transition:all 0.3s ease;opacity:0.5;">
                                                        <small style="color:#90caf9;"><strong><i class="fas fa-id-card"></i> Back of ID</strong></small>
                                                        <div id="backPhotoStatus" style="color:#60a5fa;font-size:12px;margin-top:4px;">Pending</div>
                                                    </div>
                                                </div>

                                                <!-- Current Side Label (Auto-Switch Indicator) -->
                                                <div id="currentSideLabel" style="text-align:center;margin-bottom:12px;padding:8px;background:rgba(34,197,94,0.15);border-radius:8px;border:1px solid #22c55e;display:none;">
                                                    <strong id="currentSideText" style="color:#86efac;">📷 Capturing Front of ID</strong>
                                                </div>

                                                <div class="border-2 rounded-3 p-0 mb-3" style="width:100%;height:300px;overflow:hidden;background:#000;border-color:#3b82f6;position:relative;">
                                                    <video id="photoCameraFeed" style="width:100%;height:100%;object-fit:cover;display:none;background:#000;"></video>
                                                    <canvas id="photoCanvas" style="display:none;"></canvas>
                                                    <!-- ID Frame Guide - GREEN RECTANGLE FRAME -->
                                                    <svg id="idFrameGuide" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:10;display:none;"></svg>
                                                    <!-- Photo Flash Effect -->
                                                    <div id="photoFlash" style="position:absolute;top:0;left:0;width:100%;height:100%;background:#fff;z-index:20;display:none;opacity:0.7;"></div>
                                                    <!-- Photo Captured Checkmark -->
                                                    <div id="photoCapturedCheck" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:70px;display:none;z-index:15;animation:scaleIn 0.5s ease-out;">
                                                        <i class="fas fa-check-circle" style="color:#22c55e;text-shadow:0 0 15px rgba(34,197,94,0.9);filter:drop-shadow(0 0 8px rgba(34,197,94,0.8));"></i>
                                                    </div>
                                                    <div id="noCameraMsg" class="text-center p-5" style="color:#60a5fa;z-index:5;">
                                                        <i class="fas fa-camera fa-3x mb-3 d-block opacity-75"></i>
                                                        <div style="font-size:14px;">Camera will appear here</div>
                                                    </div>
                                                </div>

                                                <style>
                                                    @keyframes scaleIn {
                                                        from { transform: translate(-50%, -50%) scale(0.5); opacity: 0; }
                                                        to { transform: translate(-50%, -50%) scale(1); opacity: 1; }
                                                    }
                                                    @keyframes flash {
                                                        from { opacity: 0.7; }
                                                        to { opacity: 0; }
                                                    }
                                                    #photoFlash.flashing {
                                                        animation: flash 0.3s ease-out;
                                                    }
                                                </style>

                                                <div class="btn-group w-100 mb-3 gap-2" role="group" style="display: flex; flex-wrap: wrap;">
                                                    <button type="button" id="startPhotoCameraBtn" class="btn btn-primary" style="flex:1;min-width:150px;">Start Camera</button>
                                                    <button type="button" id="capturePhotoBtn" class="btn btn-success d-none" style="flex:1;min-width:150px;"><span id="capturePhotoText">Capture Photo</span></button>
                                                    <button type="button" id="stopPhotoCameraBtn" class="btn btn-danger d-none" style="flex:1;min-width:150px;">Stop Camera</button>
                                                </div>

                                                <!-- Front ID Preview -->
                                                <div id="frontPhotoPreviewContainer" class="d-none mb-3">
                                                    <div class="fw-semibold mb-2" style="color:#86efac;"><i class="fas fa-check-circle"></i> Front of ID Captured</div>
                                                    <div style="width:100%;max-height:280px;border-radius:8px;border:2px solid #22c55e;background:#000;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                                        <img id="frontPhotoPreview" style="max-width:100%;max-height:100%;cursor:pointer;" onclick="window.open(this.src, '_blank');" title="Click to view larger">
                                                    </div>
                                                    <small class="text-muted d-block mt-2" style="font-size:11px;"><i class="fas fa-info-circle"></i> Frame Reference: ID card should fill the green frame guide</small>
                                                </div>

                                                <!-- Back ID Preview -->
                                                <div id="backPhotoPreviewContainer" class="d-none mb-3">
                                                    <div class="fw-semibold mb-2" style="color:#90caf9;"><i class="fas fa-check-circle"></i> Back of ID Captured</div>
                                                    <div style="width:100%;max-height:280px;border-radius:8px;border:2px solid #3b82f6;background:#000;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                                        <img id="backPhotoPreview" style="max-width:100%;max-height:100%;cursor:pointer;" onclick="window.open(this.src, '_blank');" title="Click to view larger">
                                                    </div>
                                                    <small class="text-muted d-block mt-2" style="font-size:11px;"><i class="fas fa-info-circle"></i> Frame Reference: ID card should fill the green frame guide</small>
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

        // Always create fresh instance to reset state
        try {
            if (html5QrCode) {
                try {
                    await html5QrCode.stop();
                } catch (e) {}
            }
            html5QrCode = new Html5Qrcode('reader');
        } catch (e) {}

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
            console.error('QR Scanner start error:', error);
            setStatus('Unable to access camera. Use manual code verification.', true);
            scannerStarted = false;
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
        document.getElementById('scannerSection').classList.add('d-none');
        document.getElementById('qrCameraControlsBtn').classList.add('d-none');

        // Auto-scroll to ticket results
        setTimeout(function() {
            ticketResult.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }

    function resetResult() {
        ticketResult.classList.add('d-none');
        document.getElementById('scannerSection').classList.remove('d-none');
        document.getElementById('qrCameraControlsBtn').classList.remove('d-none');
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
        setStatus('✓ SUCCESS - QR code is scanned', false);
        setTimeout(function() {
            stopScanner();
            verifyCode(decodedText);
        }, 1500);
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
            // Stop QR scanner to prevent interference and release camera
            if (scannerStarted && html5QrCode) {
                try {
                    await stopScanner();
                } catch (e) {
                    console.log('QR Scanner stop error (non-critical):', e);
                }
                startScannerBtn.disabled = false; // Re-enable button after stopping QR
            }

            // Add small delay to ensure QR camera is fully released
            await new Promise(resolve => setTimeout(resolve, 200));

            // Stop any existing photo camera stream
            if (photoCameraStream) {
                photoCameraStream.getTracks().forEach(track => track.stop());
                photoCameraStream = null;
            }

            // Try to get camera with proper error handling
            try {
                photoCameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: currentFacingMode, width: { ideal: 1280 }, height: { ideal: 720 } }
                });
            } catch (cameraError) {
                console.error('Camera access error:', cameraError);
                // If environment camera fails, try user camera (front-facing)
                if (currentFacingMode === 'environment') {
                    console.log('Environment camera failed, trying user camera...');
                    currentFacingMode = 'user';
                    photoCameraStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }
                    });
                } else {
                    throw cameraError;
                }
            }

            photoCameraFeed.srcObject = photoCameraStream;
            photoCameraFeed.play();
            photoCameraFeed.style.display = 'block';
            noCameraMsg.style.display = 'none';

            // Draw ID frame guide using SVG
            const idFrameGuide = document.getElementById('idFrameGuide');
            const container = photoCameraFeed.parentElement;

            function drawIDFrame() {
                try {
                    const w = container.offsetWidth;
                    const h = container.offsetHeight;

                    if (w === 0 || h === 0) return;

                    // Frame dimensions (US ID ratio: 1.588:1) - 65% of available space
                    let frameH = h * 0.65; // Height is 65% of camera height
                    let frameW = frameH * 1.588; // Width proportional (ID ratio)

                    // Make sure frame fits within camera width (leave 7% margin on each side)
                    if (frameW > w * 0.86) {
                        frameW = w * 0.86;
                        frameH = frameW / 1.588;
                    }

                    const frameX = (w - frameW) / 2;
                    const frameY = (h - frameH) / 2;

                // Clear and redraw SVG
                idFrameGuide.innerHTML = '';
                idFrameGuide.setAttribute('viewBox', `0 0 ${w} ${h}`);
                idFrameGuide.setAttribute('preserveAspectRatio', 'none');

                // Dark overlay outside frame
                const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                const mask = document.createElementNS('http://www.w3.org/2000/svg', 'mask');
                mask.setAttribute('id', 'frameMask');

                const maskBg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                maskBg.setAttribute('width', w);
                maskBg.setAttribute('height', h);
                maskBg.setAttribute('fill', 'white');
                mask.appendChild(maskBg);

                const maskFrame = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                maskFrame.setAttribute('x', frameX);
                maskFrame.setAttribute('y', frameY);
                maskFrame.setAttribute('width', frameW);
                maskFrame.setAttribute('height', frameH);
                maskFrame.setAttribute('fill', 'black');
                maskFrame.setAttribute('rx', '6');
                mask.appendChild(maskFrame);
                defs.appendChild(mask);
                idFrameGuide.appendChild(defs);

                // Dark overlay
                const overlay = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                overlay.setAttribute('width', w);
                overlay.setAttribute('height', h);
                overlay.setAttribute('fill', 'rgba(0,0,0,0.5)');
                overlay.setAttribute('mask', 'url(#frameMask)');
                idFrameGuide.appendChild(overlay);

                // Green border frame
                const border = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                border.setAttribute('x', frameX);
                border.setAttribute('y', frameY);
                border.setAttribute('width', frameW);
                border.setAttribute('height', frameH);
                border.setAttribute('fill', 'none');
                border.setAttribute('stroke', '#22c55e');
                border.setAttribute('stroke-width', '3');
                border.setAttribute('rx', '8');
                idFrameGuide.appendChild(border);

                // Corner brackets
                const cornerLen = 24;
                const corners = [
                    { x: frameX, y: frameY }, // top-left
                    { x: frameX + frameW, y: frameY }, // top-right
                    { x: frameX, y: frameY + frameH }, // bottom-left
                    { x: frameX + frameW, y: frameY + frameH } // bottom-right
                ];

                corners.forEach((corner, idx) => {
                    const isRight = idx === 1 || idx === 3;
                    const isBottom = idx === 2 || idx === 3;

                    // Horizontal line
                    const h1 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    h1.setAttribute('x1', isRight ? corner.x - cornerLen : corner.x);
                    h1.setAttribute('y1', corner.y);
                    h1.setAttribute('x2', isRight ? corner.x : corner.x + cornerLen);
                    h1.setAttribute('y2', corner.y);
                    h1.setAttribute('stroke', '#22c55e');
                    h1.setAttribute('stroke-width', '3');
                    idFrameGuide.appendChild(h1);

                    // Vertical line
                    const v1 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    v1.setAttribute('x1', corner.x);
                    v1.setAttribute('y1', isBottom ? corner.y - cornerLen : corner.y);
                    v1.setAttribute('x2', corner.x);
                    v1.setAttribute('y2', isBottom ? corner.y : corner.y + cornerLen);
                    v1.setAttribute('stroke', '#22c55e');
                    v1.setAttribute('stroke-width', '3');
                    idFrameGuide.appendChild(v1);
                });

                    idFrameGuide.style.display = 'block';
                } catch (e) {
                    console.error('Frame drawing error:', e);
                }
            }

            try {
                drawIDFrame();
                window.addEventListener('resize', drawIDFrame);
            } catch (e) {
                console.error('Frame initialization error:', e);
            }

            startPhotoCameraBtn.classList.add('d-none');
            capturePhotoBtn.classList.remove('d-none');
            stopPhotoCameraBtn.classList.remove('d-none');

            // Show camera switch button if camera is running
            if (cameraSwitchBtn) {
                cameraSwitchBtn.classList.remove('d-none');
            }
        } catch (error) {
            console.error('ID Camera error:', error);
            let errorMessage = 'Unable to access camera. Check browser permissions.';

            if (error.name === 'NotAllowedError') {
                errorMessage = 'Camera permission denied. Please enable camera access in your browser settings.';
            } else if (error.name === 'NotFoundError') {
                errorMessage = 'No camera device found. Please check your device has a camera.';
            } else if (error.name === 'NotReadableError') {
                errorMessage = 'Camera is already in use by another application. Please close other apps and try again.';
            } else if (error.name === 'SecurityError') {
                errorMessage = 'Camera access is blocked by security settings. Please use HTTPS or check your browser security settings.';
            }

            alert(errorMessage);

            // Re-enable QR scanner if it was stopped
            startScannerBtn.disabled = false;
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

    // Helper function to crop image to frame area ONLY
    function cropImageToFrame(sourceCanvas) {
        const container = photoCameraFeed.parentElement;
        const containerW = container.offsetWidth;
        const containerH = container.offsetHeight;

        // Calculate frame dimensions (same as SVG frame drawing)
        const frameH = containerH * 0.65; // 65% of container height
        const frameW = frameH * 1.588; // ID ratio
        const frameX = (containerW - frameW) / 2;
        const frameY = (containerH - frameH) / 2;

        // Scale to canvas dimensions
        const scaleX = sourceCanvas.width / containerW;
        const scaleY = sourceCanvas.height / containerH;

        const cropX = Math.round(frameX * scaleX);
        const cropY = Math.round(frameY * scaleY);
        const cropW = Math.round(frameW * scaleX);
        const cropH = Math.round(frameH * scaleY);

        // Create cropped canvas
        const cropCanvas = document.createElement('canvas');
        cropCanvas.width = cropW;
        cropCanvas.height = cropH;

        const ctx = cropCanvas.getContext('2d');
        ctx.drawImage(sourceCanvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);

        return cropCanvas.toDataURL('image/jpeg', 0.95);
    }

    capturePhotoBtn.addEventListener('click', async function() {
        // If already captured, check if this is a delete action
        if ((capturingFrontPhoto && frontPhotoCaptured) || (!capturingFrontPhoto && backPhotoCaptured)) {
            // Delete photo
            if (capturingFrontPhoto) {
                // Delete front photo
                frontPhotoData.value = '';
                frontPhotoPreviewContainer.classList.add('d-none');
                frontPhotoCaptured = false;
                frontPhotoStatus.textContent = 'Pending';
                frontPhotoStatus.style.color = '#60a5fa';
                document.getElementById('frontPhotoIndicator').style.opacity = '0.5';
            } else {
                // Delete back photo
                backPhotoData.value = '';
                backPhotoPreviewContainer.classList.add('d-none');
                backPhotoCaptured = false;
                backPhotoStatus.textContent = 'Pending';
                backPhotoStatus.style.color = '#60a5fa';
                document.getElementById('backPhotoIndicator').style.opacity = '0.5';
            }

            capturePhotoBtn.textContent = 'Capture Photo';
            capturePhotoBtn.classList.remove('btn-warning');
            capturePhotoBtn.classList.add('btn-success');
            return;
        }

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

        // Get image data - crop to frame
        const croppedImageData = cropImageToFrame(photoCanvas);

        // Show flash effect
        const photoFlash = document.getElementById('photoFlash');
        photoFlash.style.display = 'block';
        photoFlash.classList.add('flashing');
        setTimeout(() => { photoFlash.style.display = 'none'; photoFlash.classList.remove('flashing'); }, 300);

        // Show checkmark with animation
        const photoCapturedCheck = document.getElementById('photoCapturedCheck');
        photoCapturedCheck.style.display = 'block';
        setTimeout(() => { photoCapturedCheck.style.display = 'none'; }, 600);

            if (capturingFrontPhoto) {
            // Capture front of ID
            frontPhotoData.value = croppedImageData;
            frontPhotoPreview.src = croppedImageData;
            frontPhotoPreviewContainer.classList.remove('d-none');
            frontPhotoCaptured = true;
            frontPhotoStatus.textContent = '✓ Captured';
            frontPhotoStatus.style.color = '#86efac';
            document.getElementById('frontPhotoIndicator').style.opacity = '1';
            document.getElementById('frontPhotoIndicator').style.borderColor = '#86efac';

            // Show subtle retake option and proceed button
            capturePhotoBtn.textContent = '✕ Retake';
            capturePhotoBtn.classList.remove('btn-success');
            capturePhotoBtn.classList.add('btn-outline-warning');
            capturePhotoBtn.style.fontSize = '12px';

            // Add "Proceed to Back Camera" button if it doesn't exist
            let proceedBtn = document.getElementById('proceedToBackBtn');
            if (!proceedBtn) {
                proceedBtn = document.createElement('button');
                proceedBtn.type = 'button';
                proceedBtn.id = 'proceedToBackBtn';
                proceedBtn.className = 'btn btn-success';
                proceedBtn.innerHTML = '<i class="fas fa-arrow-right"></i> Proceed to Back';
                proceedBtn.style.fontSize = '14px';
                proceedBtn.style.marginBottom = '8px';
                proceedBtn.addEventListener('click', function() {
                    capturePhotoBtn.classList.remove('btn-outline-warning');
                    capturePhotoBtn.classList.add('btn-success');
                    capturePhotoBtn.style.fontSize = '14px';
                    capturePhotoBtn.textContent = 'Capture Photo';
                    proceedBtn.remove();
                    capturingFrontPhoto = false;

                    // Update indicator
                    document.getElementById('backPhotoIndicator').style.opacity = '1';
                    document.getElementById('currentSideLabel').style.display = 'block';
                    document.getElementById('currentSideText').textContent = '📷 Capturing Back of ID - Please flip the card';
                    document.getElementById('currentSideText').style.color = '#90caf9';

                    // Stop current camera and start back camera
                    stopPhotoCameraBtn.click();

                    setTimeout(function() {
                        startPhotoCameraBtn.click();
                    }, 300);
                });

                // Insert proceed button after capture button
                capturePhotoBtn.parentNode.insertBefore(proceedBtn, capturePhotoBtn.nextSibling);
            }
        } else {
            // Capture back of ID
            backPhotoData.value = croppedImageData;
            backPhotoPreview.src = croppedImageData;
            backPhotoPreviewContainer.classList.remove('d-none');
            backPhotoCaptured = true;
            backPhotoStatus.textContent = '✓ Captured';
            backPhotoStatus.style.color = '#90caf9';
            document.getElementById('backPhotoIndicator').style.borderColor = '#90caf9';

            // Hide current side label
            document.getElementById('currentSideLabel').style.display = 'none';

            // All photos captured
            capturePhotoBtn.textContent = '✕ Delete Back Photo';
            capturePhotoBtn.classList.remove('btn-success');
            capturePhotoBtn.classList.add('btn-warning');
            stopPhotoCameraBtn.classList.add('d-none');
        }
    });

    function stopPhotoCamera() {
        if (photoCameraStream) {
            photoCameraStream.getTracks().forEach(track => track.stop());
            photoCameraStream = null;
        }

        // Properly clear the video element
        photoCameraFeed.srcObject = null;
        photoCameraFeed.style.display = 'none';
        noCameraMsg.style.display = 'block';

        // Hide frame guide
        const idFrameGuide = document.getElementById('idFrameGuide');
        const frameGuideLabel = document.getElementById('frameGuideLabel');
        idFrameGuide.style.display = 'none';
        frameGuideLabel.style.display = 'none';

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
            resetPhotos();
        });
    }

    function resetPhotos() {
        // Reset photo states
        frontPhotoCaptured = false;
        backPhotoCaptured = false;
        capturingFrontPhoto = true;
        currentFacingMode = 'environment';

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
        document.getElementById('frontPhotoIndicator').style.opacity = '0.5';
        document.getElementById('frontPhotoIndicator').style.borderColor = '#22c55e';
        document.getElementById('backPhotoIndicator').style.opacity = '0.5';
        document.getElementById('backPhotoIndicator').style.borderColor = '#64b5f6';

        // Hide current side label
        document.getElementById('currentSideLabel').style.display = 'none';

        // Reset capture button text and style
        capturePhotoBtn.textContent = 'Capture Photo';
        capturePhotoBtn.classList.remove('btn-warning');
        capturePhotoBtn.classList.add('btn-success');
        capturePhotoBtn.disabled = false;

        // Show photo capture section
        photoCaptureSection.style.display = '';

        // Restart camera
        startPhotoCameraBtn.click();
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
