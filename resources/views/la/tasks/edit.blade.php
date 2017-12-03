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
					{{--@la_form($module)--}}
				@la_input($module, 'title')

				{{--@la_input($module, 'work_id')--}}
				<div class="form-group">
					<label for="word_id">Work Id :</label>
					<select class="form-control select2-hidden-accessible" data-placeholder="Enter Work Id" rel="select2" name="work_id" tabindex="0" aria-hidden="true">
						@foreach ($work_list as $k => $v)
							<option value="{{$k}}" @if($k == $task->work_id) selected @endif>{{$v}}</option>
						@endforeach
					</select>
					<span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100px;">
					<span class="selection">
				</span>
				<span class="dropdown-wrapper" aria-hidden="true"></span>
			</span>
		</div>
				{{--@la_input($module, 'work_id')--}}

				{{--<div class="form-group">--}}
					{{--<label for="user_key">User Key :</label>--}}
					{{--<select class="form-control select2-hidden-accessible" data-placeholder="Enter User Key" rel="select2" name="user_key" tabindex="1" aria-hidden="true">--}}
						{{--@foreach ($user_list as $k => $v)--}}
							{{--<option value="{{$k}}" @if($k == $task->user_key) selected @endif>{{$v}}</option>--}}
						{{--@endforeach--}}
					{{--</select>--}}
					{{--<span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100px;">--}}
					{{--<span class="selection">--}}
				{{--</span>--}}
				{{--<span class="dropdown-wrapper" aria-hidden="true"></span>--}}
			{{--</span>--}}
				{{--</div>--}}

				@la_input($module, 'user_key')



				{{--@la_input($module, 'run_time')--}}
				<div class="form-group"><label for="run_time">Run Time* :</label><div class='input-group datetime'> <input id= 'datetimepicker' class="form-control" placeholder="Enter Run Time" required="1" name="run_time" type="text" value=""></div>
					<input type="hidden" id="run_time_v" value="@if(1){{$task->run_time}}@endif">
					@la_input($module, 'time_point')
					@la_input($module, 'product_id')
					@la_input($module, 'code')
					@la_input($module, 'money')
					@la_input($module, 'voucher_id')
					@la_input($module, 'is_kdb_pay')
					@la_input($module, 'prize_number')
					@la_input($module, 'is_wait_sjk')
                    @if (in_array($task->status, [0, 1]))
					    @la_input($module, 'status')
                    @endif
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
    $("#datetimepicker").datetimepicker({
        format: 'YYYY-MM-DD HH:mm::ss',
    });

    $("#datetimepicker").val($("#run_time_v").val());
});
</script>
@endpush
