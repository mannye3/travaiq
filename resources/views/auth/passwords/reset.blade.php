@extends('layouts.master')

@section('content')



<div class="container">

	<div class="row">
	<div class="col-md-4 col-md-offset-4">

	
	<div class="my-account style-1 margin-top-5 margin-bottom-40">
<p> Password must have at least 8 characters, one uppercase, one lowercase, one number and one special
                character.</p>
		<ul class="tabs-nav">
			<li class=""><a href="#tab1">Reset Password</a></li>

		</ul>

		<div class="tabs-container alt">

			<!-- Login -->
			<div class="tab-content" id="tab1" style="display: none;">
				     <form method="POST" class="register" action="{{ route('resetpasswordsubmit') }}">
                        @csrf
					
					
				<input hidden  name="token" value="{{ $token }}">
                    <input hidden  name="email" value="{{ $email }}">


				<p class="form-row form-row-wide">
					<label for="password1">Password
						<i class="im im-icon-Lock-2"></i>
						
                         <input type="password" required class="input-text @error('password') is-invalid @enderror" name="password" id="password"   />
					</label>
				</p>

                <p class="form-row form-row-wide">
					<label for="password1">Confirm Password:
						<i class="im im-icon-Lock-2"></i>
						
                         <input type="password" required class="input-text @error('password') is-invalid @enderror" name="password_confirmation" id="password"   />
					</label>
				</p>

				

                

				<p class="form-row">
					<input type="submit" class="button border fw margin-top-10"  value="Submit" />
				</p>

				</form>
			</div>
			<p class="lost_password">
						  <a href="{{ route('login') }}"><strong>Return to login</strong></a>
					</p>

			<!-- Register -->
			

		</div>
	</div>



	</div>
	</div>

</div>

 

@endsection
