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
                return view('w9.form-real-pdf', [
                    'type' => 'affiliate',
                    'id' => $id,
                    'name' => $affiliate->display_name ?: $affiliate->user->name,
                    'w9Form' => $w9Form,
                    'token' => $token,
                ]);
            } elseif ($type === 'entertainer') {
                $entertainer = Entertainer::findOrFail($id);
                $w9Form = W9Form::where('entertainer_id', $id)->first();
                return view('w9.form-real-pdf', [
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

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'tax_classification' => 'required|in:individual,c_corporation,s_corporation,partnership,trust_estate,limited_liability_company_c,limited_liability_company_s,limited_liability_company_individual,sole_proprietor,other',
            'tax_classification_other' => 'nullable|string|max:255',
            'tax_id_type' => 'required|in:ssn,ein',
            'tax_id_number' => 'required|string|max:20',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'zip_code' => 'required|string|max:10',
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
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        $yOffset = 68;
        $lineHeight = 4.5;

        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($w9Form->full_name, 0, 50), 0, 1, 'L');

        $yOffset += 9;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($w9Form->business_name ?: '', 0, 50), 0, 1, 'L');

        $yOffset += 12;
        $taxClass = str_replace('_', ' ', ucwords($w9Form->tax_classification));
        if ($w9Form->tax_classification === 'other' && $w9Form->tax_classification_other) {
            $taxClass = $w9Form->tax_classification_other;
        }
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($taxClass, 0, 50), 0, 1, 'L');

        $yOffset += 12;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($w9Form->requester_name ?: '', 0, 50), 0, 1, 'L');

        $yOffset += 6;
        $pdf->SetXY(140, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($w9Form->requester_phone ?: '', 0, 30), 0, 1, 'L');

        $yOffset += 12;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($w9Form->requester_email ?: '', 0, 50), 0, 1, 'L');

        $yOffset += 16;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, ($w9Form->fatca_exemption_code ?: ''), 0, 1, 'L');

        $yOffset += 12;
        $tidType = strtoupper($w9Form->tax_id_type) === 'SSN' ? 'SSN' : 'EIN';
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(10, $lineHeight, ($w9Form->tax_id_type === 'ssn' ? '✓' : ' '), 0, 0, 'L');
        $pdf->SetXY(140, $yOffset);
        $pdf->Cell(10, $lineHeight, ($w9Form->tax_id_type === 'ein' ? '✓' : ' '), 0, 0, 'L');

        $yOffset += 8;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, $this->formatTaxId($w9Form->tax_id_number), 0, 1, 'L');

        $yOffset += 12;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($w9Form->account_numbers ?: '', 0, 50), 0, 1, 'L');

        $yOffset += 12;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($w9Form->exempt_payee_code ?: '', 0, 30), 0, 1, 'L');

        $yOffset += 12;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($w9Form->street_address, 0, 50), 0, 1, 'L');

        $yOffset += 8;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(0, $lineHeight, substr($w9Form->city, 0, 50), 0, 1, 'L');

        $yOffset += 5;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(20, $lineHeight, strtoupper($w9Form->state), 0, 0, 'L');
        $pdf->SetXY(60, $yOffset);
        $pdf->Cell(0, $lineHeight, $w9Form->zip_code, 0, 1, 'L');

        $yOffset += 12;
        $pdf->SetXY(25, $yOffset);
        $pdf->Cell(10, $lineHeight, ($w9Form->certification_signed ? '✓' : ' '), 0, 0, 'L');
    }

    private function fillSecondPage($pdf, $w9Form)
    {
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor(100, 100, 100);

        $pdf->SetXY(15, 250);
        $pdf->Cell(0, 4, 'Government-Issued ID Verification: ' . ucwords(str_replace('_', ' ', $w9Form->id_document_type)), 0, 1, 'L');

        $pdf->SetXY(15, 256);
        $pdf->Cell(0, 4, 'Submitted on: ' . $w9Form->created_at->format('M d, Y h:i A'), 0, 1, 'L');
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

    private function maskTaxId($taxId)
    {
        if (strlen($taxId) === 9) {
            return '***-**-' . substr($taxId, -4);
        } elseif (strlen($taxId) === 10) {
            return '**-' . substr($taxId, -7);
        }
        return $taxId;
    }
}
