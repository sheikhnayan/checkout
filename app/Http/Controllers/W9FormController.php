<?php

namespace App\Http\Controllers;

use App\Models\W9Form;
use App\Models\Affiliate;
use App\Models\Entertainer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi;

class W9FormController extends Controller
{
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/jpg'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];
    private const MAX_FILE_SIZE = 5242880; // 5MB in bytes

    public function show($token)
    {
        try {
            $data = json_decode(base64_decode($token), true);
            if (!$data || !isset($data['type'], $data['id'])) {
                abort(404, 'Invalid W-9 form link');
            }

            $type = $data['type'];
            $id = $data['id'];

            if ($type === 'affiliate') {
                $affiliate = Affiliate::findOrFail($id);
                $w9Form = W9Form::where('affiliate_id', $id)->first();

                // Check if already submitted
                if ($w9Form && $w9Form->status === 'submitted') {
                    return view('w9.already-submitted', [
                        'type' => 'affiliate',
                        'name' => $affiliate->display_name ?: $affiliate->user->name,
                    ]);
                }

                return view('w9.form-fillable-pdf', [
                    'type' => 'affiliate',
                    'id' => $id,
                    'name' => $affiliate->display_name ?: $affiliate->user->name,
                    'w9Form' => $w9Form,
                    'token' => $token,
                ]);
            } elseif ($type === 'entertainer') {
                $entertainer = Entertainer::findOrFail($id);
                $w9Form = W9Form::where('entertainer_id', $id)->first();

                // Check if already submitted
                if ($w9Form && $w9Form->status === 'submitted') {
                    return view('w9.already-submitted', [
                        'type' => 'entertainer',
                        'name' => $entertainer->display_name ?: $entertainer->user->name,
                    ]);
                }

                return view('w9.form-fillable-pdf', [
                    'type' => 'entertainer',
                    'id' => $id,
                    'name' => $entertainer->display_name ?: $entertainer->user->name,
                    'w9Form' => $w9Form,
                    'token' => $token,
                ]);
            }

            abort(400, 'Invalid type');
        } catch (\Exception $e) {
            abort(404, 'Invalid W-9 form link');
        }
    }

