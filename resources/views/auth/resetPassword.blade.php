@extends('layouts.auth')

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title">Reset password</h5>
            <div class="nk-block-des">
                <p>If you forgot your password, well, then we’ll email you instructions to
                    reset your password.</p>
            </div>
        </div>
    </div><!-- .nk-block-head -->
    <form action="#">
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
            <button class="btn btn-lg btn-primary btn-block">Send Reset Link</button>
        </div>
    </form>
    <div class="form-note-s2 pt-5">
        <a href="{{ route('login') }}"><strong>Return to login</strong></a>
    </div>
@endsection
