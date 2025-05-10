@extends('layouts.auth')

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title">Sign-In</h5>
            <div class="nk-block-des">
                <p>Access the DashLite panel using your email and passcode.</p>
            </div>
        </div>
    </div><!-- .nk-block-head -->
    <form action="{{ route('loginPost') }}" method="POST">
        @csrf
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="default-01">Email</label>

            </div>
            <div class="form-control-wrap">
                <input type="text" name="email" class="form-control form-control-lg" id="default-01"
                    placeholder="Enter your email address">
            </div>
        </div><!-- .form-group -->
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password">Password</label>
                <a class="link link-primary link-sm" tabindex="-1" href="{{ route('forgetPassword') }}">Forgot
                    Password?</a>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg"
                    data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" name="password" class="form-control form-control-lg" id="password"
                    placeholder="Enter your passcode">
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-lg btn-primary btn-block">Sign in</button>
        </div>
    </form>
@endsection
