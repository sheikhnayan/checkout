<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form W-9 - Tax Withholding Information</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .w9-form {
            background: white;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }

        .w9-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .w9-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .w9-subtitle {
            font-size: 10px;
        }

        .form-line {
            margin-bottom: 18px;
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 15px;
            align-items: center;
        }

        .line-label {
            font-weight: bold;
            font-size: 10px;
            text-align: right;
            padding-right: 10px;
        }

        .form-input {
            border: none;
            border-bottom: 1px solid #000;
            padding: 4px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            width: 100%;
        }

        .form-input:focus {
            outline: none;
            background: #fffacd;
        }

        .checkbox-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .checkbox-item label {
            font-size: 10px;
            cursor: pointer;
            flex: 1;
        }

        .checkbox-item input[type="radio"] {
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        .section-header {
            font-weight: bold;
            font-size: 11px;
            margin-top: 20px;
            margin-bottom: 10px;
            padding: 8px 0;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .address-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .city-state-zip {
            display: grid;
            grid-template-columns: 2fr 100px 80px;
            gap: 10px;
        }

        .ssn-ein-group {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .ssn-boxes, .ein-boxes {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .ssn-box, .ein-box {
            width: 30px;
            height: 25px;
            border: 1px solid #000;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            padding: 2px;
        }

        .certification-section {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #999;
            background: #f9f9f9;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            display: inline-block;
            margin-top: 10px;
        }

        .date-line {
            border-bottom: 1px solid #000;
            width: 100px;
            display: inline-block;
            margin-left: 50px;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .sidebar-card {
            background: white;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .sidebar-title {
            font-weight: bold;
            font-size: 13px;
            color: #0066cc;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 12px;
        }

        .required {
            color: #dc3545;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 12px;
            border-radius: 4px;
            font-size: 11px;
            color: #0066cc;
            margin-bottom: 15px;
        }

        .error-box {
            background: #ffe7e7;
            border-left: 4px solid #dc3545;
            padding: 12px;
            border-radius: 4px;
            font-size: 11px;
            color: #dc3545;
            margin-bottom: 15px;
            display: none;
        }

        .file-upload {
            margin-bottom: 15px;
        }

        .file-upload-area {
            border: 2px dashed #0066cc;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            background: #f9f9ff;
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 11px 16px;
            font-size: 13px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .btn-primary {
            background: #0066cc;
            color: white;
        }

        .btn-primary:hover {
            background: #0052a3;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 10px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0066cc;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media print {
            .sidebar { display: none; }
            body { background: white; padding: 0; }
            .w9-form { box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- W-9 Form -->
    <form id="w9HtmlForm" class="w9-form">
        <!-- Header -->
        <div class="w9-header">
            <div class="w9-subtitle">Form</div>
            <div class="w9-title">W-9</div>
            <div class="w9-subtitle">Request for Taxpayer Identification Number and Certification</div>
            <div class="w9-subtitle" style="margin-top: 8px;">✓ Keep for your records</div>
        </div>

        <!-- Line 1: Name -->
        <div class="form-line">
            <div class="line-label">Line 1</div>
            <div>
                <input type="text" id="line1Name" class="form-input" placeholder="Name of entity/individual">
                <div style="font-size: 9px; margin-top: 3px; color: #666;">Name (as shown on your income tax return)</div>
            </div>
        </div>

        <!-- Line 2: Business Name -->
        <div class="form-line">
            <div class="line-label">Line 2</div>
            <div>
                <input type="text" id="line2Business" class="form-input" placeholder="Business name/DBA">
                <div style="font-size: 9px; margin-top: 3px; color: #666;">Business name/disregarded entity name, if different from above</div>
            </div>
        </div>

        <!-- Line 3a: Tax Classification -->
        <div class="form-line">
            <div class="line-label">Line 3a</div>
            <div>
                <div style="font-size: 9px; margin-bottom: 8px;">Tax classification. Check the appropriate box:</div>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="radio" id="tax_individual" name="tax_classification" value="individual">
                        <label for="tax_individual">Individual/sole proprietor</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" id="tax_ccorp" name="tax_classification" value="c_corporation">
                        <label for="tax_ccorp">C Corporation</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" id="tax_scorp" name="tax_classification" value="s_corporation">
                        <label for="tax_scorp">S Corporation</label>
                    </div>
                </div>
                <div class="checkbox-group" style="margin-top: 8px;">
                    <div class="checkbox-item">
                        <input type="radio" id="tax_partnership" name="tax_classification" value="partnership">
                        <label for="tax_partnership">Partnership</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" id="tax_trust" name="tax_classification" value="trust_estate">
                        <label for="tax_trust">Trust/estate</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" id="tax_llc" name="tax_classification" value="llc">
                        <label for="tax_llc">LLC</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line 4: Exemption Codes -->
        <div class="form-line">
            <div class="line-label">Line 4</div>
            <div class="two-column">
                <div>
                    <input type="text" id="line4Exempt" class="form-input" placeholder="Exemption code (if any)">
                    <div style="font-size: 9px; margin-top: 3px; color: #666;">Exemption from FATCA reporting code (if any)</div>
                </div>
            </div>
        </div>

        <!-- Line 5: Tax ID -->
        <div class="form-line">
            <div class="line-label">Line 5</div>
            <div>
                <div style="font-size: 9px; margin-bottom: 10px;">Enter your TIN in the appropriate box. The TIN provided must match the name given on Line 1 above.</div>
                <div class="ssn-ein-group">
                    <div>
                        <div style="font-size: 9px; margin-bottom: 5px;">
                            <input type="radio" id="ssn_select" name="tin_type" value="ssn" checked>
                            <label for="ssn_select">Social security number</label>
                        </div>
                        <div class="ssn-boxes">
                            <input type="text" id="ssn_part1" class="ssn-box" maxlength="3" placeholder="___">
                            <span style="margin: 0 2px;">-</span>
                            <input type="text" id="ssn_part2" class="ssn-box" maxlength="2" placeholder="__">
                            <span style="margin: 0 2px;">-</span>
                            <input type="text" id="ssn_part3" class="ssn-box" maxlength="4" placeholder="____">
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 9px; margin-bottom: 5px;">
                            <input type="radio" id="ein_select" name="tin_type" value="ein">
                            <label for="ein_select">Employer identification number</label>
                        </div>
                        <div class="ein-boxes">
                            <input type="text" id="ein_part1" class="ein-box" maxlength="2" placeholder="__">
                            <span style="margin: 0 2px;">-</span>
                            <input type="text" id="ein_part2" class="ein-box" maxlength="7" placeholder="_______">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line 6: Address -->
        <div class="form-line">
            <div class="line-label">Line 6</div>
            <div>
                <input type="text" id="line6Address" class="form-input" placeholder="Street address">
                <div style="font-size: 9px; margin-top: 3px; color: #666;">Address (number, street, and apt. or suite no.)</div>
            </div>
        </div>

        <!-- Line 7: City, State, ZIP -->
        <div class="form-line">
            <div class="line-label">Line 7</div>
            <div>
                <div class="city-state-zip">
                    <input type="text" id="line7City" class="form-input" placeholder="City">
                    <input type="text" id="line7State" class="form-input" placeholder="State" maxlength="2">
                    <input type="text" id="line7Zip" class="form-input" placeholder="ZIP">
                </div>
                <div style="font-size: 9px; margin-top: 3px; color: #666;">City, state, and ZIP code</div>
            </div>
        </div>

        <!-- Line 8: Account Numbers -->
        <div class="form-line">
            <div class="line-label">Line 8</div>
            <div>
                <input type="text" id="line8Account" class="form-input" placeholder="Account number(s) (optional)">
                <div style="font-size: 9px; margin-top: 3px; color: #666;">List account number(s) here (optional)</div>
            </div>
        </div>

        <!-- Certification -->
        <div class="certification-section">
            <div style="font-weight: bold; font-size: 11px; margin-bottom: 10px;">Certification</div>
            <div style="font-size: 10px; line-height: 1.6; margin-bottom: 15px;">
                Under penalties of perjury, I certify that:
                <br/>1. The number shown on this form is my correct taxpayer identification number (or I am waiting for a number to be issued to me), and
                <br/>2. I am not subject to backup withholding because: (a) I have not been notified by the IRS that I am subject to backup withholding as a result of a failure to report all interest or dividends, or (b) the IRS has notified me that I am no longer subject to backup withholding, and
                <br/>3. I am a U.S. citizen or other U.S. person.
            </div>

            <div style="margin-bottom: 15px;">
                <input type="checkbox" id="cert_checkbox" name="certification">
                <label for="cert_checkbox" style="display: inline; font-size: 10px;">
                    I certify that the information provided is correct and complete
                </label>
            </div>

            <div style="font-size: 9px;">
                <div style="margin-bottom: 15px;">
                    Signature of U.S. person: <span class="signature-line"></span> Date: <span class="date-line"></span>
                </div>
            </div>
        </div>
    </form>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-card">
            <div class="error-box" id="errorBox"></div>

            <div class="sidebar-title">📸 Government ID Verification</div>

            <div class="form-group">
                <label>Type of ID <span class="required">*</span></label>
                <select id="idDocumentType" required>
                    <option value="">-- Select ID Type --</option>
                    <option value="driver_license">Driver's License</option>
                    <option value="passport">Passport</option>
                    <option value="state_id">State ID Card</option>
                    <option value="other">Other Government ID</option>
                </select>
            </div>

            <div class="info-box">
                ⚠️ Upload clear images of BOTH sides of your government-issued ID
            </div>

            <div class="file-upload">
                <label>Front of ID <span class="required">*</span></label>
                <div class="file-upload-area" onclick="document.getElementById('idFront').click();">
                    <div>📤 Click to upload</div>
                </div>
                <input type="file" id="idFront" accept="image/jpeg,image/png,image/jpg" required>
                <div id="idFrontPreview"></div>
                <div style="font-size: 9px; margin-top: 5px; color: #999;">✓ JPG or PNG | ✓ Max 5 MB</div>
            </div>

            <div class="file-upload">
                <label>Back of ID <span class="required">*</span></label>
                <div class="file-upload-area" onclick="document.getElementById('idBack').click();">
                    <div>📤 Click to upload</div>
                </div>
                <input type="file" id="idBack" accept="image/jpeg,image/png,image/jpg" required>
                <div id="idBackPreview"></div>
                <div style="font-size: 9px; margin-top: 5px; color: #999;">✓ JPG or PNG | ✓ Max 5 MB</div>
            </div>

            <div class="button-group">
                <button type="button" id="submitBtn" onclick="submitForm()" class="btn btn-primary">
                    ✓ Submit W-9 Form
                </button>
                <div class="loading" id="loader">
                    <div class="spinner"></div>
                    <p style="font-size: 12px; color: #666;">Processing your submission...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle file uploads
document.getElementById('idFront').addEventListener('change', function(e) {
    previewFile(e.target, 'idFrontPreview');
});

document.getElementById('idBack').addEventListener('change', function(e) {
    previewFile(e.target, 'idBackPreview');
});

function previewFile(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (file) {
        if (file.size > 5242880) {
            preview.innerHTML = '<div style="color: red; font-size: 12px;">❌ File exceeds 5 MB limit</div>';
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '150px';
            img.style.borderRadius = '4px';
            img.style.marginTop = '8px';
            img.style.border = '1px solid #ccc';
            preview.appendChild(img);

            const name = document.createElement('div');
            name.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            name.style.fontSize = '11px';
            name.style.color = '#0066cc';
            name.style.marginTop = '5px';
            preview.appendChild(name);
        };
        reader.readAsDataURL(file);
    }
}

async function submitForm() {
    const errorBox = document.getElementById('errorBox');
    errorBox.style.display = 'none';
    errorBox.innerHTML = '';

    // Get form values
    const line1Name = document.getElementById('line1Name').value.trim();
    const line7City = document.getElementById('line7City').value.trim();
    const line7State = document.getElementById('line7State').value.trim();
    const line7Zip = document.getElementById('line7Zip').value.trim();
    const line6Address = document.getElementById('line6Address').value.trim();
    const taxClassification = document.querySelector('input[name="tax_classification"]:checked')?.value || '';
    const tinType = document.querySelector('input[name="tin_type"]:checked')?.value || 'ssn';

    let taxId = '';
    if (tinType === 'ssn') {
        const p1 = document.getElementById('ssn_part1').value;
        const p2 = document.getElementById('ssn_part2').value;
        const p3 = document.getElementById('ssn_part3').value;
        taxId = p1 + '-' + p2 + '-' + p3;
    } else {
        const p1 = document.getElementById('ein_part1').value;
        const p2 = document.getElementById('ein_part2').value;
        taxId = p1 + '-' + p2;
    }

    const idDocumentType = document.getElementById('idDocumentType').value;
    const idFront = document.getElementById('idFront').files[0];
    const idBack = document.getElementById('idBack').files[0];
    const certification = document.getElementById('cert_checkbox').checked;

    // Validate
    const errors = [];
    if (!line1Name) errors.push('✗ Full Name (Line 1) is required');
    if (!taxId || taxId.includes('--')) errors.push('✗ Tax ID (Line 5) is required');
    if (!idDocumentType) errors.push('✗ ID Type is required');
    if (!idFront) errors.push('✗ Front of ID is required');
    if (!idBack) errors.push('✗ Back of ID is required');
    if (!certification) errors.push('✗ You must certify the information');

    if (errors.length > 0) {
        errorBox.innerHTML = '<strong>Please correct these errors:</strong><br>' + errors.join('<br>');
        errorBox.style.display = 'block';
        window.scrollTo(0, 0);
        return;
    }

    // Show loading
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('loader').style.display = 'block';

    // Prepare form data
    const formData = new FormData();
    formData.append('id_document_type', idDocumentType);
    formData.append('id_front_image', idFront);
    formData.append('id_back_image', idBack);
    formData.append('certification_signed', true);

    // Add all W-9 fields
    const w9Data = {
        'line1_name': line1Name,
        'line2_business': document.getElementById('line2Business').value.trim(),
        'line3a_tax_classification': taxClassification,
        'line4_exempt_code': document.getElementById('line4Exempt').value.trim(),
        'line5_tin_type': tinType,
        'line5_tax_id': taxId,
        'line6_address': line6Address,
        'line7_city': line7City,
        'line7_state': line7State,
        'line7_zip': line7Zip,
        'line8_account': document.getElementById('line8Account').value.trim(),
    };

    formData.append('pdf_form_data', JSON.stringify(w9Data));

    try {
        const response = await fetch('{{ route("w9.store", $token) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        const contentType = response.headers.get('content-type');
        let data;

        if (contentType && contentType.includes('application/json')) {
            data = await response.json();
        } else {
            throw new Error('Invalid server response');
        }

        if (response.ok) {
            setTimeout(() => {
                window.location.href = '{{ route("w9.thank-you") }}';
            }, 500);
        } else {
            const errorMsg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Failed to submit form');
            errorBox.innerHTML = '<strong>Error:</strong><br>' + errorMsg;
            errorBox.style.display = 'block';
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('loader').style.display = 'none';
            window.scrollTo(0, 0);
        }
    } catch (error) {
        console.error('Submission error:', error);
        errorBox.innerHTML = '<strong>Error:</strong> ' + error.message + '<br><small>Please check your internet connection and try again.</small>';
        errorBox.style.display = 'block';
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('loader').style.display = 'none';
        window.scrollTo(0, 0);
    }
}
</script>

</body>
</html>
