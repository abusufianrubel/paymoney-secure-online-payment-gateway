@php
    $virtualcardModules = collect(\Nwidart\Modules\Facades\Module::all())
                            ->filter(function($module) {
                                return isActive($module) && $module->get('type') === 'virtualcard' && $module->get('core') !== true;
                            })->all();

@endphp

@if (
    count($virtualcardModules) > 0
    && (
        Common::has_permission(auth('admin')->user()->id, 'view_card_holder') || Common::has_permission(auth('admin')->user()->id, 'view_virtual_card')||  Common::has_permission(auth('admin')->user()->id, 'view_card_withdrawal')  || Common::has_permission(auth('admin')->user()->id, 'view_card_topup') || Common::has_permission(auth('admin')->user()->id, 'view_card_category') || Common::has_permission(auth('admin')->user()->id, 'view_card_fees_limit') || Common::has_permission(auth('admin')->user()->id, 'view_card_preference') || Common::has_permission(auth('admin')->user()->id, 'view_card_provider')
    )
)

<li <?= (isset($menu) &&  $menu == 'virtualcard') ? ' class="active treeview"' : 'treeview'?> >
    <a href="javascript: void(0)">
        <i class="fa fa-credit-card"></i>
        <span>{{ __('Virtual Card') }}</span>
        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
    </a>
    <ul class="treeview-menu">
    
        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_holder'))
        <li <?= isset($sub_menu) && $sub_menu == 'cardHolder' ? ' class="active child"' : 'child'?> >
            <a href="{{ route('admin.virtualcard_holder.index') }}"><i class="fa fa-pencil-square-o"></i><span>{{ __('Card Holders') }}</span></a>
        </li>
        @endif
        
        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_virtual_card'))
        <li <?= isset($subMenu) && $subMenu == 'virtualcard' ? ' class="active child"' : 'child'?> >
            <a href="{{ route('admin.virtualcard.index') }}"><i class="fa fa-credit-card-alt"></i><span>{{ __('Cards') }}</span></a>
        </li>
        @endif
        
        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_topup'))
        <li <?= isset($sub_menu) && $sub_menu == 'virtualcard_topup' ? ' class="active child"' : 'child'?> >
            <a href="{{ route('admin.virtualcard_topup.index') }}"><i class="fa fa-money"></i><span>{{ __('Topups') }}</span></a>
        </li>
        @endif

        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_withdrawal'))
        <li <?= isset($subMenu) && $subMenu == 'virtualcardWithdrawal' ? ' class="active child"' : 'child'?> >
            <a href="{{ route('admin.virtualcard_withdrawal.index') }}"><i class="fa fa-google-wallet"></i><span>{{ __('Withdrawals') }}</span></a>
        </li>
        @endif

        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_category'))
        <li <?= isset($sub_menu) && $sub_menu == 'categories' ? ' class="active child"' : 'child'?> >
            <a href="{{ route('admin.card_categories.index') }}"><i class="fa fa-sitemap"></i><span>{{ __('Categories') }}</span></a>
        </li>
        @endif

        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_fees_limit'))
        <li <?= isset($sub_menu) && $sub_menu == 'card_fees' ? ' class="active child"' : 'child'?> >
            <a href="{{ route('admin.card_fees.index') }}"><i class="fa fa-sliders"></i><span>{{ __('Fees Limits') }}</span></a>
        </li>
        @endif

        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_provider'))
        <li <?= isset($sub_menu) && $sub_menu == 'provider' ? ' class="active child"' : 'child'?> >
            <a href="{{ route('admin.virtualcard_provider.index') }}"><i class="fa fa-globe"></i><span>{{ __('Providers') }}</span></a>
        </li>
        @endif

        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_card_preference'))
        <li <?= isset($sub_menu) && $sub_menu == 'preference' ? ' class="active child"' : 'child'?> >
            <a href="{{ route('admin.virtualcard_preference.create') }}"><i class="fa fa-cogs"></i><span>{{ __('Preference') }}</span></a>
        </li>
        @endif 
    </ul>
</li>
@endif