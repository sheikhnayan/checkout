@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-6 automation-reports-page">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h1 class="h2 mb-1" style="color: #fff">Automation Reports</h1>
            <p class="text-muted mb-0">Create dynamic schedules and send executive report links automatically.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">Back to Reports</a>
            <a href="{{ route('admin.reports.automation.history') }}" class="btn btn-primary btn-sm">Generated/Sent History</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0" style="color: #fff">Create Schedule</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reports.automation.schedules.store') }}" id="scheduleForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Schedule Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Send Frequency</label>
                            <select name="frequency" class="form-select" id="frequency" required>
                                <option value="daily" {{ old('frequency') === 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ old('frequency') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                            <small class="text-muted">This controls how often the report is sent.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Report Type</label>
                            <select name="report_period_type" class="form-select" id="reportPeriodType" required>
                                <option value="daily" {{ old('report_period_type') === 'daily' ? 'selected' : '' }}>Daily Data</option>
                                <option value="weekly" {{ old('report_period_type', 'weekly') === 'weekly' ? 'selected' : '' }}>Weekly Data</option>
                                <option value="monthly" {{ old('report_period_type') === 'monthly' ? 'selected' : '' }}>Monthly Data</option>
                                <option value="yearly" {{ old('report_period_type') === 'yearly' ? 'selected' : '' }}>Yearly Data</option>
                                <option value="custom_range" {{ old('report_period_type') === 'custom_range' ? 'selected' : '' }}>Custom Range</option>
                            </select>
                            <small class="text-muted">This controls which date range is included in each sent report.</small>
                        </div>

                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label class="form-label">Recipients</label>
                            <div class="d-flex gap-2">
                                <input type="email" class="form-control" id="recipientInput" placeholder="name@email.com">
                                <button type="button" class="btn btn-outline-primary" id="addRecipientBtn">Add</button>
                            </div>
                            <div id="recipientList" class="mt-2"></div>
                            <div id="recipientInputs"></div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-12">
                                <label class="form-label">Send Time</label>
                                <input type="time" name="send_time" class="form-control" value="{{ old('send_time', '06:00') }}">
                                <small class="text-muted">If left blank, default is 6:00 AM PT.</small>
                            </div>
                        </div>

                        <div class="row g-2 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">First Send Date (Optional)</label>
                                <input type="date" name="one_time_date" class="form-control" value="{{ old('one_time_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">First Send Time (Optional)</label>
                                <input type="time" name="one_time_time" class="form-control" value="{{ old('one_time_time') }}">
                            </div>
                        </div>

                        <div id="weeklyFields" class="mt-3 d-none">
                            <label class="form-label">Week Day</label>
                            <select name="weekly_day" class="form-select">
                                <option value="0">Sunday</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                            </select>
                        </div>

                        <div id="monthlyFields" class="mt-3 d-none">
                            <label class="form-label">Day of Month</label>
                            <input type="number" name="monthly_day" min="1" max="31" class="form-control" value="{{ old('monthly_day', 1) }}">
                        </div>

                        <div id="yearlyFields" class="mt-3 d-none">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Month</label>
                                    <input type="number" name="yearly_month" min="1" max="12" class="form-control" value="{{ old('yearly_month', 1) }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Day</label>
                                    <input type="number" name="yearly_day" min="1" max="31" class="form-control" value="{{ old('yearly_day', 1) }}">
                                </div>
                            </div>
                        </div>

                        <div id="customRangeFields" class="mt-3 d-none">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="custom_from_month" class="form-control" value="{{ old('custom_from_month') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="custom_to_month" class="form-control" value="{{ old('custom_to_month') }}">
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 mt-4" type="submit">Create Automation Schedule</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0" style="color: #fff">Existing Schedules</h5>
                </div>
                <div class="card-body p-0">
                    @if($schedules->isEmpty())
                        <div class="p-4 text-muted">No schedules created yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Send Frequency</th>
                                    <th>Report Type</th>
                                    <th>Next Run</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $schedule->name }}</div>
                                            <div class="small text-muted">{{ count($schedule->email_recipients ?? []) }} recipients</div>
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $schedule->frequency)) }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $schedule->report_period_type ?: ($schedule->frequency === 'custom_month_range' ? 'custom_range' : $schedule->frequency))) }}</td>
                                        <td>{{ $schedule->next_run_at ? $schedule->next_run_at->copy()->timezone('America/Los_Angeles')->format('Y-m-d h:i A') . ' PT' : '-' }}</td>
                                        <td>
                                            @if($schedule->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Paused</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.reports.automation.schedules.edit', $schedule) }}">Edit</a>
                                                <form method="POST" action="{{ route('admin.reports.automation.schedules.run', $schedule) }}">
                                                    @csrf
                                                    <button class="btn btn-primary btn-sm" type="submit">Run Now</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reports.automation.schedules.toggle', $schedule) }}">
                                                    @csrf
                                                    <button class="btn btn-outline-secondary btn-sm" type="submit">{{ $schedule->is_active ? 'Pause' : 'Resume' }}</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reports.automation.schedules.destroy', $schedule) }}" onsubmit="return confirm('Delete this schedule?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const initialWebsiteIds = @json(array_map('intval', old('website_ids', [])));
    const websites = @json($websites->map(fn($w) => ['id' => (int) $w->id, 'name' => $w->name])->values());
    const clubPicker = document.getElementById('clubPicker');
    const addClubBtn = document.getElementById('addClubBtn');
    const clubList = document.getElementById('clubList');
    const clubInputs = document.getElementById('clubInputs');

    const initialRecipientsRaw = @json(old('email_recipients', []));
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
    const yearly = document.getElementById('yearlyFields');
    const customRange = document.getElementById('customRangeFields');
    const form = document.getElementById('scheduleForm');

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
        yearly.classList.toggle('d-none', value !== 'yearly');
    }

    function updateReportTypeVisibility() {
        const value = reportPeriodType.value;
        customRange.classList.toggle('d-none', value !== 'custom_range');
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
.automation-reports-page .table thead th,
.automation-reports-page .card-header h5 {
    color: #fff !important;
}
</style>
@endsection
