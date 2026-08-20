@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="{{ route('admin.nightly-reports.reports.show', ['type' => 'nightly', 'id' => $report->id]) }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left me-1"></i> Back to Report Details
      </a>
      <h4 class="text-white fw-bold mb-0">Executive Email Briefing Preview</h4>
      <p class="text-muted small mb-0">Generated for {{ $report->location->name ?? 'Venue' }} ({{ $report->business_date->format('M d, Y') }})</p>
    </div>
    <div>
      <button class="btn btn-sm btn-gold" onclick="window.print()"><i class="fas fa-print me-1"></i> Print Briefing</button>
    </div>
  </div>

  <!-- Email Card Container -->
  <div class="card mx-auto" style="max-width: 680px; background: #ffffff !important; color: #1e293b !important; border-radius: 8px;">
    <div class="card-body p-4 p-md-5">
      <!-- Header -->
      <div style="border-bottom: 2px solid #c9a84c; padding-bottom: 16px; margin-bottom: 24px;">
        <h3 style="color: #0f172a; margin: 0; font-weight: 700; font-family: 'Playfair Display', serif;">
          {{ $report->location->name ?? 'Venue Operations' }}
        </h3>
        <div style="color: #64748b; font-size: 14px; margin-top: 4px;">
          Morning Shift Summary — {{ $report->business_date->format('l, F j, Y') }}
        </div>
      </div>

      <!-- Highlights Grid -->
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
        <tr>
          <td style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; width: 50%;">
            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Net Sales</div>
            <div style="font-size: 22px; font-weight: 700; color: #0f172a;">${{ number_format($report->net_sales, 2) }}</div>
            <div style="font-size: 12px; color: #64748b;">Goal: ${{ number_format($report->nightly_goal, 2) }}</div>
          </td>
          <td style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; width: 50%;">
            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Total Guests / Avg</div>
            <div style="font-size: 22px; font-weight: 700; color: #0f172a;">{{ number_format($report->total_guests) }} <span style="font-size: 14px; font-weight: 400; color: #64748b;">(${{ number_format($report->guest_average, 2) }}/hd)</span></div>
            <div style="font-size: 12px; color: #64748b;">Paid: {{ number_format($report->paid_guests ?? 0) }}</div>
          </td>
        </tr>
        <tr>
          <td style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0;">
            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Bank Deposit</div>
            <div style="font-size: 18px; font-weight: 700; color: #16a34a;">${{ number_format($report->deposit, 2) }}</div>
          </td>
          <td style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0;">
            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Total Payouts</div>
            <div style="font-size: 18px; font-weight: 700; color: #d97706;">${{ number_format($report->total_payouts, 2) }}</div>
          </td>
        </tr>
      </table>

      <!-- Executive Notes -->
      @if($report->night_summary)
      <div style="margin-bottom: 20px;">
        <div style="font-weight: 700; font-size: 13px; text-transform: uppercase; color: #475569; margin-bottom: 6px;">Night Summary</div>
        <div style="background: #f1f5f9; padding: 12px; border-radius: 6px; font-size: 14px; line-height: 1.5; color: #334155;">
          {{ $report->night_summary }}
        </div>
      </div>
      @endif

      @if($report->super_star_nomination)
      <div style="margin-bottom: 20px;">
        <div style="font-weight: 700; font-size: 13px; text-transform: uppercase; color: #475569; margin-bottom: 6px;">Superstar Nomination</div>
        <div style="font-size: 14px; color: #b45309; font-weight: 600;">
          ⭐ {{ $report->super_star_nomination }}
        </div>
      </div>
      @endif

      <!-- Footer Sign-off -->
      <div style="border-top: 1px solid #e2e8f0; padding-top: 16px; margin-top: 24px; font-size: 12px; color: #94a3b8; text-align: center;">
        Submitted by {{ $report->submitter_name }} ({{ $report->submitter_email }}) • The Nightly Reports System
      </div>
    </div>
  </div>
</div>
@endsection