    public function store(Request $request, $token)
    {
        try {
            $data = json_decode(base64_decode($token), true);
            if (!$data || !isset($data['type'], $data['id'])) {
                return response()->json(['error' => 'Invalid token'], 400);
            }

            $type = $data['type'];
            $id = $data['id'];
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        try {
            $validated = $request->validate([
                'full_name' => 'nullable|string|max:255',
                'business_name' => 'nullable|string|max:255',
                'tax_classification' => 'nullable|in:individual,c_corporation,s_corporation,partnership,trust_estate,limited_liability_company_c,limited_liability_company_s,limited_liability_company_individual,sole_proprietor,other',
                'tax_classification_other' => 'nullable|string|max:255',
                'tax_id_type' => 'nullable|in:ssn,ein',
                'tax_id_number' => 'nullable|string|max:20',
                'street_address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|size:2',
                'zip_code' => 'nullable|string|max:10',
                'account_numbers' => 'nullable|string|max:255',
                'requester_name' => 'nullable|string|max:255',
                'requester_phone' => 'nullable|string|max:20',
                'requester_email' => 'nullable|email|max:255',
                'exempt_payee_code' => 'nullable|string|max:50',
                'fatca_exemption_code' => 'nullable|string|max:50',
                'id_document_type' => 'required|in:driver_license,passport,state_id,other',
                'id_front_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'id_back_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'certification_signed' => 'required|accepted',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        try {
            if ($type === 'affiliate') {
                $w9Form = W9Form::firstOrCreate(['affiliate_id' => $id], ['type' => 'affiliate']);
            } else {
                $w9Form = W9Form::firstOrCreate(['entertainer_id' => $id], ['type' => 'entertainer']);
            }

            if ($request->hasFile('id_front_image')) {
                $file = $request->file('id_front_image');
                $path = $file->store('w9-documents/id-front', 'public');
                $validated['id_front_image'] = $path;
            }

            if ($request->hasFile('id_back_image')) {
                $file = $request->file('id_back_image');
                $path = $file->store('w9-documents/id-back', 'public');
                $validated['id_back_image'] = $path;
            }

            $validated['certification_signed'] = true;
            $validated['certification_date'] = now();
            $validated['certification_ip'] = $request->ip();
            $validated['status'] = 'submitted';

            $w9Form->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'W-9 form submitted successfully. Your submission is under review.',
                'redirect' => route('w9.thank-you'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while processing your submission: ' . $e->getMessage()
            ], 500);
        }
    }

    public function thankYou()
    {
        return view('w9.thank-you', [
            'submissionId' => 'W9-' . strtoupper(uniqid()),
        ]);
    }

    public function viewModal($id)
    {
        $w9Form = W9Form::with(['affiliate', 'entertainer', 'reviewedBy'])->findOrFail($id);

        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('w9.admin-modal', ['w9Form' => $w9Form]);
    }

    public function downloadPdf($id)
    {
        $w9Form = W9Form::with(['affiliate', 'entertainer'])->findOrFail($id);

        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        try {
            $filledPdf = $this->generateFilledW9PDF($w9Form);

            $filename = 'W-9_' . preg_replace('/[^a-z0-9]/i', '_', $w9Form->full_name) . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            return response()->download($filledPdf, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            abort(500, 'Error generating PDF: ' . $e->getMessage());
        }
    }

    private function generateFilledW9PDF($w9Form)
    {
        $pdf = new Fpdi();
        $templatePath = storage_path('app/public/w9-template/fw9_template.pdf');

        if (!file_exists($templatePath)) {
            throw new \Exception('W-9 template PDF not found');
        }

        $pageCount = $pdf->setSourceFile($templatePath);

        for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
            $templateId = $pdf->importPage($pageNum);
            $pdf->addPage();
            $pdf->useTemplate($templateId, 0, 0);

            if ($pageNum === 1) {
                $this->fillFirstPage($pdf, $w9Form);
            } elseif ($pageNum === 2) {
                $this->fillSecondPage($pdf, $w9Form);
            }
        }

        $outputPath = storage_path('app/temp-pdfs/' . uniqid('w9_') . '.pdf');
        @mkdir(dirname($outputPath), 0755, true);
        $pdf->output('F', $outputPath);

        return $outputPath;
    }

    private function fillFirstPage($pdf, $w9Form)
    {
        $pdf->SetFont('Helvetica', '', 11);
        $pdf->SetTextColor(0, 0, 0);

        // Page 1 Form Field Positions (approximate coordinates from PDF)

        // Line 1: Name of entity/individual
        $pdf->SetXY(36, 73.5);
        $pdf->Cell(120, 5, substr($w9Form->full_name, 0, 60), 0, 1, 'L');

        // Line 2: Business name/disregarded entity name
        $pdf->SetXY(36, 84);
        $pdf->Cell(120, 5, substr($w9Form->business_name ?: '', 0, 60), 0, 1, 'L');

        // Line 3a: Tax Classification - Mark the checkbox
        if ($w9Form->tax_classification) {
            $checkPositions = [
                'individual' => [19.5, 101],
                'c_corporation' => [58, 101],
                's_corporation' => [98, 101],
                'partnership' => [135, 101],
                'trust_estate' => [172, 101],
            ];

            if (isset($checkPositions[$w9Form->tax_classification])) {
                [$x, $y] = $checkPositions[$w9Form->tax_classification];
                $pdf->SetFont('Helvetica', 'B', 14);
                $pdf->SetXY($x, $y - 0.5);
                $pdf->Cell(4, 4, '✓', 0, 0, 'L');
            }

            // If LLC, add tax classification
            if (strpos($w9Form->tax_classification, 'limited_liability_company') === 0) {
                $pdf->SetFont('Helvetica', '', 11);
                $code = str_ends_with($w9Form->tax_classification, '_c') ? 'C' : (str_ends_with($w9Form->tax_classification, '_s') ? 'S' : 'P');
                $pdf->SetXY(98, 109);
                $pdf->Cell(20, 5, $code, 0, 1, 'L');
            }
        }

        // Line 3b: Foreign partners checkbox
        // Not checked in our form, can be left blank

        // Line 4: Exempt payee code
        $pdf->SetXY(142, 129);
        $pdf->Cell(35, 5, substr($w9Form->exempt_payee_code ?: '', 0, 15), 0, 1, 'L');

        // Line 4: FATCA exemption code
        $pdf->SetXY(142, 142);
        $pdf->Cell(35, 5, substr($w9Form->fatca_exemption_code ?: '', 0, 15), 0, 1, 'L');

        // Line 5: Address (street)
        $pdf->SetXY(36, 156);
        $pdf->Cell(120, 5, substr($w9Form->street_address, 0, 60), 0, 1, 'L');

        // Line 6: City, State, ZIP
        $pdf->SetXY(36, 169);
        $pdf->Cell(80, 5, substr($w9Form->city, 0, 40), 0, 0, 'L');
        $pdf->SetXY(120, 169);
        $pdf->Cell(10, 5, strtoupper($w9Form->state), 0, 0, 'L');
        $pdf->SetXY(145, 169);
        $pdf->Cell(20, 5, $w9Form->zip_code, 0, 1, 'L');

        // Line 7: Account numbers
        $pdf->SetXY(36, 182);
        $pdf->Cell(120, 5, substr($w9Form->account_numbers ?: '', 0, 60), 0, 1, 'L');

        // Requester info (optional field)
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetXY(142, 182);
        $pdf->Cell(45, 4, substr($w9Form->requester_name ?: '', 0, 30), 0, 1, 'L');

        // Part I: TIN
        $pdf->SetFont('Helvetica', '', 11);

        // SSN or EIN selection
        if ($w9Form->tax_id_type === 'ssn') {
            $pdf->SetFont('Helvetica', 'B', 14);
            $pdf->SetXY(92, 210);
            $pdf->Cell(4, 4, '✓', 0, 0, 'L');
        } elseif ($w9Form->tax_id_type === 'ein') {
            $pdf->SetFont('Helvetica', 'B', 14);
            $pdf->SetXY(92, 226);
            $pdf->Cell(4, 4, '✓', 0, 0, 'L');
        }

        // TIN Number formatting
        $pdf->SetFont('Helvetica', '', 11);
        $tidFormatted = $this->formatTaxIdForPDF($w9Form->tax_id_number, $w9Form->tax_id_type);

        if ($w9Form->tax_id_type === 'ssn') {
            // SSN format XXX-XX-XXXX (fill in separate boxes)
            $parts = explode('-', $tidFormatted);
            if (count($parts) === 3) {
                $pdf->SetXY(65, 214);
                $pdf->Cell(12, 4, $parts[0], 0, 0, 'C');
                $pdf->SetXY(82, 214);
                $pdf->Cell(8, 4, $parts[1], 0, 0, 'C');
                $pdf->SetXY(95, 214);
                $pdf->Cell(12, 4, $parts[2], 0, 0, 'C');
            }
        } else {
            // EIN format XX-XXXXXXX
            $parts = explode('-', $tidFormatted);
            if (count($parts) === 2) {
                $pdf->SetXY(65, 230);
                $pdf->Cell(8, 4, $parts[0], 0, 0, 'C');
                $pdf->SetXY(78, 230);
                $pdf->Cell(18, 4, $parts[1], 0, 0, 'C');
            }
        }

        // Part II: Certification signature area
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetXY(36, 270);
        $pdf->Cell(80, 4, 'Digital Submission via CartVIP', 0, 1, 'L');

        $pdf->SetXY(152, 270);
        $pdf->Cell(40, 4, $w9Form->certification_date ? $w9Form->certification_date->format('m/d/Y') : date('m/d/Y'), 0, 1, 'L');
    }

    private function fillSecondPage($pdf, $w9Form)
    {
        // Page 2 can include ID verification info if needed
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(80, 80, 80);

        $pdf->SetXY(20, 30);
        $pdf->Cell(0, 5, 'ADDITIONAL INFORMATION (CartVIP Form Submission)', 0, 1, 'L');

        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetXY(20, 40);
        $pdf->Cell(0, 4, 'Government-Issued ID Type: ' . ucwords(str_replace('_', ' ', $w9Form->id_document_type)), 0, 1, 'L');

        $pdf->SetXY(20, 46);
        $pdf->Cell(0, 4, 'ID Verification: ✓ Submitted (Front & Back Images)', 0, 1, 'L');

        $pdf->SetXY(20, 52);
        $pdf->Cell(0, 4, 'Submission Date: ' . $w9Form->created_at->format('M d, Y h:i A'), 0, 1, 'L');

        $pdf->SetXY(20, 58);
        $pdf->Cell(0, 4, 'IP Address: ' . $w9Form->certification_ip, 0, 1, 'L');

        $pdf->SetXY(20, 64);
        $pdf->Cell(0, 4, 'Status: ' . ucfirst($w9Form->status), 0, 1, 'L');
    }

    private function formatTaxIdForPDF($taxId, $type)
    {
        if ($type === 'ssn') {
            // Format as XXX-XX-XXXX
            $clean = preg_replace('/[^0-9]/', '', $taxId);
            if (strlen($clean) === 9) {
                return substr($clean, 0, 3) . '-' . substr($clean, 3, 2) . '-' . substr($clean, 5);
            }
        } else {
            // Format as XX-XXXXXXX
            $clean = preg_replace('/[^0-9]/', '', $taxId);
            if (strlen($clean) === 9) {
                return substr($clean, 0, 2) . '-' . substr($clean, 2);
            }
        }
        return $taxId;
    }

    private function formatTaxId($taxId)
    {
        if (strlen($taxId) === 11) { // SSN format XXX-XX-XXXX
            return substr($taxId, 0, 3) . '-' . substr($taxId, 3, 2) . '-' . substr($taxId, 5);
        } elseif (strlen($taxId) === 9 && !strpos($taxId, '-')) { // SSN without dashes
            return substr($taxId, 0, 3) . '-' . substr($taxId, 3, 2) . '-' . substr($taxId, 5);
        }
        return $taxId;
    }

}
