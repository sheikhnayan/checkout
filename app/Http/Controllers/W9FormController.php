<?php

namespace App\Http\Controllers;

use App\Models\W9Form;
use App\Models\Affiliate;
use App\Models\Entertainer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
                return view('w9.form-official', [
                    'type' => 'affiliate',
                    'id' => $id,
                    'name' => $affiliate->display_name ?: $affiliate->user->name,
                    'w9Form' => $w9Form,
                    'token' => $token,
                ]);
            } elseif ($type === 'entertainer') {
                $entertainer = Entertainer::findOrFail($id);
                $w9Form = W9Form::where('entertainer_id', $id)->first();
                return view('w9.form-official', [
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

        $filename = 'W-9_' . ($w9Form->full_name ?: 'Form') . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        $pdf = Pdf::loadView('w9.pdf-download', ['w9Form' => $w9Form])->setPaper('letter', 'portrait');

        return $pdf->download($filename);
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
