<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nightly Report Summary</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 20px; color: #1e293b;">
    <div style="max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 28px 32px; border-bottom: 3px solid #c9a84c;">
            <div style="color: #c9a84c; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px;">
                Nightly Reports Portal Notification
            </div>
            <h2 style="margin: 0; font-size: 24px; font-weight: 700;">
                {{ $report->location->name ?? 'Venue Operations' }}
            </h2>
            <div style="color: #94a3b8; font-size: 14px; margin-top: 6px;">
                Business Date: <strong>{{ is_a($report->business_date ?? null, \Carbon\Carbon::class) ? $report->business_date->format('l, F j, Y') : (isset($report->business_date) ? \Carbon\Carbon::parse($report->business_date)->format('l, F j, Y') : date('l, F j, Y')) }}</strong>
            </div>
        </div>

        <!-- Content Body -->
        <div style="padding: 32px;">
            
            <div style="background: #f8fafc; border-left: 4px solid #c9a84c; padding: 14px 18px; margin-bottom: 24px; border-radius: 0 8px 8px 0;">
                <p style="margin: 0; font-size: 14px; color: #334155;">
                    A new report has been submitted by <strong>{{ $report->submitter_name ?? 'Staff Member' }}</strong> (<a href="mailto:{{ $report->submitter_email }}" style="color: #6366f1; text-decoration: none;">{{ $report->submitter_email }}</a>).
                </p>
                @if(!empty($report->additional_contributor))
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">
                    Additional Contributor: {{ $report->additional_contributor }}
                </p>
                @endif
            </div>

            <!-- Key Metrics Table (For Nightly Operations Report) -->
            @if(isset($report->net_sales))
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                <tr>
                    <td style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; width: 50%; border-radius: 8px 0 0 0;">
                        <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Net Sales</div>
                        <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">${{ number_format((float)($report->net_sales ?? 0), 2) }}</div>
                        @if(isset($report->nightly_goal) && $report->nightly_goal > 0)
                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Goal: ${{ number_format((float)$report->nightly_goal, 2) }}</div>
                        @endif
                    </td>
                    <td style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; width: 50%; border-radius: 0 8px 0 0;">
                        <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Total Guests & Spend</div>
                        <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ number_format((int)($report->total_guests ?? 0)) }}</div>
                        @if(isset($report->guest_average) && $report->guest_average > 0)
                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Avg Spend: ${{ number_format((float)$report->guest_average, 2) }}/guest</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0 0 0 8px;">
                        <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Bank Deposit</div>
                        <div style="font-size: 20px; font-weight: 700; color: #16a34a; margin-top: 4px;">${{ number_format((float)($report->deposit ?? 0), 2) }}</div>
                    </td>
                    <td style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0 0 8px 0;">
                        <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Total Payouts</div>
                        <div style="font-size: 20px; font-weight: 700; color: #d97706; margin-top: 4px;">${{ number_format((float)($report->total_payouts ?? 0), 2) }}</div>
                    </td>
                </tr>
            </table>
            @endif

            <!-- Key Metrics Table (For Boutique Report) -->
            @if(isset($report->gross_daily_sales))
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                <tr>
                    <td style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; width: 50%;">
                        <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700;">Gross Daily Sales</div>
                        <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">${{ number_format((float)$report->gross_daily_sales, 2) }}</div>
                    </td>
                    <td style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; width: 50%;">
                        <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700;">Total Guest Count</div>
                        <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ number_format((int)$report->total_guest_count) }}</div>
                    </td>
                </tr>
            </table>
            @endif

            <!-- Shift Notes / Summary -->
            @if(!empty($report->night_summary))
            <div style="margin-bottom: 20px;">
                <div style="font-weight: 700; font-size: 12px; text-transform: uppercase; color: #475569; letter-spacing: 0.5px; margin-bottom: 6px;">Night Summary</div>
                <div style="background: #f1f5f9; padding: 14px 16px; border-radius: 8px; font-size: 14px; line-height: 1.6; color: #334155;">
                    {!! nl2br(e($report->night_summary)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->team_member_notes))
            <div style="margin-bottom: 20px;">
                <div style="font-weight: 700; font-size: 12px; text-transform: uppercase; color: #475569; letter-spacing: 0.5px; margin-bottom: 6px;">Team Member Notes</div>
                <div style="background: #f1f5f9; padding: 14px 16px; border-radius: 8px; font-size: 14px; line-height: 1.6; color: #334155;">
                    {!! nl2br(e($report->team_member_notes)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->incident_notes))
            <div style="margin-bottom: 20px;">
                <div style="font-weight: 700; font-size: 12px; text-transform: uppercase; color: #dc2626; letter-spacing: 0.5px; margin-bottom: 6px;">⚠️ Incident Notes</div>
                <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 14px 16px; border-radius: 8px; font-size: 14px; line-height: 1.6; color: #991b1b;">
                    {!! nl2br(e($report->incident_notes)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->super_star_nomination))
            <div style="margin-bottom: 20px; background: #fffbeb; border: 1px solid #fef3c7; padding: 14px 16px; border-radius: 8px;">
                <div style="font-weight: 700; font-size: 12px; text-transform: uppercase; color: #b45309; margin-bottom: 4px;">⭐ Superstar Nomination</div>
                <div style="font-size: 14px; color: #92400e; font-weight: 600;">
                    {{ $report->super_star_nomination }}
                </div>
            </div>
            @endif

            @if(!empty($report->additional_recipient))
            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b;">
                <strong>Additional Recipient(s):</strong> {{ $report->additional_recipient }}
            </div>
            @endif

        </div>

        <!-- Footer -->
        <div style="background: #f8fafc; padding: 18px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center;">
            This email was automatically dispatched by <strong>CartVIP Nightly Reports System</strong>.<br>
            Submitted by {{ $report->submitter_name ?? 'Staff' }} ({{ $report->submitter_email ?? 'N/A' }}).
        </div>
    </div>
</body>
</html>
