@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/tasks') }}">Tasks</a> :
@endsection
@section("contentheader_description", $task->$view_col)
@section("section", "Tasks")
@section("section_url", url(config('laraadmin.adminRoute') . '/tasks'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Task Edit : ".$task->$view_col)

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
				{!! Form::model($task, ['route' => [config('laraadmin.adminRoute') . '.tasks.update', $task->id ], 'method'=>'PUT', 'id' => 'task-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'name')
					@la_input($module, 'designation')
					@la_input($module, 'gender')
					@la_input($module, 'mobile')
					@la_input($module, 'mobile2')
					@la_input($module, 'email')
					@la_input($module, 'dept')
					@la_input($module, 'city')
					@la_input($module, 'address')
					@la_input($module, 'about')
					@la_input($module, 'date_birth')
					@la_input($module, 'date_hire')
					@la_input($module, 'date_left')
					@la_input($module, 'salary_cur')
					--}}
                    <div class="form-group">
						<label for="role">Role* :</label>
						<select class="form-control" required="1" data-placeholder="Select Role" rel="select2" name="role">
							<?php $roles = App\Role::all(); ?>
							@foreach($roles as $role)
								@if($role->id != 1 || Entrust::hasRole("SUPER_ADMIN"))
									@if($user->hasRole($role->name))
										<option value="{{ $role->id }}" selected>{{ $role->name }}</option>
									@else
										<option value="{{ $role->id }}">{{ $role->name }}</option>
									@endif
								@endif
							@endforeach
						</select>
					</div>
					<br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/tasks') }}">Cancel</a></button>
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
	$("#task-edit-form").validate({
		
	});
});
</script>
@endpush
