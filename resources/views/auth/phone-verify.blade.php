@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Phone Verification') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('OTP.Verify') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="two_factor_code" class="col-md-4 col-form-label text-md-right">{{ __('Verification Code') }}</label>
                                <div class="col-md-6">
                                    <input id="two_factor_code" type="text" class="form-control @error('two_factor_code') is-invalid @enderror" name="two_factor_code" value="{{ old('two_factor_code') }}" required autocomplete="two_factor_code" autofocus>
                                    @error('Code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="User_ID" value="{{ $User_ID }}">

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit Verification Code') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
