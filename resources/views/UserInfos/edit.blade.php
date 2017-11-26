@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/userinfos') }}">UserInfos</a> :
@endsection
@section("contentheader_description", $userinfo->$view_col)
@section("section", "UserInfos")
@section("section_url", url(config('laraadmin.adminRoute') . '/userinfos'))
@section("sub_section", "Edit")

@section("htmlheader_title", "UserInfos Edit : ".$userinfo->$view_col)

@section("main-content")


@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="box">
	<div class="box-header">
		
	</div>

	<div class="box-body">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! Form::model($userinfo, ['route' => [config('laraadmin.adminRoute') . '.userinfos.update', $userinfo->id ], 'method'=>'PUT', 'id' => 'userinfo-edit-form']) !!}
					@la_form($module)
					<br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/userinfos') }}">Cancel</a></button>
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
	$("#userinfo-edit-form").validate({
		
	});
});
</script>
@endpush
