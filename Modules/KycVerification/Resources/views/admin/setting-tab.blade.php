@if (verifyKycModulesCredentialRequirement())
    <div class="box">
        <div class="panel-body ml-20">
            <ul class="nav nav-tabs cus f-14">
                <li class="nav-item">
                    <a class="nav-link {{ isset($sub_menu) && $sub_menu == 'kyc_settings' ? 'active' : '' }}" href="{{ route('admin.kyc.settings.create') }}">{{ __('General Setting') }}</a>
                </li>
                @if (Common::has_permission(auth('admin')->user()->id, 'view_kyc_credential_setting'))
                    <li class="nav-item">
                        <a class="nav-link {{ isset($sub_menu) && $sub_menu == 'credential_settings' ? 'active' : '' }}" href="{{ route('admin.kyc.credentials.active-provider-setting') }}">{{ __('Credential Setting') }}</a>
                    </li>
                @endif

            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
@endif
