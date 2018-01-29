@extends("la.layouts.app")

@section("contentheader_title", "Tool")
@section("contentheader_description", "Interface Tool")
@section("section", "Tool")
@section("sub_section", "Interface")
@section("htmlheader_title", "Interface Tool")

@section("main-content")
	<div class="box">
		<div class="box-body">
			<div class="form-group control-all">
				<label>用户标识 :</label>
				<select class="form-control select2-hidden-accessible user_key" data-placeholder="选择用户标识" rel="select2" tabindex="1" aria-hidden="true">
					@foreach ($user_list as $k => $v)
						<option value="{{$k}}" >{{$v}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group control-all">
				<label>查询项 :</label>
				<select class="form-control select2-hidden-accessible search_idx" data-placeholder="选择查询选项" rel="select2" tabindex="2" aria-hidden="true">
					<option value="0" >项目列表</option>
				</select>
			</div>
			<button type="button" class="btn btn-default" data-dismiss="modal">查询</button>
		</div>
	</div>
	<div class="box">
		<div class="box-body">
			<div class="result">
			</div>
		</div>
	</div>
@endsection

@push('scripts')
<style>
	.form-group {
		width: 200px;
	}
</style>
<script>
$(function () {
    $('button').click(function () {
        user_key = $(".user_key").val();
        search_idx = $(".search_idx").val();
        params = "user_key=" + user_key + "&search_idx=" + search_idx
        $.get("{{ url(config('laraadmin.adminRoute') . '/captchas_search?') }}" + params, function(result){
            $(".result").html(result);
        });
    });

});
</script>
@endpush