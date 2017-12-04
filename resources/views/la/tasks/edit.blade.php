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

				<div class="form-group">
					<label for="user_key">User Key :</label>
					<select class="form-control select2-hidden-accessible" data-placeholder="Enter User Key" rel="select2" name="user_key" tabindex="1" aria-hidden="true">
						@foreach ($user_list as $k => $v)
							<option value="{{$k}}" @if($k == $task->user_key) selected @endif>{{$v}}</option>
						@endforeach
					</select>
					<span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100px;">
					<span class="selection">
				</span>
				<span class="dropdown-wrapper" aria-hidden="true"></span>
			</span>
				</div>

				{{--@la_input($module, 'user_key')--}}



				{{--@la_input($module, 'run_time')--}}
				<div class="form-group"><label for="run_time">Run Time* :</label><div class='input-group datetime'> <input id= 'datetimepicker' class="form-control" placeholder="Enter Run Time" required="1" name="run_time" type="text" value=""></div>
					<input type="hidden" id="run_time_v" value="@if(1){{$task->run_time}}@endif">
					{{--@la_input($module, 'time_point')--}}
					{{--@la_input($module, 'product_id')--}}

					<div class="form-group">
						<label for="time_point">Time Point :</label>
						<input class="form-control valid" placeholder="Enter Time Point" data-rule-maxlength="256" name="time_point" type="text" value="{{$task->time_point}}" aria-invalid="false">
					</div>

					<div class="form-group">
						<label for="product_id">Product Id :</label>
						<input class="form-control valid" placeholder="Enter Product Id" name="product_id" type="number" value="{{$task->product_id}}" aria-invalid="false">
					</div>
					@if ($task->img_url != "")
						<div class="form-group"><label for="img_url">Img Url:</label><div class='input-group'>
								<img src="{{asset($task->img_url)}}">
						</div>
					@endif
					{{--@la_input($module, 'code')--}}
					{{--@la_input($module, 'money')--}}
					{{--@la_input($module, 'voucher_id')--}}

							<div class="form-group">
								<label for="code">Code :</label>
								<input class="form-control" placeholder="Enter Code" data-rule-maxlength="256" name="code" type="text" value="{{$task->code}}">
							</div>

							<div class="form-group">
								<label for="money">Money :</label>
								<input class="form-control valid" placeholder="Enter Money" name="money" type="number" value="{{$task->money}}" aria-invalid="false">
							</div>

							<div class="form-group">
								<label for="voucher_id">Voucher Id :</label>
								<input class="form-control valid" placeholder="Enter Voucher Id" name="voucher_id" type="number" value="{{$task->voucher_id}}" aria-invalid="false">
							</div>

							<div class="form-group">
								<label for="is_kdb_pay">Is Kdb Pay :</label>
								<select class="form-control select2-hidden-accessible" data-placeholder="Enter Is Kdb Pay" rel="select2" name="is_kdb_pay" tabindex="-1" aria-hidden="true">
									<option value="0" @if(0 == $task->is_kdb_pay) selected @endif>0</option>
									<option value="1" @if(1 == $task->is_kdb_pay) selected @endif>1</option>
								</select>
							</div>
					{{--@la_input($module, 'is_kdb_pay')--}}
					{{--@la_input($module, 'prize_number')--}}
					{{--@la_input($module, 'is_wait_sjk')--}}

							<div class="form-group">
								<label for="prize_number">Prize Number :</label>
								<input class="form-control valid" placeholder="Enter Prize Number" name="prize_number" type="number" value="{{$task->prize_number}}" aria-invalid="false">
							</div>

					<div class="form-group">
						<label for="is_wait_sjk">Is Wait Sjk :</label>
						<select class="form-control select2-hidden-accessible" data-placeholder="Enter Is Wait Sjk" rel="select2" name="is_wait_sjk" tabindex="-1" aria-hidden="true">
							<option value="0" @if(0 == $task->is_wait_sjk) selected @endif>0</option>
							<option value="1" @if(1 == $task->is_wait_sjk) selected @endif>1</option>
						</select>
					</div>
                    {{--@if (in_array($task->status, [0, 1]))--}}
					    {{--@la_input($module, 'status')--}}
                    {{--@endif--}}
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
