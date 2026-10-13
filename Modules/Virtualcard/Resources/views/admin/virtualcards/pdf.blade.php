@extends('admin.pdf.app')

@section('title', __('Card List pdf'))

@section('content')
    <div class="mt-30">
        <table class="table">
            <tr class="table-header">
                <td>{{ __('Card Holder') }}</td>
                <td>{{ __('Number') }}</td>
                <td>{{ __('Brand') }}</td>
                <td>{{ __('Currency') }}</td>
                <td>{{ __('Providers') }}</td>
                <td>{{ __('Expired') }}</td>
                <td>{{ __('Assigned') }}</td>
                <td>{{ __('Status') }}</td>
            </tr>

            @foreach ($virtualcards as $virtualcard)
                <tr class="table-body">
                    <td>{{ cardTitle($virtualcard->virtualcardHolder) }}</td>
                    <td>{!!
                        $virtualcard->card_number 
                            ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $virtualcard->card_brand)) . '_icon') .  maskCardNumberForLogo($virtualcard->card_number) 
                            : '-'
                    !!}</td>
                    <td>{{ $virtualcard->card_brand }}</td>
                    <td>{{ $virtualcard->currency_code }}</td>
                    <td>{{ $virtualcard->virtualcardProvider?->name }}</td>
                    <td>{{ $virtualcard->expiry_year ? \Carbon\Carbon::createFromDate($virtualcard->expiry_year, $virtualcard->expiry_month, 1) ->format('m-Y') : '-' }}</td>
                    <td>{{ dateFormat($virtualcard->created_at) }}</td>
                    <td>{{ $virtualcard->status }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection


