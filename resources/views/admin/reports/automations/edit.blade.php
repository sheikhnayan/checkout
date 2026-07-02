@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-6 automation-reports-page">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <a href="{{ route('admin.reports.automation.schedules') }}" class="btn btn-outline-secondary btn-sm mb-2">Back to Schedules</a>
            <h1 class="h2 mb-1" style="color: #fff">Edit Automation Schedule</h1>
            <p class="text-muted mb-0">Update delivery timing, clubs, and recipients.</p>
        </div>
        <a href="{{ route('admin.reports.automation.history') }}" class="btn btn-primary btn-sm">View History</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.reports.automation.schedules.update', $schedule) }}" id="editScheduleForm">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Schedule Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $schedule->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Send Frequency</label>
                        <select name="frequency" class="form-select" id="frequency" required>
                            @php
                                $cadence = old('frequency', $schedule->frequency);
                                if ($cadence === 'custom_month_range') {
                                    $cadence = 'monthly';
                                }
                            @endphp
                            <option value="daily" {{ $cadence === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $cadence === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $cadence === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ $cadence === 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                        <small class="text-muted">This controls how often the report is sent.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Report Type</label>
                        @php
                            $reportType = old('report_period_type', $schedule->report_period_type ?: ($schedule->frequency === 'custom_month_range' ? 'custom_range' : $schedule->frequency));
                        @endphp
                        <select name="report_period_type" class="form-select" id="reportPeriodType" required>
                            <option value="daily" {{ $reportType === 'daily' ? 'selected' : '' }}>Daily Data</option>
                            <option value="weekly" {{ $reportType === 'weekly' ? 'selected' : '' }}>Weekly Data</option>
                            <option value="monthly" {{ $reportType === 'monthly' ? 'selected' : '' }}>Monthly Data</option>
                            <option value="yearly" {{ $reportType === 'yearly' ? 'selected' : '' }}>Yearly Data</option>
                            <option value="custom_range" {{ $reportType === 'custom_range' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                        <small class="text-muted">This controls which date range is included in each sent report.</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Clubs</label>
                        <div class="d-flex gap-2">
                            <select class="form-select" id="clubPicker">
                                <option value="">Select a club</option>
                                @foreach($websites as $website)
                                    <option value="{{ $website->id }}">{{ $website->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" id="addClubBtn">Add</button>
                        </div>
                        <div id="clubList" class="mt-2"></div>
                        <div id="clubInputs"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Recipients</label>
                        <div class="d-flex gap-2">
                            <input type="email" class="form-control" id="recipientInput" placeholder="name@email.com">
                            <button type="button" class="btn btn-outline-primary" id="addRecipientBtn">Add</button>
                        </div>
                        <div id="recipientList" class="mt-2"></div>
                        <div id="recipientInputs"></div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Send Time</label>
                        <input type="time" name="send_time" class="form-control" value="{{ old('send_time', $schedule->send_time ? substr((string) $schedule->send_time, 0, 5) : '06:00') }}">
                        <small class="text-muted">If left blank, default is 6:00 AM PST.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">First Send Date (Optional)</label>
                        <input type="date" name="one_time_date" class="form-control" value="{{ old('one_time_date', optional($schedule->one_time_date)->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">First Send Time (Optional)</label>
                        <input type="time" name="one_time_time" class="form-control" value="{{ old('one_time_time', $schedule->one_time_time ? substr((string) $schedule->one_time_time, 0, 5) : '') }}">
                    </div>

                    <div class="col-md-4 d-none" id="weeklyFields">
                        <label class="form-label">Week Day</label>
                        <select name="weekly_day" class="form-select">
                            @for($i = 0; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ (int) old('weekly_day', $schedule->weekly_day) === $i ? 'selected' : '' }}>{{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$i] }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4 d-none" id="monthlyFields">
                        <label class="form-label">Day of Month</label>
                        <input type="number" name="monthly_day" min="1" max="31" class="form-control" value="{{ old('monthly_day', $schedule->monthly_day) }}">
                    </div>

                    <div class="col-md-4 d-none" id="yearlyMonthField">
                        <label class="form-label">Yearly Month</label>
                        <input type="number" name="yearly_month" min="1" max="12" class="form-control" value="{{ old('yearly_month', $schedule->yearly_month) }}">
                    </div>

                    <div class="col-md-4 d-none" id="yearlyDayField">
                        <label class="form-label">Yearly Day</label>
                        <input type="number" name="yearly_day" min="1" max="31" class="form-control" value="{{ old('yearly_day', $schedule->yearly_day) }}">
                    </div>

                    <div class="col-md-4 d-none" id="customFromField">
                        <label class="form-label">From Date</label>
                        <input type="date" name="custom_from_month" class="form-control" value="{{ old('custom_from_month', optional($schedule->custom_from_month)->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-4 d-none" id="customToField">
                        <label class="form-label">To Date</label>
                        <input type="date" name="custom_to_month" class="form-control" value="{{ old('custom_to_month', optional($schedule->custom_to_month)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.reports.automation.schedules') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const initialWebsiteIds = @json(array_map('intval', old('website_ids', $schedule->website_ids ?? [])));
    const websites = @json($websites->map(fn($w) => ['id' => (int) $w->id, 'name' => $w->name])->values());
    const clubPicker = document.getElementById('clubPicker');
    const addClubBtn = document.getElementById('addClubBtn');
    const clubList = document.getElementById('clubList');
    const clubInputs = document.getElementById('clubInputs');

    const initialRecipientsRaw = @json(old('email_recipients', $schedule->email_recipients ?? []));
    const initialRecipients = Array.isArray(initialRecipientsRaw)
        ? initialRecipientsRaw
        : String(initialRecipientsRaw || '').split(/[,;\s]+/).filter(Boolean);
    const recipientInput = document.getElementById('recipientInput');
    const addRecipientBtn = document.getElementById('addRecipientBtn');
    const recipientList = document.getElementById('recipientList');
    const recipientInputs = document.getElementById('recipientInputs');

    const frequency = document.getElementById('frequency');
    const reportPeriodType = document.getElementById('reportPeriodType');
    const weekly = document.getElementById('weeklyFields');
    const monthly = document.getElementById('monthlyFields');
    const yearlyMonth = document.getElementById('yearlyMonthField');
    const yearlyDay = document.getElementById('yearlyDayField');
    const customFrom = document.getElementById('customFromField');
    const customTo = document.getElementById('customToField');
    const form = document.getElementById('editScheduleForm');

    let selectedWebsiteIds = Array.from(new Set(initialWebsiteIds));
    let recipients = Array.from(new Set(initialRecipients.map(v => String(v).trim().toLowerCase()).filter(Boolean)));

    function renderClubs() {
        clubList.innerHTML = '';
        clubInputs.innerHTML = '';

        selectedWebsiteIds.forEach((id) => {
            const website = websites.find((w) => w.id === id);
            if (!website) return;

            const chip = document.createElement('span');
            chip.className = 'badge bg-primary me-1 mb-1';
            chip.innerHTML = `${website.name} <button type="button" class="btn btn-sm btn-link text-white p-0 ms-1" data-remove-club="${id}">x</button>`;
            clubList.appendChild(chip);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'website_ids[]';
            input.value = String(id);
            clubInputs.appendChild(input);
        });
    }

    function renderRecipients() {
        recipientList.innerHTML = '';
        recipientInputs.innerHTML = '';

        recipients.forEach((email) => {
            const chip = document.createElement('span');
            chip.className = 'badge bg-info me-1 mb-1';
            chip.innerHTML = `${email} <button type="button" class="btn btn-sm btn-link text-white p-0 ms-1" data-remove-recipient="${email}">x</button>`;
            recipientList.appendChild(chip);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'email_recipients[]';
            input.value = email;
            recipientInputs.appendChild(input);
        });
    }

    addClubBtn.addEventListener('click', function () {
        const id = Number(clubPicker.value || 0);
        if (!id) return;
        if (!selectedWebsiteIds.includes(id)) {
            selectedWebsiteIds.push(id);
            renderClubs();
        }
        clubPicker.value = '';
    });

    clubList.addEventListener('click', function (event) {
        const target = event.target;
        const id = Number(target.getAttribute('data-remove-club') || 0);
        if (!id) return;
        selectedWebsiteIds = selectedWebsiteIds.filter((v) => v !== id);
        renderClubs();
    });

    addRecipientBtn.addEventListener('click', function () {
        const email = String(recipientInput.value || '').trim().toLowerCase();
        if (!email) return;
        const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        if (!valid) {
            alert('Please enter a valid email address.');
            return;
        }
        if (!recipients.includes(email)) {
            recipients.push(email);
            renderRecipients();
        }
        recipientInput.value = '';
    });

    recipientList.addEventListener('click', function (event) {
        const target = event.target;
        const email = target.getAttribute('data-remove-recipient');
        if (!email) return;
        recipients = recipients.filter((v) => v !== email);
        renderRecipients();
    });

    form.addEventListener('submit', function (event) {
        if (selectedWebsiteIds.length === 0) {
            event.preventDefault();
            alert('Please add at least one club.');
            return;
        }
        if (recipients.length === 0) {
            event.preventDefault();
            alert('Please add at least one recipient email.');
        }
    });

    function updateFrequencyVisibility() {
        const value = frequency.value;
        weekly.classList.toggle('d-none', value !== 'weekly');
        monthly.classList.toggle('d-none', value !== 'monthly');
        yearlyMonth.classList.toggle('d-none', value !== 'yearly');
        yearlyDay.classList.toggle('d-none', value !== 'yearly');
    }

    function updateReportTypeVisibility() {
        const value = reportPeriodType.value;
        customFrom.classList.toggle('d-none', value !== 'custom_range');
        customTo.classList.toggle('d-none', value !== 'custom_range');
    }

    renderClubs();
    renderRecipients();
    frequency.addEventListener('change', updateFrequencyVisibility);
    reportPeriodType.addEventListener('change', updateReportTypeVisibility);
    updateFrequencyVisibility();
    updateReportTypeVisibility();
})();
</script>

<style>
.automation-reports-page .form-label,
.automation-reports-page .card-header h5 {
    color: #fff !important;
}
</style>
@endsection
