<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form W-9 - Fill Directly in PDF</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #0066cc;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .header p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .content {
                grid-template-columns: 1fr;
            }
        }

        .pdf-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            overflow: auto;
            max-height: 85vh;
        }

        #pdfViewer {
            width: 100%;
            max-width: 100%;
            border: 2px solid #ddd;
            border-radius: 6px;
            display: block;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .sidebar-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .sidebar-title {
            font-weight: bold;
            font-size: 14px;
            color: #0066cc;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            color: #333;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 12px;
            font-family: Arial, sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0066cc;
            background: #f9f9ff;
        }

        .file-upload {
            position: relative;
            margin-bottom: 15px;
        }

        .file-upload label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .file-upload-area {
            border: 2px dashed #0066cc;
            border-radius: 4px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            background: #f9f9ff;
            transition: all 0.3s;
        }

        .file-upload-area:hover {
            background: #f0f0ff;
            border-color: #003d99;
        }

        .file-upload-area input[type="file"] {
            display: none;
        }

        .upload-text {
            font-size: 11px;
            color: #666;
        }

        .file-name {
            font-size: 10px;
            color: #0066cc;
            margin-top: 5px;
            font-weight: bold;
        }

        .preview-img {
            width: 100%;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 8px;
            object-fit: cover;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            padding: 10px 16px;
            font-size: 13px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            width: 100%;
        }

        .btn-primary {
            background: #0066cc;
            color: white;
        }

        .btn-primary:hover {
            background: #0052a3;
            box-shadow: 0 4px 8px rgba(0, 102, 204, 0.3);
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 12px;
            border-radius: 4px;
            font-size: 11px;
            color: #0066cc;
            line-height: 1.5;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: none;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        .error-message {
            color: #dc3545;
            font-size: 10px;
            margin-top: 4px;
            display: none;
        }

        .loading {
            text-align: center;
            padding: 20px;
            display: none;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0066cc;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }

        .checkbox-item input[type="radio"] {
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        .checkbox-item label {
            cursor: pointer;
            margin: 0;
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📄 Form W-9 - Fill Directly in the PDF</h1>
            <p>Fill out the form fields directly in the PDF below. Complete all required fields, upload your government-issued ID images, and submit.</p>
        </div>

        <div class="success-message" id="successMsg">
            ✓ Your Form W-9 has been submitted successfully!
        </div>

        <div class="content">
            <!-- PDF Viewer -->
            <div class="pdf-container">
                <embed id="pdfViewer" type="application/pdf" src="{{ asset('storage/w9-template/fw9_template.pdf') }}" />
            </div>

            <!-- Sidebar Form -->
            <div class="sidebar">
                <div class="sidebar-card">
                    <div class="sidebar-title">Required Information</div>

                    <div class="info-box">
                        ℹ️ Fill the PDF form fields above. This info helps capture your data.
                    </div>

                    <div class="form-group">
                        <label>Full Name <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="fullName" placeholder="Your full name" required>
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label>Business Name (if different)</label>
                        <input type="text" id="businessName" placeholder="Business DBA">
                    </div>

                    <div class="form-group">
                        <label>Tax Classification <span style="color: #dc3545;">*</span></label>
                        <select id="taxClassification" required>
                            <option value="">-- Select --</option>
                            <option value="individual">Individual/Sole Proprietor</option>
                            <option value="c_corporation">C Corporation</option>
                            <option value="s_corporation">S Corporation</option>
                            <option value="partnership">Partnership</option>
                            <option value="trust_estate">Trust/Estate</option>
                            <option value="llc_c">LLC - C Corp</option>
                            <option value="llc_s">LLC - S Corp</option>
                            <option value="llc_i">LLC - Individual</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label>Tax ID Type <span style="color: #dc3545;">*</span></label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="radio" id="ssn" name="taxIdType" value="ssn" required>
                                <label for="ssn">SSN</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="radio" id="ein" name="taxIdType" value="ein">
                                <label for="ein">EIN</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tax ID Number <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="taxIdNumber" placeholder="XXX-XX-XXXX or XX-XXXXXXX" required>
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label>Street Address <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="streetAddress" placeholder="Street address" required>
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label>City <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="city" placeholder="City" required>
                        <span class="error-message"></span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label>State <span style="color: #dc3545;">*</span></label>
                            <input type="text" id="state" placeholder="State" maxlength="2" required style="text-transform: uppercase;">
                            <span class="error-message"></span>
                        </div>
                        <div class="form-group">
                            <label>ZIP <span style="color: #dc3545;">*</span></label>
                            <input type="text" id="zipCode" placeholder="ZIP" required>
                            <span class="error-message"></span>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card">
                    <div class="sidebar-title">Government ID Upload</div>

                    <div class="file-upload">
                        <label>Front of ID <span style="color: #dc3545;">*</span></label>
                        <div class="file-upload-area" onclick="document.getElementById('idFront').click();">
                            <div class="upload-text">📤 Click to upload</div>
                        </div>
                        <input type="file" id="idFront" accept="image/jpeg,image/png,image/jpg" required>
                        <div id="idFrontPreview"></div>
                        <span class="error-message"></span>
                    </div>

                    <div class="file-upload">
                        <label>Back of ID <span style="color: #dc3545;">*</span></label>
                        <div class="file-upload-area" onclick="document.getElementById('idBack').click();">
                            <div class="upload-text">📤 Click to upload</div>
                        </div>
                        <input type="file" id="idBack" accept="image/jpeg,image/png,image/jpg" required>
                        <div id="idBackPreview"></div>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="sidebar-card">
                    <div class="sidebar-title">Certification</div>

                    <div class="checkbox-item" style="margin-bottom: 15px;">
                        <input type="checkbox" id="certification" required>
                        <label for="certification" style="font-weight: 600; margin: 0;">I certify this information is true and complete</label>
                    </div>

                    <div class="button-group">
                        <button type="button" class="btn btn-primary" id="submitBtn" onclick="submitForm()">
                            ✓ Submit W-9 Form
                        </button>
                    </div>

                    <div class="loading" id="loader">
                        <div class="spinner"></div>
                        <p style="font-size: 12px; color: #666;">Processing...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File preview
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
                    preview.innerHTML = '<span class="error-message" style="display: block;">File exceeds 5 MB limit</span>';
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const html = '<img src="' + e.target.result + '" class="preview-img" alt="Preview">' +
                                '<div class="file-name">✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)</div>';
                    preview.innerHTML = html;
                };
                reader.readAsDataURL(file);
            }
        }

        // Form submission
        async function submitForm() {
            const fullName = document.getElementById('fullName').value;
            const businessName = document.getElementById('businessName').value;
            const taxClassification = document.getElementById('taxClassification').value;
            const taxIdType = document.querySelector('input[name="taxIdType"]:checked')?.value;
            const taxIdNumber = document.getElementById('taxIdNumber').value;
            const streetAddress = document.getElementById('streetAddress').value;
            const city = document.getElementById('city').value;
            const state = document.getElementById('state').value;
            const zipCode = document.getElementById('zipCode').value;
            const idFront = document.getElementById('idFront').files[0];
            const idBack = document.getElementById('idBack').files[0];
            const certification = document.getElementById('certification').checked;

            // Validation
            const errors = [];
            if (!fullName) errors.push('Full name is required');
            if (!taxClassification) errors.push('Tax classification is required');
            if (!taxIdType) errors.push('Tax ID type is required');
            if (!taxIdNumber) errors.push('Tax ID number is required');
            if (!streetAddress) errors.push('Street address is required');
            if (!city) errors.push('City is required');
            if (!state) errors.push('State is required');
            if (!zipCode) errors.push('ZIP code is required');
            if (!idFront) errors.push('Front of ID is required');
            if (!idBack) errors.push('Back of ID is required');
            if (!certification) errors.push('Certification is required');

            if (errors.length > 0) {
                alert('Please correct the following errors:\n\n' + errors.join('\n'));
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('full_name', fullName);
            formData.append('business_name', businessName);
            formData.append('tax_classification', taxClassification);
            formData.append('tax_id_type', taxIdType);
            formData.append('tax_id_number', taxIdNumber);
            formData.append('street_address', streetAddress);
            formData.append('city', city);
            formData.append('state', state.toUpperCase());
            formData.append('zip_code', zipCode);
            formData.append('id_front_image', idFront);
            formData.append('id_back_image', idBack);
            formData.append('certification_signed', true);

            // Submit
            document.getElementById('submitBtn').style.display = 'none';
            document.getElementById('loader').style.display = 'block';

            try {
                const response = await fetch('{{ route("w9.store", $token) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: formData
                });

                if (response.ok) {
                    document.getElementById('loader').style.display = 'none';
                    document.getElementById('successMsg').style.display = 'block';
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Failed to submit form'));
                    document.getElementById('submitBtn').style.display = 'block';
                    document.getElementById('loader').style.display = 'none';
                }
            } catch (error) {
                alert('Error: ' + error.message);
                document.getElementById('submitBtn').style.display = 'block';
                document.getElementById('loader').style.display = 'none';
            }
        }
    </script>
</body>
</html>
