@extends("la.layouts.app")

@section("contentheader_title", "Captcha")
@section("contentheader_description", "Captcha Study")
@section("section", "Captcha")
@section("sub_section", "Study")
@section("htmlheader_title", "Captcha Study")

@section("main-content")
	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					{!! Form::model(null, ['route' => [config('laraadmin.adminRoute') . '.captchas.store'], 'method'=>'PUT', 'id' => 'captcha-edit-form']) !!}
					<img src="{{asset('la-assets/img/captcha/captcha.png')}}">
					<div class="form-group"><label for="run_time">Enter Code* :</label><div class='input-group'> <input class="form-control" placeholder="Enter Code" required="1" name="code" type="text" value=""></div>
						<br>
						<div class="form-group">
							{!! Form::submit( 'Store', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/captchas') }}">Cancel</a></button>
						</div>
						{!! Form::close() !!}

					</div>
				</div>
			</div>
		</div>
@endsection

@push('scripts')
<script>
$(function () {

});
</script>
@endpush