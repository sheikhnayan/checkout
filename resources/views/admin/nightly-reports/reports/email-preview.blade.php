@extends('admin.nightly-reports.layout')

@section('content')
<style>
  /* Strict style isolation for Email Briefing Card to prevent dark-mode layout text bleaching */
  .email-preview-card {
    max-width: 680px;
    background: #ffffff !important;
    color: #1e293b !important;
    border-radius: 10px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
    border: 1px solid #e2e8f0;
    overflow: hidden;
  }
  .email-preview-card,
  .email-preview-card div,
  .email-preview-card p,
  .email-preview-card span,
  .email-preview-card td,
  .email-preview-card th,
  .email-preview-card h3,
  .email-preview-card small {
    color: #1e293b;
  }
  .email-preview-card .ep-header-title {
    color: #0f172a !important;
    margin: 0;
    font-weight: 700;
    font-family: 'Playfair Display', Georgia, serif;
  }
  .email-preview-card .ep-subtext {
    color: #64748b !important;
    font-size: 14px;
    margin-top: 4px;
  }
  .email-preview-card .ep-metric-td {
    padding: 14px 16px;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    vertical-align: top;
  }
  .email-preview-card .ep-metric-label {
    font-size: 12px;
    color: #64748b !important;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }
  .email-preview-card .ep-metric-val {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a !important;
    line-height: 1.2;
  }
  .email-preview-card .ep-metric-sub {
    font-size: 12px;
    color: #64748b !important;
    margin-top: 4px;
  }
  .email-preview-card .ep-metric-val-green {
    font-size: 20px;
    font-weight: 700;
    color: #16a34a !important;
    line-height: 1.2;
  }
  .email-preview-card .ep-metric-val-amber {
    font-size: 20px;
    font-weight: 700;
    color: #d97706 !important;
    line-height: 1.2;
  }
  .email-preview-card .ep-section-heading {
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    color: #475569 !important;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
  }
  .email-preview-card .ep-note-box {
    background: #f1f5f9 !important;
    padding: 14px 16px;
    border-radius: 6px;
    font-size: 14px;
    line-height: 1.6;
    color: #334155 !important;
    border-left: 3px solid #cbd5e1;
  }
  .email-preview-card .ep-star-text {
    font-size: 14px;
    color: #b45309 !important;
    font-weight: 600;
  }
  .email-preview-card .ep-footer-text {
    border-top: 1px solid #e2e8f0;
    padding-top: 16px;
    margin-top: 24px;
    font-size: 12px;
    color: #94a3b8 !important;
    text-align: center;
  }

  @media print {
    body {
      background: #ffffff !important;
    }
    .email-preview-card {
      box-shadow: none !important;
      border: none !important;
      max-width: 100% !important;
    }
    .no-print {
      display: none !important;
    }
  }
</style>

<div class="container-fluid p-0">
  <!-- Top Navigation & Action Controls -->
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
    <div>
      <a href="{{ route('admin.nightly-reports.reports.show', ['type' => 'nightly', 'id' => $report->id]) }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left me-1"></i> Back to Report Details
      </a>
      <h4 class="text-white fw-bold mb-0">Executive Email Briefing Preview</h4>
      <p class="text-muted small mb-0">
        Generated for <strong class="text-white">{{ $report->location->name ?? 'Venue' }}</strong> • Business Date: {{ $report->business_date->format('M d, Y') }}
      </p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-sm btn-outline-light" onclick="window.print()">
        <i class="fas fa-print me-1"></i> Print Briefing
      </button>

      <form action="{{ route('admin.nightly-reports.reports.send-email', ['type' => 'nightly', 'id' => $report->id]) }}" method="POST" onsubmit="return confirm('Send email briefing now to all configured recipients?');" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-gold">
          <i class="fas fa-paper-plane me-1"></i> Send Email Now
        </button>
      </form>
    </div>
  </div>

  @php
    $recipientList = [];
    if (!empty($report->submitter_email)) {
      $recipientList[] = $report->submitter_email . ' (Submitter)';
    }
    if ($report->location && !empty($report->location->gm_email)) {
      $recipientList[] = $report->location->gm_email . ' (GM)';
    }
    if (!empty($report->additional_recipient)) {
      $recipientList[] = $report->additional_recipient . ' (Additional)';
    }
  @endphp

  <!-- Recipients Banner -->
  <div class="alert no-print mb-4 p-3 d-flex align-items-center justify-content-between" style="background: rgba(30, 41, 59, 0.7); border: 1px solid #334155; border-radius: 8px;">
    <div class="small">
      <span class="text-gold fw-bold me-2"><i class="fas fa-envelope-open-text me-1"></i> Delivery Recipients:</span>
      @if(count($recipientList) > 0)
        <span class="text-white">{{ implode(' • ', $recipientList) }}</span>
      @else
        <span class="text-warning">No email recipients configured on this report or venue.</span>
      @endif
    </div>
    <div class="badge bg-secondary text-light">Official Briefing Preview</div>
  </div>

  <!-- Email Card Container -->
  <div class="email-preview-card mx-auto">
    <div class="p-4 p-md-5">
      <!-- Header -->
      <div style="border-bottom: 2px solid #c9a84c; padding-bottom: 16px; margin-bottom: 24px;">
        <h3 class="ep-header-title">
          {{ $report->location->name ?? 'Venue Operations' }}
        </h3>
        <div class="ep-subtext">
          Nightly Operations Briefing — {{ $report->business_date->format('l, F j, Y') }}
        </div>
      </div>

      <!-- Highlights Grid -->
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
        <tr>
          <td class="ep-metric-td" style="width: 50%;">
            <div class="ep-metric-label">Net Sales</div>
            <div class="ep-metric-val">${{ number_format($report->net_sales, 2) }}</div>
            <div class="ep-metric-sub">Goal: ${{ number_format($report->nightly_goal, 2) }}</div>
          </td>
          <td class="ep-metric-td" style="width: 50%;">
            <div class="ep-metric-label">Total Guests / Avg</div>
            <div class="ep-metric-val">
              {{ number_format($report->total_guests) }} 
              <span style="font-size: 14px; font-weight: 400; color: #64748b !important;">(${{ number_format($report->guest_average, 2) }}/hd)</span>
            </div>
            <div class="ep-metric-sub">Paid: {{ number_format($report->paid_guests ?? 0) }}</div>
          </td>
        </tr>
        <tr>
          <td class="ep-metric-td">
            <div class="ep-metric-label">Bank Deposit</div>
            <div class="ep-metric-val-green">${{ number_format($report->deposit, 2) }}</div>
          </td>
          <td class="ep-metric-td">
            <div class="ep-metric-label">Total Payouts</div>
            <div class="ep-metric-val-amber">${{ number_format($report->total_payouts, 2) }}</div>
          </td>
        </tr>
      </table>

      <!-- Executive Notes -->
      @if($report->night_summary)
      <div style="margin-bottom: 20px;">
        <div class="ep-section-heading">Night Summary</div>
        <div class="ep-note-box">
          {{ $report->night_summary }}
        </div>
      </div>
      @endif

      @if($report->super_star_nomination)
      <div style="margin-bottom: 20px;">
        <div class="ep-section-heading">Superstar Nomination</div>
        <div class="ep-star-text">
          ⭐ {{ $report->super_star_nomination }}
        </div>
      </div>
      @endif

      <!-- Footer Sign-off -->
      <div class="ep-footer-text">
        Submitted by {{ $report->submitter_name }} ({{ $report->submitter_email }}) • The Nightly Reports System
      </div>
    </div>
  </div>
</div>
@endsection
