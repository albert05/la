@extends("la.layouts.app")

@section("contentheader_title", "任务")
@section("contentheader_description", "任务列表")
@section("section", "任务")
@section("sub_section", "列表")
@section("htmlheader_title", "任务列表")

@section("headerElems")
    @la_access("Tasks", "create")
    <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">添加任务</button>
    @endla_access
@endsection

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

    <div class="box box-success">
        <!--<div class="box-header"></div>-->
        <div class="box-body">
            <table id="example1" class="table table-bordered">
                <thead>
                <tr class="success">
                    @foreach( $listing_cols as $col )
                        <th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
                    @endforeach
                    @if($show_actions)
                        <th>操作</th>
                    @endif
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    @la_access("Tasks", "create")
    <div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">添加任务</h4>
                </div>
                {!! Form::open(['action' => 'LA\TasksController@store', 'id' => 'task-add-form']) !!}
                <div class="modal-body">
                    <div class="box-body">
                        <div class="form-group control-all">
                            <label for="title">任务标题 :</label>
                            <input class="form-control" placeholder=" 输入任务标题" data-rule-maxlength="256" name="title" type="text" value="">
                        </div>

                        <div class="form-group control-all">
                            <label for="word_id">任务类型 :</label>
                            <select class="form-control select2-hidden-accessible work-select" data-placeholder="选择任务类型" rel="select2" name="work_id" tabindex="0" aria-hidden="true">
                                @foreach ($work_list as $k => $v)
                                    <option value="{{$k}}" >{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group control-all">
                            <label for="user_key">用户标识 :</label>
                            <select class="form-control select2-hidden-accessible" data-placeholder="选择用户标识" rel="select2" name="user_key" tabindex="1" aria-hidden="true">
                                @foreach ($user_list as $k => $v)
                                    <option value="{{$k}}" >{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        {{--@la_input($module, 'user_key')--}}

                        <div class="form-group control-all">
                            <label for="run_time">脚本开始时间* :</label>
                            <div class='input-group datetime'>
                                <input id= 'datetimepicker' class="form-control" placeholder="选择脚本开始时间" required="1" name="run_time" type="text" value="">
                            </div>
                        </div>

                            <div class="form-group control-exchange control-order">
                                <label for="time_point">任务执行时间点 :</label>
                                <input class="form-control valid" placeholder="输入任务请求时间点，如输入100101.95表示10点1分1秒950毫秒执行" data-rule-maxlength="256" name="time_point" type="text" value="" aria-invalid="false">
                            </div>
                            <div class="form-group control-exchange control-order control-transfer">
                                <label for="product_id">产品ID :</label>
                                <input class="form-control valid" placeholder="输入产品ID" name="product_id" type="number" value="" aria-invalid="false">
                            </div>
                            {{--<div class="form-group control-exchange">--}}
                                {{--<label for="code">验证码 :</label>--}}
                                {{--<input class="form-control" placeholder="输入验证码" data-rule-maxlength="256" name="code" type="text" value="">--}}
                            {{--</div>--}}
                            <div class="form-group control-order control-transfer">
                                <label for="money">金额 :</label>
                                <input class="form-control valid" placeholder="输入金额" name="money" type="number" value="0" aria-invalid="false">
                            </div>
                            <div class="form-group control-order">
                                <label for="voucher_id">加息券ID :</label>
                                <input class="form-control valid" placeholder="输入加息券ID" name="voucher_id" type="number" value="0" aria-invalid="false">
                            </div>
                            <div class="form-group control-order">
                                <label for="is_kdb_pay">是否使用口袋宝 :</label>
                                <select class="form-control select2-hidden-accessible" data-placeholder="选择师傅使用口袋宝" rel="select2" name="is_kdb_pay" tabindex="-1" aria-hidden="true">
                                    <option value="0" selected>否</option>
                                    <option value="1" >是</option>
                                </select>
                            </div>
                            <div class="form-group control-order">
                                <label for="is_wait_sjk">是否等待三剑客 :</label>
                                <select class="form-control select2-hidden-accessible" data-placeholder="选择是否已等待三剑客" rel="select2" name="is_wait_sjk" tabindex="-1" aria-hidden="true">
                                    <option value="0" selected>否</option>
                                    <option value="1" >是</option>
                                </select>
                            </div>
                        <div class="form-group control-order">
                            <label for="order_number">投资笔数 :</label>
                            <input class="form-control valid" placeholder="输入投资笔数" name="order_number" type="number" value="1" aria-invalid="false">
                        </div>
                            <div class="form-group control-exchange">
                                <label for="prize_number">券数量 :</label>
                                <input class="form-control valid" placeholder="输入券数量" name="prize_number" type="number" value="1" aria-invalid="false">
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    {!! Form::submit( '提交', ['class'=>'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @endla_access

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>
@endpush

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
    $(function () {
        $("#example1").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url(config('laraadmin.adminRoute') . '/task_dt_ajax') }}",
            language: {
                lengthMenu: "_MENU_",
                search: "_INPUT_",
                searchPlaceholder: "Search"
            },
            @if($show_actions)
            columnDefs: [ { orderable: false, targets: [-1] }],
            @endif
        });
        $("#task-add-form").validate({

        });
        $("#datetimepicker").datetimepicker({
            format: 'YYYY-MM-DD HH:mm::ss',
        });

        $(".work-select").change(function(){
            var work_id = $(this).val();

            $(".form-group").hide();
            $(".control-all").show();
            if (work_id == 'exchange') {
                $(".control-exchange").show();
            } else if (work_id == 'order') {
                $(".control-order").show();
            } else if (work_id == 'transfer') {
                $(".control-transfer").show();
            }
        });

        var work = $(".work-select").val();
        $(".form-group").hide();
        $(".control-all").show();
        if (work == 'exchange') {
            $(".control-exchange").show();
        } else if (work == 'order') {
            $(".control-order").show();
        } else if (work_id == 'transfer') {
            $(".control-transfer").show();
        }
    });
</script>
@endpush