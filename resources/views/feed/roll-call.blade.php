<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $club->name }} Roll Call</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --roll-bg: #070d1a;
            --roll-stage: rgba(10, 17, 33, 0.86);
            --roll-stage-border: rgba(159, 196, 255, 0.18);
            --roll-card: rgba(12, 20, 36, 0.92);
            --roll-border: rgba(255, 255, 255, 0.1);
            --roll-text: #edf3ff;
            --roll-muted: #91a2c1;
            --roll-accent: #efbe6f;
            --roll-shadow: 0 28px 86px rgba(0, 0, 0, 0.48);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Poppins", "Segoe UI", sans-serif;
            color: var(--roll-text);
            background:
                radial-gradient(circle at 14% 6%, rgba(239, 190, 111, 0.2), transparent 24%),
                radial-gradient(circle at 85% 88%, rgba(122, 169, 255, 0.22), transparent 30%),
                linear-gradient(90deg, #02050d 0%, #081123 19%, #15345f 50%, #081123 81%, #02050d 100%),
                linear-gradient(180deg, #071121 0%, var(--roll-bg) 100%);
            background-attachment: fixed;
        }

        .roll-shell {
            width: min(1120px, calc(100% - 24px));
            margin: 0 auto;
            padding: 22px 20px 48px;
            border: 1px solid var(--roll-stage-border);
            border-radius: 28px;
            background:
                linear-gradient(180deg, rgba(18, 34, 59, 0.62) 0%, rgba(8, 14, 28, 0.76) 24%, rgba(8, 13, 25, 0.86) 100%),
                var(--roll-stage);
            backdrop-filter: blur(8px);
            box-shadow: var(--roll-shadow);
        }

        .roll-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: var(--roll-muted);
            margin-bottom: 18px;
        }

        .roll-topbar a:hover { color: #fff; }

        .roll-hero {
            border: 1px solid var(--roll-border);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 16px;
            background:
                radial-gradient(circle at 7% 14%, rgba(239, 190, 111, 0.16), transparent 28%),
                linear-gradient(145deg, rgba(16, 25, 44, 0.98), rgba(8, 14, 27, 0.9));
            box-shadow: var(--roll-shadow);
        }

        .roll-kicker {
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: .16em;
            font-size: .72rem;
            color: var(--roll-accent);
            margin-bottom: 8px;
        }

        .roll-title {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.3rem);
            letter-spacing: -0.04em;
            line-height: .95;
            font-weight: 800;
        }

        .roll-copy {
            margin: 10px 0 0;
            color: var(--roll-muted);
            line-height: 1.75;
            max-width: 70ch;
        }

        .roll-grid {
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 16px;
        }

        .roll-calendar-panel,
        .roll-models-panel {
            border: 1px solid var(--roll-border);
            border-radius: 16px;
            background: var(--roll-card);
            box-shadow: var(--roll-shadow);
        }

        .roll-calendar-panel {
            padding: 16px;
            position: sticky;
            top: 14px;
            height: fit-content;
        }

        .roll-calendar-head {
            margin-bottom: 12px;
        }

        .roll-calendar-head h2 {
            margin: 0;
            font-size: 1.2rem;
        }

        .roll-calendar-head p {
            margin: 6px 0 0;
            color: var(--roll-muted);
            font-size: .9rem;
            line-height: 1.6;
        }

        .roll-selected-date {
            margin-top: 12px;
            border-radius: 12px;
            border: 1px solid rgba(239, 190, 111, 0.28);
            background: rgba(239, 190, 111, 0.12);
            padding: 11px 12px;
            font-size: .88rem;
        }

        .roll-selected-date strong {
            color: #fff;
        }

        .flatpickr-calendar.inline {
            width: 100%;
            max-width: 100%;
            background: transparent;
            box-shadow: none;
            border: 0;
            margin: 0;
        }

        .flatpickr-months .flatpickr-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-weekday,
        .flatpickr-day {
            color: var(--roll-text);
        }

        .flatpickr-months {
            margin-bottom: 8px;
        }

        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            color: #f3d8a5;
            fill: #f3d8a5;
            top: 4px;
            border-radius: 10px;
            width: 34px;
            height: 34px;
            padding: 6px;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            color: #fff;
            fill: #fff;
            background: rgba(255,255,255,0.08);
        }

        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            width: 14px;
            height: 14px;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            color: var(--roll-text);
            font-weight: 700;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: rgba(20, 31, 54, 0.95);
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 10px;
            padding: 2px 8px;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months option {
            background: #172643;
            color: #edf3ff;
        }

        .flatpickr-current-month .numInputWrapper span {
            border-color: rgba(148, 163, 184, 0.5);
        }

        .flatpickr-current-month .numInputWrapper span.arrowUp:after {
            border-bottom-color: #edf3ff;
        }

        .flatpickr-current-month .numInputWrapper span.arrowDown:after {
            border-top-color: #edf3ff;
        }

        .flatpickr-weekdays {
            background: rgba(255,255,255,0.03);
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .flatpickr-weekdays .flatpickr-weekday {
            color: #91a2c1 !important;
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .flatpickr-day {
            border-radius: 10px;
            border: 1px solid transparent;
            max-width: 40px;
            line-height: 36px;
            height: 38px;
        }

        .flatpickr-day:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.12);
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: linear-gradient(135deg, #f4d696, #e2af5c);
            color: #241707;
            border-color: rgba(242, 188, 103, 0.9);
            box-shadow: 0 10px 22px rgba(239, 190, 111, 0.28);
        }

        .flatpickr-day.today:not(.selected) {
            border-color: rgba(125, 174, 255, 0.68);
            color: #dce9ff;
        }

        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            color: rgba(145, 162, 193, 0.35);
            cursor: not-allowed;
            border-color: transparent;
            background: transparent;
        }

        .roll-models-panel {
            padding: 16px;
        }

        .roll-models-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .roll-models-head h2 {
            margin: 0;
            font-size: 1.26rem;
        }

        .roll-count-chip {
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.05);
            color: var(--roll-muted);
            padding: 8px 12px;
            font-size: .8rem;
            font-weight: 700;
        }

        .roll-model-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .roll-model-card {
            border-radius: 14px;
            border: 1px solid var(--roll-border);
            background: rgba(255,255,255,0.03);
            overflow: hidden;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .roll-model-card:hover {
            transform: translateY(-4px);
            border-color: rgba(239, 190, 111, 0.38);
            box-shadow: 0 20px 44px rgba(0,0,0,0.3);
        }

        .roll-model-media {
            aspect-ratio: 1 / 1;
            background: rgba(255,255,255,0.06);
        }

        .roll-model-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .roll-model-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            color: #dce8ff;
            background: linear-gradient(135deg, rgba(115, 161, 246, 0.35), rgba(242, 190, 109, 0.35));
        }

        .roll-model-body {
            padding: 11px 12px 13px;
        }

        .roll-model-name {
            margin: 0;
            font-size: .95rem;
            font-weight: 700;
        }

        .roll-model-link {
            margin-top: 5px;
            color: var(--roll-muted);
            font-size: .78rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .roll-empty {
            border: 1px dashed rgba(255,255,255,0.14);
            border-radius: 14px;
            background: rgba(255,255,255,0.02);
            text-align: center;
            padding: 36px 18px;
            color: var(--roll-muted);
        }

        @media (max-width: 991.98px) {
            .roll-grid {
                grid-template-columns: 1fr;
            }

            .roll-calendar-panel {
                position: static;
            }

            .roll-model-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .roll-shell {
                width: calc(100% - 12px);
                padding: 12px 10px 28px;
                border-radius: 18px;
            }

            .roll-hero {
                border-radius: 14px;
                padding: 16px;
            }

            .roll-model-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="roll-shell">
        <div class="roll-topbar">
            <a href="{{ route('club.feed.profile', $club->slug) }}">Back To Club Profile</a>
            <a href="{{ route('club.feed', $club->slug) }}">Back To Feed</a>
        </div>

        <section class="roll-hero">
            <span class="roll-kicker">Club Schedule</span>
            <h1 class="roll-title">Roll Call</h1>
            <p class="roll-copy">Choose a date to see which entertainers are working at {{ $club->name }}. Select any profile card to jump straight into that entertainer's profile page.</p>
        </section>

        <section class="roll-grid">
            <aside class="roll-calendar-panel">
                <div class="roll-calendar-head">
                    <h2>Date Selector</h2>
                    <p>This calendar only enables dates where at least one entertainer is scheduled.</p>
                </div>
                <div id="rollcall-calendar"></div>
                <div class="roll-selected-date">
                    Selected Date:
                    <strong>{{ \Illuminate\Support\Carbon::parse($selectedDate)->format('l, F j, Y') }}</strong>
                </div>
            </aside>

            <div class="roll-models-panel">
                <div class="roll-models-head">
                    <h2>Working Entertainers</h2>
                    <span class="roll-count-chip">{{ $workingModels->count() }} On Shift</span>
                </div>

                @if($workingModels->isNotEmpty())
                    <div class="roll-model-grid">
                        @foreach($workingModels as $model)
                            <a class="roll-model-card" href="{{ route('club.feed.model.profile', [$club->slug, $model]) }}">
                                <div class="roll-model-media">
                                    @if($model->profile_image)
                                        <img src="{{ asset('uploads/' . $model->profile_image) }}" alt="{{ $model->name }}">
                                    @else
                                        <div class="roll-model-fallback">{{ strtoupper(substr($model->name, 0, 2)) }}</div>
                                    @endif
                                </div>
                                <div class="roll-model-body">
                                    <h3 class="roll-model-name">{{ $model->name }}</h3>
                                    <span class="roll-model-link">
                                        <i class="fas fa-arrow-right"></i>
                                        Open profile
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="roll-empty">
                        <h3 class="mb-2">No entertainers are scheduled</h3>
                        <p class="mb-0">Try another date on the calendar to see who is working.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function () {
            const selectedDate = @json($selectedDate);
            const availableDates = @json($availableDates);

            flatpickr('#rollcall-calendar', {
                inline: true,
                defaultDate: selectedDate,
                dateFormat: 'Y-m-d',
                enable: availableDates.length ? availableDates : undefined,
                onChange: function (selectedDates, dateStr) {
                    if (!dateStr) {
                        return;
                    }

                    const url = new URL(window.location.href);
                    url.searchParams.set('date', dateStr);
                    window.location.href = url.toString();
                }
            });
        })();
    </script>
</body>
</html>
