<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nightly Operations Report Summary</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0b0f17; margin: 0; padding: 24px; color: #e2e8f0; line-height: 1.5;">
    
    <div style="max-width: 680px; margin: 0 auto; background: #151d2a; border-radius: 16px; overflow: hidden; border: 1px solid #2d3748; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 32px 36px; border-bottom: 2px solid #c9a84c;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td>
                        <div style="color: #c9a84c; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 6px;">
                            Nightly Reports Portal
                        </div>
                        <h1 style="margin: 0; font-size: 26px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                            {{ $report->location->name ?? 'Venue Operations' }}
                        </h1>
                        <div style="color: #94a3b8; font-size: 14px; margin-top: 6px; font-weight: 500;">
                            Business Date: <strong style="color: #f8fafc;">{{ is_a($report->business_date ?? null, \Carbon\Carbon::class) ? $report->business_date->format('l, F j, Y') : (isset($report->business_date) ? \Carbon\Carbon::parse($report->business_date)->format('l, F j, Y') : date('l, F j, Y')) }}</strong>
                        </div>
                    </td>
                    @if(isset($report->incident_flag))
                    <td style="text-align: right; vertical-align: top;">
                        @if($report->incident_flag)
                            <span style="background: #ef4444; color: #ffffff; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: inline-block;">
                                ⚠️ Incident Reported
                            </span>
                        @else
                            <span style="background: #10b981; color: #ffffff; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: inline-block;">
                                ✓ Clean Shift
                            </span>
                        @endif
                    </td>
                    @endif
                </tr>
            </table>
        </div>

        <!-- Submitter & Meta Banner -->
        <div style="background: #1a2332; padding: 18px 36px; border-bottom: 1px solid #2d3748; font-size: 13px; color: #94a3b8;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td>
                        <strong style="color: #f8fafc;">Submitted By:</strong> {{ $report->submitter_name ?? 'Staff Member' }}
                        @if(!empty($report->submitter_email))
                            (<a href="mailto:{{ $report->submitter_email }}" style="color: #818cf8; text-decoration: none;">{{ $report->submitter_email }}</a>)
                        @endif
                        @if(!empty($report->additional_contributor))
                            <br><span style="color: #64748b;">Additional Contributor: {{ $report->additional_contributor }}</span>
                        @endif
                    </td>
                    @if(!empty($report->weather))
                    <td style="text-align: right; color: #cbd5e1;">
                        <strong>Weather:</strong> {{ $report->weather }}
                    </td>
                    @endif
                </tr>
            </table>
        </div>

        <div style="padding: 32px 36px;">

            <!-- ========================================================================= -->
            <!-- 1. FINANCIAL PERFORMANCE & SALES METRICS (Nightly Operations Report) -->
            <!-- ========================================================================= -->
            @if(isset($report->net_sales))
            <div style="margin-bottom: 28px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #c9a84c; letter-spacing: 1.5px; margin-bottom: 12px;">
                    💰 Sales & Revenue Metrics
                </div>

                <table style="width: 100%; border-collapse: separate; border-spacing: 8px; margin-left: -8px; margin-right: -8px;">
                    <tr>
                        <td style="background: #1e293b; padding: 16px; border-radius: 10px; border: 1px solid #334155; width: 33%;">
                            <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Net Sales</div>
                            <div style="font-size: 22px; font-weight: 800; color: #38bdf8; margin-top: 4px;">${{ number_format((float)$report->net_sales, 2) }}</div>
                            @if(isset($report->nightly_goal) && $report->nightly_goal > 0)
                                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Goal: ${{ number_format((float)$report->nightly_goal, 2) }}</div>
                            @endif
                        </td>
                        <td style="background: #1e293b; padding: 16px; border-radius: 10px; border: 1px solid #334155; width: 33%;">
                            <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Bank Deposit</div>
                            <div style="font-size: 22px; font-weight: 800; color: #4ade80; margin-top: 4px;">${{ number_format((float)($report->deposit ?? 0), 2) }}</div>
                            @if(isset($report->safe_balance))
                                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Safe: ${{ number_format((float)$report->safe_balance, 2) }}</div>
                            @endif
                        </td>
                        <td style="background: #1e293b; padding: 16px; border-radius: 10px; border: 1px solid #334155; width: 33%;">
                            <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Total Payouts</div>
                            <div style="font-size: 22px; font-weight: 800; color: #fbbf24; margin-top: 4px;">${{ number_format((float)($report->total_payouts ?? 0), 2) }}</div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Taxi / ATM / Other</div>
                        </td>
                    </tr>
                </table>

                <!-- Secondary Sales Metrics Grid -->
                <div style="background: #1a2332; border: 1px solid #2d3748; border-radius: 10px; padding: 16px; margin-top: 12px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #cbd5e1;">
                        <tr>
                            <td style="padding: 6px 0;">Last Year Net Sales:</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #f8fafc;">${{ number_format((float)($report->last_year_net_sales ?? 0), 2) }}</td>
                            <td style="padding: 6px 0 6px 24px;">Weekly Running Sales:</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #f8fafc;">${{ number_format((float)($report->weekly_running_net_sales ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; border-top: 1px solid #2d3748;">Day Shift Net Sales:</td>
                            <td style="padding: 6px 0; border-top: 1px solid #2d3748; text-align: right; font-weight: 700; color: #f8fafc;">${{ number_format((float)($report->day_shift_net_sales ?? 0), 2) }}</td>
                            <td style="padding: 6px 0 6px 24px; border-top: 1px solid #2d3748;">Voids / Comps:</td>
                            <td style="padding: 6px 0; border-top: 1px solid #2d3748; text-align: right; font-weight: 700; color: #f8fafc;">
                                ${{ number_format((float)($report->voids ?? 0), 2) }} / ${{ number_format((float)($report->comps ?? 0), 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 2. GUEST ATTENDANCE & ENTERTAINER METRICS -->
            <!-- ========================================================================= -->
            <div style="margin-bottom: 28px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #c9a84c; letter-spacing: 1.5px; margin-bottom: 12px;">
                    👥 Guest Attendance & Entertainers
                </div>

                <div style="background: #1a2332; border: 1px solid #2d3748; border-radius: 10px; padding: 18px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #cbd5e1;">
                        <tr>
                            <td style="padding: 8px 0; width: 50%;">
                                <div style="color: #94a3b8; font-size: 11px; text-transform: uppercase;">Total Guest Count</div>
                                <div style="font-size: 20px; font-weight: 800; color: #f8fafc;">{{ number_format((int)($report->total_guests ?? 0)) }} guests</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    Paid: {{ number_format((int)($report->paid_guests ?? 0)) }} | Free/Discount: {{ number_format((int)($report->free_discount_guests ?? 0)) }}
                                </div>
                            </td>
                            <td style="padding: 8px 0; width: 50%;">
                                <div style="color: #94a3b8; font-size: 11px; text-transform: uppercase;">Guest Average Spend</div>
                                <div style="font-size: 20px; font-weight: 800; color: #38bdf8;">${{ number_format((float)($report->guest_average ?? 0), 2) }} / head</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    Passes Redeemed: {{ number_format((int)($report->passes_redeemed ?? 0)) }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0 4px 0; border-top: 1px solid #2d3748;">
                                <strong>IPEs On Floor:</strong> {{ number_format((int)($report->ipes ?? 0)) }}
                            </td>
                            <td style="padding: 12px 0 4px 0; border-top: 1px solid #2d3748;">
                                <strong>VIP Rooms Sold:</strong> {{ number_format((int)($report->vip_rooms_sold ?? 0)) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0;">
                                <strong>Dance Dollars Sold:</strong> ${{ number_format((float)($report->dance_dollars_sold ?? 0), 2) }}
                            </td>
                            <td style="padding: 4px 0;">
                                <strong>Dance Dollars Redeemed:</strong> ${{ number_format((float)($report->dance_dollars_redeemed ?? 0), 2) }}
                            </td>
                        </tr>
                        @if(isset($report->dance_average) && $report->dance_average > 0)
                        <tr>
                            <td colspan="2" style="padding: 4px 0; color: #c9a84c;">
                                <strong>Dance Average Per IPE:</strong> ${{ number_format((float)$report->dance_average, 2) }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 3. PAYOUT BREAKDOWN -->
            <!-- ========================================================================= -->
            @if(!empty($report->taxi_payout) || !empty($report->atm_payout) || !empty($report->other_payouts))
            <div style="margin-bottom: 28px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #c9a84c; letter-spacing: 1.5px; margin-bottom: 12px;">
                    💵 Payout Breakdown
                </div>
                <div style="background: #1a2332; border: 1px solid #2d3748; border-radius: 10px; padding: 14px 18px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #cbd5e1;">
                        <tr>
                            <td style="padding: 4px 0;">Taxi Payout:</td>
                            <td style="padding: 4px 0; text-align: right; font-weight: 700; color: #f8fafc;">${{ number_format((float)($report->taxi_payout ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0;">ATM Payout:</td>
                            <td style="padding: 4px 0; text-align: right; font-weight: 700; color: #f8fafc;">${{ number_format((float)($report->atm_payout ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0;">Other Payouts:</td>
                            <td style="padding: 4px 0; text-align: right; font-weight: 700; color: #f8fafc;">${{ number_format((float)($report->other_payouts ?? 0), 2) }}</td>
                        </tr>
                        <tr style="border-top: 1px solid #2d3748;">
                            <td style="padding: 8px 0 4px 0; font-weight: 800; color: #ffffff;">Total Payouts:</td>
                            <td style="padding: 8px 0 4px 0; text-align: right; font-weight: 800; color: #fbbf24;">${{ number_format((float)($report->total_payouts ?? 0), 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif

            @endif <!-- End Net Sales Block -->

            <!-- ========================================================================= -->
            <!-- 4. BOUTIQUE METRICS (If Boutique Report) -->
            <!-- ========================================================================= -->
            @if(isset($report->gross_daily_sales))
            <div style="margin-bottom: 28px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #c9a84c; letter-spacing: 1.5px; margin-bottom: 12px;">
                    🛍️ Boutique Performance Metrics
                </div>

                <div style="background: #1a2332; border: 1px solid #2d3748; border-radius: 10px; padding: 18px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #cbd5e1;">
                        <tr>
                            <td style="padding: 6px 0; width: 50%;">Gross Daily Sales: <strong style="color:#38bdf8;">${{ number_format((float)$report->gross_daily_sales, 2) }}</strong></td>
                            <td style="padding: 6px 0; width: 50%;">Daily Goal: <strong style="color:#f8fafc;">${{ number_format((float)($report->daily_sales_goal ?? 0), 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0;">Total Guest Count: <strong style="color:#f8fafc;">{{ number_format((int)$report->total_guest_count) }}</strong></td>
                            <td style="padding: 6px 0;">Arcade/Theater Guests: <strong style="color:#f8fafc;">{{ number_format((int)($report->arcade_theater_guest_count ?? 0)) }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0;">Returns / Discounts: <strong style="color:#f8fafc;">${{ number_format((float)($report->total_returns ?? 0), 2) }} / ${{ number_format((float)($report->total_discount ?? 0), 2) }}</strong></td>
                            <td style="padding: 6px 0;">Actual Deposit: <strong style="color:#4ade80;">${{ number_format((float)($report->actual_deposit ?? 0), 2) }}</strong></td>
                        </tr>
                        @if(!empty($report->sales_direction))
                        <tr>
                            <td colspan="2" style="padding: 10px 0 0 0; border-top: 1px solid #2d3748; color: #cbd5e1;">
                                <strong>Sales Direction:</strong> <span style="color: {{ $report->sales_direction === 'UP' ? '#4ade80' : '#f87171' }}; font-weight:800;">{{ $report->sales_direction }}</span> — {{ $report->sales_direction_reason }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            <!-- ========================================================================= -->
            <!-- 5. CASH ON HAND (COH) METRICS (If COH Report) -->
            <!-- ========================================================================= -->
            @if(isset($report->vu_cash_on_hand))
            <div style="margin-bottom: 28px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #c9a84c; letter-spacing: 1.5px; margin-bottom: 12px;">
                    💵 Cash On Hand (COH) Audit Details
                </div>
                <div style="background: #1a2332; border: 1px solid #2d3748; border-radius: 10px; padding: 18px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #cbd5e1;">
                        <tr>
                            <td style="padding: 4px 0;">Drop Safe / Main Safe:</td>
                            <td style="padding: 4px 0; text-align: right; font-weight: 700; color: #f8fafc;">${{ number_format((float)($report->drop_safe ?? 0), 2) }} / ${{ number_format((float)($report->main_safe ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0;">Total Registers (1-4):</td>
                            <td style="padding: 4px 0; text-align: right; font-weight: 700; color: #f8fafc;">${{ number_format((float)(($report->register_1 ?? 0) + ($report->register_2 ?? 0) + ($report->register_3 ?? 0) + ($report->register_4 ?? 0)), 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0;">Total ATMs (1-4):</td>
                            <td style="padding: 4px 0; text-align: right; font-weight: 700; color: #f8fafc;">${{ number_format((float)(($report->atm_1 ?? 0) + ($report->atm_2 ?? 0) + ($report->atm_3 ?? 0) + ($report->atm_4 ?? 0)), 2) }}</td>
                        </tr>
                        <tr style="border-top: 1px solid #2d3748;">
                            <td style="padding: 10px 0 4px 0; font-weight: 800; color: #ffffff; font-size: 15px;">Verified Cash On Hand:</td>
                            <td style="padding: 10px 0 4px 0; text-align: right; font-weight: 800; color: #4ade80; font-size: 18px;">${{ number_format((float)$report->vu_cash_on_hand, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif

            <!-- ========================================================================= -->
            <!-- 6. OPERATIONAL SUMMARIES & SHIFT NOTES -->
            <!-- ========================================================================= -->
            @if(!empty($report->night_summary))
            <div style="margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 8px;">
                    📝 Night Summary & Executive Briefing
                </div>
                <div style="background: #1e293b; border: 1px solid #334155; padding: 16px; border-radius: 10px; font-size: 14px; color: #e2e8f0; line-height: 1.6;">
                    {!! nl2br(e($report->night_summary)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->team_member_notes))
            <div style="margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 8px;">
                    🗣️ Team Member Notes
                </div>
                <div style="background: #1e293b; border: 1px solid #334155; padding: 16px; border-radius: 10px; font-size: 14px; color: #e2e8f0; line-height: 1.6;">
                    {!! nl2br(e($report->team_member_notes)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->incident_notes))
            <div style="margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #f87171; letter-spacing: 1px; margin-bottom: 8px;">
                    ⚠️ Incident Notes
                </div>
                <div style="background: #2a1b1d; border: 1px solid #7f1d1d; padding: 16px; border-radius: 10px; font-size: 14px; color: #fca5a5; line-height: 1.6;">
                    {!! nl2br(e($report->incident_notes)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->ipe_notes))
            <div style="margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 8px;">
                    💃 IPE / Entertainer Shift Notes
                </div>
                <div style="background: #1e293b; border: 1px solid #334155; padding: 16px; border-radius: 10px; font-size: 14px; color: #e2e8f0; line-height: 1.6;">
                    {!! nl2br(e($report->ipe_notes)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->ordering_notes))
            <div style="margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 8px;">
                    📦 Inventory & Ordering Requests
                </div>
                <div style="background: #1e293b; border: 1px solid #334155; padding: 16px; border-radius: 10px; font-size: 14px; color: #e2e8f0; line-height: 1.6;">
                    {!! nl2br(e($report->ordering_notes)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->social_media_content))
            <div style="margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 8px;">
                    📸 Social Media Content & Highlights
                </div>
                <div style="background: #1e293b; border: 1px solid #334155; padding: 16px; border-radius: 10px; font-size: 14px; color: #e2e8f0; line-height: 1.6;">
                    {!! nl2br(e($report->social_media_content)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->nightly_checklists))
            <div style="margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 8px;">
                    📋 Nightly Checklists
                </div>
                <div style="background: #1e293b; border: 1px solid #334155; padding: 16px; border-radius: 10px; font-size: 14px; color: #e2e8f0; line-height: 1.6;">
                    {!! nl2br(e($report->nightly_checklists)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->pass_distribution_locations))
            <div style="margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 8px;">
                    🎟️ Pass Distribution Locations
                </div>
                <div style="background: #1e293b; border: 1px solid #334155; padding: 16px; border-radius: 10px; font-size: 14px; color: #e2e8f0; line-height: 1.6;">
                    {!! nl2br(e($report->pass_distribution_locations)) !!}
                </div>
            </div>
            @endif

            @if(!empty($report->super_star_nomination))
            <div style="margin-bottom: 24px; background: #272015; border: 1px solid #92400e; padding: 16px; border-radius: 10px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #fbbf24; letter-spacing: 1px; margin-bottom: 6px;">
                    ⭐ Superstar Nomination
                </div>
                <div style="font-size: 15px; color: #fef3c7; font-weight: 700;">
                    {{ $report->super_star_nomination }}
                </div>
            </div>
            @endif

            @if(!empty($report->additional_recipient))
            <div style="margin-top: 28px; padding-top: 18px; border-top: 1px solid #2d3748; font-size: 12px; color: #64748b;">
                <strong>Additional Recipient(s):</strong> {{ $report->additional_recipient }}
            </div>
            @endif

        </div>

        <!-- Footer -->
        <div style="background: #0f172a; padding: 24px 36px; border-top: 1px solid #2d3748; font-size: 12px; color: #64748b; text-align: center; line-height: 1.6;">
            Dispatched by <strong>CartVIP Nightly Reports Management Portal</strong><br>
            Submitted on {{ isset($report->created_at) ? $report->created_at->format('M d, Y \a\t h:i A') : date('M d, Y') }}
        </div>
    </div>
</body>
</html>
