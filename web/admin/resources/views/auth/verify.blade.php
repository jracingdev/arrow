@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('lang.verify_your_email_address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ trans('lang.a_fresh_verification_link_has_been_sent_to_your_email_address') }}
                        </div>
                    @endif

                    {{ trans('lang.before_proceeding_please_check_your_email_for_a_verification_link') }}
                    {{ trans('lang.if_you_did_not_receive_the_email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ trans('lang.click_here_to_request_another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
