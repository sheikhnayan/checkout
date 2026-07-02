@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-6">
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
                        <label class="form-label">Frequency</label>
                        <select name="frequency" class="form-select" id="frequency" required>
                            <option value="daily" {{ old('frequency', $schedule->frequency) === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('frequency', $schedule->frequency) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('frequency', $schedule->frequency) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('frequency', $schedule->frequency) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                            <option value="custom_month_range" {{ old('frequency', $schedule->frequency) === 'custom_month_range' ? 'selected' : '' }}>Custom Month Range</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Clubs</label>
                        <select name="website_ids[]" class="form-select" multiple required size="7">
                            @php $selectedIds = old('website_ids', $schedule->website_ids ?? []); @endphp
                            @foreach($websites as $website)
                                <option value="{{ $website->id }}" {{ in_array($website->id, $selectedIds) ? 'selected' : '' }}>{{ $website->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Recipients</label>
                        <textarea name="email_recipients" class="form-control" rows="3" required>{{ old('email_recipients', implode(', ', $schedule->email_recipients ?? [])) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Timezone</label>
                        <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $schedule->timezone ?: $defaultTimezone) }}" required>
                    </div>

                    <div class="col-md-6">
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
                        <label class="form-label">From Month</label>
                        <input type="month" name="custom_from_month" class="form-control" value="{{ old('custom_from_month', optional($schedule->custom_from_month)->format('Y-m')) }}">
                    </div>

                    <div class="col-md-4 d-none" id="customToField">
                        <label class="form-label">To Month</label>
                        <input type="month" name="custom_to_month" class="form-control" value="{{ old('custom_to_month', optional($schedule->custom_to_month)->format('Y-m')) }}">
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
    const frequency = document.getElementById('frequency');
    const weekly = document.getElementById('weeklyFields');
    const monthly = document.getElementById('monthlyFields');
    const yearlyMonth = document.getElementById('yearlyMonthField');
    const yearlyDay = document.getElementById('yearlyDayField');
    const customFrom = document.getElementById('customFromField');
    const customTo = document.getElementById('customToField');

    function updateVisibility() {
        const value = frequency.value;
        weekly.classList.toggle('d-none', value !== 'weekly');
        monthly.classList.toggle('d-none', value !== 'monthly');
        yearlyMonth.classList.toggle('d-none', value !== 'yearly');
        yearlyDay.classList.toggle('d-none', value !== 'yearly');
        customFrom.classList.toggle('d-none', value !== 'custom_month_range');
        customTo.classList.toggle('d-none', value !== 'custom_month_range');
    }

    frequency.addEventListener('change', updateVisibility);
    updateVisibility();
})();
</script>
@endsection
