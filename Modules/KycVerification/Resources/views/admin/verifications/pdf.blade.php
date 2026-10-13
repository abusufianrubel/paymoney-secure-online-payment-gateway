@extends('admin.pdf.app')

@section('title', __('Verification pdf'))

@section('content')
    <div class="mt-30">
        <table class="table">
            <tr class="table-header">
                <td>{{ __('Date') }}</td>
                <td>{{ __('User') }}</td>
                <td>{{ __('Provider') }}</td>
                <td>{{ __('Type') }}</td>
                <td>{{ __('Status') }}</td>
            </tr>

            @foreach($verifications as $verification)
                <tr class="table-body">
                    <td>{{ dateFormat($verification->created_at) }}</td>
                    <td>{{ getColumnValue($verification->user)  }}</td>
                    <td>{{ getColumnValue($verification->provider, 'name', '')  }}</td>
                    <td>{{ ucwords($verification->verification_type) }}</td>
                    <td>{{ $verification->status }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection