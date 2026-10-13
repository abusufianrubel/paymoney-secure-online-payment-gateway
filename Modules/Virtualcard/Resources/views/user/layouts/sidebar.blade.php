@php
    $virtualcardModules = collect(\Nwidart\Modules\Facades\Module::all())
                            ->filter(function($module) {
                                return isActive($module) && $module->get('type') === 'virtualcard' && $module->get('core') !== true;
                            })->all();

@endphp

@if (handleVirtualcardAccess() && count($virtualcardModules) > 0
&& (
    Common::has_permission(auth()->id(), 'manage_card_holder') || Common::has_permission(auth()->id(), 'manage_virtual_card')||  Common::has_permission(auth()->id(), 'manage_card_topup')  || Common::has_permission(auth()->id(), 'manage_card_withdrawal')
))
    <li class="accordion-item bg-secondary border-0">
        
        <div class="accordion-header {{ request()->route()->named('user.virtualcard_holder.*') || request()->route()->named('user.virtualcard.*') || request()->route()->named('user.topup.*') || request()->route()->named('user.virtualcard_withdrawal.*') ? 'bg-info' : '' }}" id="Virtual-Card">
            <button class="mb-1 shadow-none bg-transparent p-0 d-flex align-items-center list-option h-46 accordion-button accordion-icon collapsed {{ request()->route()->named('user.virtualcard_holder.*') || request()->route()->named('user.virtualcard.*') || request()->route()->named('user.topup.*') || request()->route()->named('user.virtualcard_withdrawal.*') ? 'text-white bg-info' : 'text-info-100' }}" type="button" data-bs-toggle="collapse" data-bs-target="#flush-Virtual-Card" aria-expanded="false" aria-controls="flush-Virtual-Card">
                <span class="ml-12 mr-20">{!! menuSvgIcon('user_dashboard.crypto_buy_sell.*') !!}</span>
                <span class="child-currency d-flex justify-content-start align-items-center">{{ __('Virtual Card') }}
                    @if (checkDemoEnvironment())
                        <small class="new-badge d-flex justify-content-start align-items-center p-1 rounded-pill mx-2">{{ __('Addon') }}</small>
                    @endif
                </span>
            </button>
        </div>

        <div id="flush-Virtual-Card" class="accordion-collapse collapse {{ request()->route()->named('user.virtualcard_holder.*') || request()->route()->named('user.virtualcard.*') || request()->route()->named('user.topup.*') || request()->route()->named('user.virtualcard_withdrawal.*') ? 'show' : '' }}" aria-labelledby="Virtual-Card" data-bs-parent="#accordion-menu">
            <ul class="accordion-body collapse-child ml-28 p-0 pl-16 mr-20">
                @if(Common::has_permission(auth()->id(),'manage_card_holder'))
                <li>
                    <a href="{{ route('user.virtualcard_holder.index') }}" class="mb-2 ml-34 pl-14 f-14 d-flex align-items-center list-option h-46 {{ request()->route()->named('user.virtualcard_holder.*')  ? 'text-white bg-info' : 'text-info-100' }}">{{ __('Card Holders') }}</a>
                </li>
                @endif

                @if(Common::has_permission(auth()->id(),'manage_virtual_card'))
                <li>
                    <a href="{{ route('user.virtualcard.index') }}" class="mb-2 ml-34 pl-14 f-14 d-flex align-items-center list-option h-46 {{ request()->route()->named('user.virtualcard.*')  ? 'text-white bg-info' : 'text-info-100' }}">{{ __('Cards') }}</a>
                </li>
                @endif
                
                @if(Common::has_permission(auth()->id(),'manage_card_topup'))
                <li>
                    <a href="{{ route('user.topup.index') }}" class="mb-2 ml-34 pl-14 f-14 d-flex align-items-center list-option h-46 {{ request()->route()->named('user.topup.*')  ? 'text-white bg-info' : 'text-info-100' }}">{{ __('Topups') }}</a>
                </li>
                @endif
                
                @if(Common::has_permission(auth()->id(),'manage_card_withdrawal'))
                <li>
                    <a href="{{ route('user.virtualcard_withdrawal.index') }}" class="mb-2 ml-34 pl-14 f-14 d-flex align-items-center list-option h-46 {{ request()->route()->named('user.virtualcard_withdrawal.*')  ? 'text-white bg-info' : 'text-info-100' }}">{{ __('Withdrawals') }}</a>
                </li>
                @endif
            </ul>
        </div>
    </li>
@endif
