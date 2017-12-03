@extends("la.layouts.app")

@section("contentheader_title", "Captcha")
@section("contentheader_description", "Captcha Study")
@section("section", "Captcha")
@section("sub_section", "Study")
@section("htmlheader_title", "Captcha Study")

@section("main-content")
	<form action="" method="post">
		<img src="{{asset('la-assets/img/captcha/captcha.png')}}">
		<input type="text" name="code">
		<input name="send" type="submit" value="send" />
	</form>
@endsection

@push('scripts')
<script>
$(function () {

});
</script>
@endpush