@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/tasks') }}">任务</a> :
@endsection
@section("contentheader_description", $task->$view_col)
@section("section", "任务")
@section("section_url", url(config('laraadmin.adminRoute') . '/tasks'))
@section("sub_section", "编辑")

@section("htmlheader_title", "任务编辑 : ".$task->$view_col)

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
				<div class="form-group control-all">
					<label for="title">任务标题 :</label>
					<input class="form-control" placeholder="输入任务标题" data-rule-maxlength="256" name="title" type="text" value="{{$task->title}}">
				</div>

				<div class="form-group control-all">
					<label for="word_id">任务类型 :</label>
					<select class="form-control select2-hidden-accessible work-select" data-placeholder="选择任务类型" rel="select2" name="work_id" tabindex="0" aria-hidden="true">
						@foreach ($work_list as $k => $v)
							<option value="{{$k}}" @if($k == $task->work_id) selected @endif>{{$v}}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group control-all">
					<label for="user_key">用户标识 :</label>
					<select class="form-control select2-hidden-accessible" data-placeholder="选择用户标识" rel="select2" name="user_key" tabindex="1" aria-hidden="true" disabled="disabled">
						@foreach ($user_list as $k => $v)
							<option value="{{$k}}" @if($k == $task->user_key) selected @endif>{{$v}}</option>
						@endforeach
					</select>
				</div>

				{{--@la_input($module, 'user_key')--}}

				<div class="form-group control-all">
					<label for="run_time">脚本开始时间* :</label>
					<div class='input-group datetime'>
						<input id= 'datetimepicker' class="form-control" placeholder="选择脚本开始时间" required="1" name="run_time" type="text" value="{{$task->run_time}}" >
					</div>
				</div>
				<input type="hidden" id="run_time_v" value="{{$task->run_time}}">

					<div class="form-group  control-exchange control-order control-abcGift control-icbcGift">
						<label for="time_point">任务执行时间点 :</label>
						<input class="form-control valid" placeholder="输入任务请求时间点" data-rule-maxlength="256" name="time_point" type="text" value="{{$task->time_point}}" aria-invalid="false" >
					</div>

					<div class="form-group  control-exchange control-order  control-transfer control-abcGift control-icbcGift">
						<label for="product_id">产品ID :</label>
						<input class="form-control valid" placeholder="输入产品ID" name="product_id" value="{{$task->product_id}}" aria-invalid="false">
					</div>
					@if ($task->img_url != "")
						<div class="form-group control-exchange">
							<label for="img_url">图片验证码 :</label>
							<div class='input-group'>
								<img src="{{asset($task->img_url)}}">
							</div>
						</div>
					@endif
							<div class="form-group control-exchange control-abcGift control-icbcGift">
								<label for="code">验证码 :</label>
								<input class="form-control" placeholder="输入上图图片验证码" data-rule-maxlength="256" name="code" type="text" value="{{$task->code}}">
							</div>

							<div class="form-group control-order control-transfer">
								<label for="money">金额 :</label>
								<input class="form-control valid" placeholder="输入投资金额" name="money" type="number" value="{{$task->money}}" aria-invalid="false">
							</div>

							<div class="form-group control-order">
								<label for="voucher_id">加息券ID :</label>
								<input class="form-control valid" placeholder="输入加息券Id" name="voucher_id" type="text" value="{{$task->voucher_id}}" aria-invalid="false">
							</div>

							<div class="form-group control-order control-abcGift">
								<label for="is_kdb_pay">是否使用口袋宝 :</label>
								<select class="form-control select2-hidden-accessible" data-placeholder="选择是否使用口袋宝" rel="select2" name="is_kdb_pay" tabindex="0" aria-hidden="true">
									<option value="0" @if(0 == $task->is_kdb_pay) selected @endif>否</option>
									<option value="1" @if(1 == $task->is_kdb_pay) selected @endif>是</option>
								</select>
							</div>

							<div class="form-group control-exchange">
								<label for="prize_number">券数量 :</label>
								<input class="form-control valid" placeholder="输入券数量" name="prize_number" type="number" value="{{$task->prize_number}}" aria-invalid="false">
							</div>

					<div class="form-group control-order control-exchange">
						<label for="is_wait_sjk">是否等待三剑客 :</label>
						<select class="form-control select2-hidden-accessible" data-placeholder="选择是否等待三剑客" rel="select2" name="is_wait_sjk" tabindex="1" aria-hidden="true">
							<option value="0" @if(0 == $task->is_wait_sjk) selected @endif>否</option>
							<option value="1" @if(1 == $task->is_wait_sjk) selected @endif>是</option>
						</select>
					</div>
				<div class="form-group control-order">
					<label for="order_number">投资笔数 :</label>
					<input class="form-control valid" placeholder="输入投资笔数" name="order_number" type="number" value="{{$task->order_number}}" aria-invalid="false">
				</div>
                    @if (in_array($task->status, [0]))
					<div class="form-group control-order">
						<label for="status">任务状态 :</label>
						<select class="form-control select2-hidden-accessible" data-placeholder="选择任务状态" rel="select2" name="status" tabindex="2" aria-hidden="true">
							<option value="{{$task->status}}" selected >@if(0 == $task->status) 已创建 @else 运行中 @endif</option>
							<option value="4">取消任务</option>
						</select>
					</div>
					    @la_input($module, 'status')
                    @endif
					<br>
					<div class="form-group control-all">
						{!! Form::submit( '更新', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/tasks') }}">取消</a></button>
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

$(window).load(function (){
    var work = $(".work-select").val();

    $(".form-group").hide();
    $(".control-all").show();
    if (work == 'exchange') {
        $(".control-exchange").show();
    } else if (work == 'order') {
        $(".control-order").show();
    } else if (work == 'transfer') {
        $(".control-transfer").show();
    } else if (work == 'abcGift') {
        $(".control-abcGift").show();
    } else if (work == 'icbcGift') {
        $(".control-icbcGift").show();
    }
});
</script>
@endpush
