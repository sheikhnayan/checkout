@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-sliders-h text-warning me-2"></i> Dynamic Form Builder</h4>
          <p class="text-muted small mb-0">Super Admin configurator for field labels, required validation rules, visibility, and display ordering.</p>
        </div>
      </div>

      <!-- Form Selector Tabs -->
      <div class="d-flex gap-2 border-bottom pb-2" style="border-color: var(--nr-border) !important;">
        <a href="{{ route('admin.nightly-reports.form-builder.index', ['type' => 'nightly']) }}" class="btn btn-sm {{ $reportType === 'nightly' ? 'btn-gold' : 'btn-outline-secondary' }}">
          Nightly Report Form
        </a>
        <a href="{{ route('admin.nightly-reports.form-builder.index', ['type' => 'boutique']) }}" class="btn btn-sm {{ $reportType === 'boutique' ? 'btn-gold' : 'btn-outline-secondary' }}">
          Boutique Store Form
        </a>
        <a href="{{ route('admin.nightly-reports.form-builder.index', ['type' => 'coh']) }}" class="btn btn-sm {{ $reportType === 'coh' ? 'btn-gold' : 'btn-outline-secondary' }}">
          COH Vault Form
        </a>
        <a href="{{ route('admin.nightly-reports.form-builder.index', ['type' => 'incident']) }}" class="btn btn-sm {{ $reportType === 'incident' ? 'btn-gold' : 'btn-outline-secondary' }}">
          Incident Report Form
        </a>
        <a href="{{ route('admin.nightly-reports.form-builder.index', ['type' => 'witness']) }}" class="btn btn-sm {{ $reportType === 'witness' ? 'btn-gold' : 'btn-outline-secondary' }}">
          Witness Statement Form
        </a>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.nightly-reports.form-builder.update') }}">
    @csrf
    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th style="width: 80px;">Order</th>
              <th>Field Key</th>
              <th>Display Label</th>
              <th>Visible on Form</th>
              <th>Mandatory / Required</th>
              <th>Help Tooltip</th>
            </tr>
          </thead>
          <tbody>
            @foreach($configs as $cfg)
            <tr>
              <td>
                <input type="number" name="fields[{{ $cfg->id }}][sort_order]" class="form-control form-control-sm text-center" value="{{ $cfg->sort_order }}" style="width: 70px;" />
              </td>
              <td><code>{{ $cfg->field_key }}</code></td>
              <td>
                <input type="text" name="fields[{{ $cfg->id }}][label]" class="form-control form-control-sm" value="{{ $cfg->label }}" />
              </td>
              <td>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="fields[{{ $cfg->id }}][visible]" value="1" {{ $cfg->visible ? 'checked' : '' }} />
                </div>
              </td>
              <td>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="fields[{{ $cfg->id }}][required]" value="1" {{ $cfg->required ? 'checked' : '' }} />
                </div>
              </td>
              <td>
                <input type="text" name="fields[{{ $cfg->id }}][hint]" class="form-control form-control-sm" value="{{ $cfg->hint }}" placeholder="Tooltip..." />
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex justify-content-end py-3">
        <button type="submit" class="btn btn-gold px-4"><i class="fas fa-save me-1"></i> Save Form Configurations</button>
      </div>
    </div>
  </form>
</div>
@endsection
