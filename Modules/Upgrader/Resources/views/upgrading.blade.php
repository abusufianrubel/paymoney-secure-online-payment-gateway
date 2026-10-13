@extends('admin.layouts.master')
@section('title', __('System Upgrade'))

@section('head_style')
    <link rel="stylesheet" href="{{ asset('Modules/Upgrader/Resources/assets/css/upgrade-progress.min.css') }}">
@endsection

@section('page_content')
    <div class="upgrade-shell" id="upgrade-app"
        data-files-url="{{ $filesUrl }}"
        data-finalize-url="{{ $finalizeUrl }}"
        data-home-url="{{ $homeUrl }}"
        data-retry-url="{{ $retryUrl }}"
        data-current="{{ $currentVersion }}"
        data-target="{{ $targetVersion }}">

        <div class="upgrade-hero card border-0 shadow-sm">
            <div class="upgrade-hero__decor" aria-hidden="true">
                <span class="upgrade-hero__orb upgrade-hero__orb--1"></span>
                <span class="upgrade-hero__orb upgrade-hero__orb--2"></span>
            </div>
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <span class="upgrade-badge upgrade-badge--live" id="upgrade-status-badge">{{ __('In progress') }}</span>
                        <h4 class="upgrade-title mt-2 mb-1">{{ __('Upgrading your system') }}</h4>
                        <p class="upgrade-subtitle mb-0">{{ __('Please keep this page open until the process completes.') }}</p>
                    </div>
                    <div class="upgrade-version-pills">
                        <span class="upgrade-pill upgrade-pill--muted">{{ $currentVersion }}</span>
                        <span class="upgrade-pill-arrow" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="upgrade-pill upgrade-pill--accent">{{ $targetVersion }}</span>
                    </div>
                </div>

                <div class="upgrade-steps" id="upgrade-steps">
                    <div class="upgrade-step is-active" data-step="prepare">
                        <div class="upgrade-step__icon">
                            <span class="upgrade-step__dot"></span>
                        </div>
                        <div class="upgrade-step__label">{{ __('Prepare') }}</div>
                    </div>
                    <div class="upgrade-step" data-step="files">
                        <div class="upgrade-step__icon"><span class="upgrade-step__dot"></span></div>
                        <div class="upgrade-step__label">{{ __('Files') }}</div>
                    </div>
                    <div class="upgrade-step" data-step="database">
                        <div class="upgrade-step__icon"><span class="upgrade-step__dot"></span></div>
                        <div class="upgrade-step__label">{{ __('Database') }}</div>
                    </div>
                    <div class="upgrade-step" data-step="finish">
                        <div class="upgrade-step__icon"><span class="upgrade-step__dot"></span></div>
                        <div class="upgrade-step__label">{{ __('Finish') }}</div>
                    </div>
                </div>

                <div class="upgrade-progress-track mt-4">
                    <div class="upgrade-progress-bar" id="upgrade-progress-bar" style="width: 8%"></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4 upgrade-console-card">
            <div class="card-header upgrade-console-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="upgrade-console-dots" aria-hidden="true">
                        <i></i><i></i><i></i>
                    </span>
                    <span class="fw-semibold">{{ __('Activity log') }}</span>
                </div>
                <span class="upgrade-console-clock" id="upgrade-clock">--:--:--</span>
            </div>
            <div class="card-body p-0">
                <div class="upgrade-console" id="upgrade-log" role="log" aria-live="polite">
                    <div class="upgrade-log-empty" id="upgrade-log-empty">
                        <div class="upgrade-spinner" aria-hidden="true"></div>
                        <p>{{ __('Initializing upgrade…') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="upgrade-actions mt-4 d-none" id="upgrade-actions">
            <a href="{{ route('systemUpdate.upgrade') }}" class="btn btn-theme-danger f-14" id="upgrade-back-btn">{{ __('Back to update page') }}</a>
            <button type="button" class="btn custom-btn-update f-14 d-none" id="upgrade-retry-btn">{{ __('Retry database phase') }}</button>
            <a href="{{ $homeUrl }}" class="btn custom-btn-submit f-14 d-none" id="upgrade-home-btn">{{ __('Go to dashboard') }}</a>
        </div>
    </div>
@endsection

@push('extra_body_scripts')
    <script src="{{ asset('Modules/Upgrader/Resources/assets/js/upgrade-run.min.js') }}"></script>
@endpush
