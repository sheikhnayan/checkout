@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-6">
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
                            <label class="form-label">Frequency</label>
                            <select name="frequency" class="form-select" id="frequency" required>
                                <option value="daily" {{ old('frequency') === 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ old('frequency') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="custom_month_range" {{ old('frequency') === 'custom_month_range' ? 'selected' : '' }}>Custom Month Range</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Clubs</label>
                            <select name="website_ids[]" class="form-select" multiple required size="6">
                                @foreach($websites as $website)
                                    <option value="{{ $website->id }}" {{ in_array($website->id, old('website_ids', [])) ? 'selected' : '' }}>{{ $website->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple clubs.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Recipients</label>
                            <textarea name="email_recipients" class="form-control" rows="3" required placeholder="one@email.com, two@email.com">{{ old('email_recipients') }}</textarea>
                            <small class="text-muted">Separate emails with comma, space, or semicolon.</small>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Timezone</label>
                                <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $defaultTimezone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Send Time</label>
                                <input type="time" name="send_time" class="form-control" value="{{ old('send_time', '06:00') }}">
                                <small class="text-muted">If left blank, default is 6:00 AM PST.</small>
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

                        <div id="customMonthFields" class="mt-3 d-none">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">From Month</label>
                                    <input type="month" name="custom_from_month" class="form-control" value="{{ old('custom_from_month') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">To Month</label>
                                    <input type="month" name="custom_to_month" class="form-control" value="{{ old('custom_to_month') }}">
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
                                    <th>Frequency</th>
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
                                        <td>{{ $schedule->next_run_at ? $schedule->next_run_at->format('Y-m-d H:i') : '-' }}</td>
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
    const frequency = document.getElementById('frequency');
    const weekly = document.getElementById('weeklyFields');
    const monthly = document.getElementById('monthlyFields');
    const yearly = document.getElementById('yearlyFields');
    const custom = document.getElementById('customMonthFields');

    function updateVisibility() {
        const value = frequency.value;
        weekly.classList.toggle('d-none', value !== 'weekly');
        monthly.classList.toggle('d-none', value !== 'monthly');
        yearly.classList.toggle('d-none', value !== 'yearly');
        custom.classList.toggle('d-none', value !== 'custom_month_range');
    }

    frequency.addEventListener('change', updateVisibility);
    updateVisibility();
})();
</script>
@endsection
