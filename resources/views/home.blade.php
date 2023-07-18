@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Two Factor Authentication') }}</div>

                    <div class="card-body">
                        @if (session('status') == "two-factor-authentication-disabled")
                            <div class="alert alert-danger" role="alert">
                                Two factor authentication has been disabled
                            </div>
                        @endif

                        @if (session('status') == "two-factor-authentication-enabled")
                            <div class="alert alert-success" role="alert">
                                Two factor authentication has been enabled
                            </div>
                        @endif
                        <form method="post" action="/user/two-factor-authentication">
                            @csrf

                            @if (auth()->user()->two_factor_secret)
                                @method('DELETE')
                                <div class="pb-3">
                                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                                </div>
                                <div class="mt-4">
                                    <h3>Recovery Codes</h3>
                                    <ul class="list-group mb-2">
                                        @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes)) as $code)
                                            <li class="list-group-item">{{ $code }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button class="btn btn-danger">
                                    Disable
                                </button>
                            @else
                                <button class="btn btn-success">
                                    Enable
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Phone Number Verification') }}</div>

                    <div class="card-body">
                        @if (auth()->user()->is_phone_verified === 0)
                            <div class="alert alert-danger" role="alert">
                                Phone Number is not Verified!!
                            </div>

                            <form action="{{ route('Phone.Verify') }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <label for="email"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>
                                    <div class="col-md-6">
                                        <input id="email" type="tel"
                                               class="form-control @error('Phone_Number') is-invalid @enderror"
                                               name="Phone_Number"
                                               value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                                        @error('Phone_Number')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Verified Now!</button>
                            </form>
                        @endif

                        @if (auth()->user()->is_phone_verified === 1)
                            <div class="alert alert-success" role="alert">
                                Phone Number is Verified!!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @php
                $alternate_email = \App\Models\AlternativeEmail::where('user_id', auth()->user()->id)->latest('id')->first();
            @endphp
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Alternative Email Verification') }}</div>

                    <div class="card-body">
                        @if (empty($alternate_email))
                            <div class="alert alert-danger" role="alert">
                                Alternative Email is not Verified!!
                            </div>

                            <form action="{{ route('Email.Verify') }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <label for="email"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Email Address') }}</label>
                                    <div class="col-md-6">
                                        <input id="email" type="email"
                                               class="form-control @error('Email_Address') is-invalid @enderror"
                                               name="Email_Address"
                                               value="{{ old('Email_Address') }}" required autocomplete="Email_Address"
                                               autofocus>
                                        @error('Email_Address')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Verified Now!</button>
                            </form>
                        @else
                            <div class="alert alert-success" role="alert">
                                Email Address is Verified!!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
