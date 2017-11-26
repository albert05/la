@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/workconfigs') }}">WorkLists</a> :
@endsection
@section("contentheader_description", $workconfig->$view_col)
@section("section", "Workconfigs")
@section("section_url", url(config('laraadmin.adminRoute') . '/workconfigs'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Workconfigs Edit : ".$workconfig->$view_col)

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
				{!! Form::model($worklist, ['route' => [config('laraadmin.adminRoute') . '.workconfigs.update', $workconfig->id ], 'method'=>'PUT', 'id' => 'workconfig-edit-form']) !!}
					@la_form($module)

					<br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/workconfigs') }}">Cancel</a></button>
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
	$("#workconfig-edit-form").validate({
		
	});
});
</script>
@endpush
