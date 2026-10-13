@extends('admin.pdf.app')

@section('title', __('Card Holder pdf'))

@section('content')
    <div class="mt-30">
        <table class="table">
            <tr class="table-header">
                <td>{{ __('Card Type') }}</td>
                <td>{{ __('User') }}</td>
                <td>{{ __('Name') }}</td>
                <td>{{ __('Country') }}</td>
                <td>{{ __('Status') }}</td>
                <td>{{ __('Date') }}</td>
            </tr>

            @foreach ($virtualcardHolders as $holder)
            @php
                $name = getColumnValue($holder);
                if (\Modules\Virtualcard\Enums\CardHolderTypes::BUSINESS->value == $holder->card_holder_type) {
                    $name = $holder->business_name . ' (' . getColumnValue($holder) . ')';
                }
            @endphp
                <tr class="table-body">
                    <td>{{ ucwords($holder->card_holder_type) }}</td>
                    <td>{{ getColumnValue($holder->user) }}</td>
                    <td>{{ $name ?? '-' }}</td>
                    <td>{{ $holder->country ?? '-' }}</td>
                    <td>{{ getStatus($holder->status) }}</td>
                    <td>{{ dateFormat($holder->created_at) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
