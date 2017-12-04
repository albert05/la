@extends("la.layouts.app")

@section("contentheader_title", "Tasks")
@section("contentheader_description", "tasks listing")
@section("section", "Tasks")
@section("sub_section", "Listing")
@section("htmlheader_title", "Tasks Listing")

@section("headerElems")
    @la_access("Tasks", "create")
    <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Tasks</button>
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
                        <th>Actions</th>
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
                    <h4 class="modal-title" id="myModalLabel">Add Task</h4>
                </div>
                {!! Form::open(['action' => 'LA\TasksController@store', 'id' => 'task-add-form']) !!}
                <div class="modal-body">
                    <div class="box-body">
                        <div class="form-group control-all">
                            <label for="title">Title :</label>
                            <input class="form-control" placeholder="Enter Title" data-rule-maxlength="256" name="title" type="text" value="">
                        </div>

                        <div class="form-group control-all">
                            <label for="word_id">Work Id :</label>
                            <select class="form-control select2-hidden-accessible work-select" data-placeholder="Enter Work Id" rel="select2" name="work_id" tabindex="0" aria-hidden="true">
                                @foreach ($work_list as $k => $v)
                                    <option value="{{$k}}" >{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group control-all">
                            <label for="user_key">User Key :</label>
                            <select class="form-control select2-hidden-accessible" data-placeholder="Enter User Key" rel="select2" name="user_key" tabindex="1" aria-hidden="true">
                                @foreach ($user_list as $k => $v)
                                    <option value="{{$k}}" >{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        {{--@la_input($module, 'user_key')--}}

                        <div class="form-group control-exchange control-order">
                            <label for="run_time">Run Time* :</label>
                            <div class='input-group datetime'>
                                <input id= 'datetimepicker' class="form-control" placeholder="Enter Run Time" required="1" name="run_time" type="text" value="">
                            </div>
                        </div>

                            <div class="form-group control-exchange control-order">
                                <label for="time_point">Time Point :</label>
                                <input class="form-control valid" placeholder="Enter Time Point" data-rule-maxlength="256" name="time_point" type="text" value="" aria-invalid="false">
                            </div>
                            <div class="form-group control-exchange control-order">
                                <label for="product_id">Product Id :</label>
                                <input class="form-control valid" placeholder="Enter Product Id" name="product_id" type="number" value="" aria-invalid="false">
                            </div>
                            <div class="form-group control-exchange">
                                <label for="code">Code :</label>
                                <input class="form-control" placeholder="Enter Code" data-rule-maxlength="256" name="code" type="text" value="">
                            </div>
                            <div class="form-group control-order">
                                <label for="money">Money :</label>
                                <input class="form-control valid" placeholder="Enter Money" name="money" type="number" value="" aria-invalid="false">
                            </div>
                            <div class="form-group control-order">
                                <label for="voucher_id">Voucher Id :</label>
                                <input class="form-control valid" placeholder="Enter Voucher Id" name="voucher_id" type="number" value="" aria-invalid="false">
                            </div>
                            <div class="form-group control-order">
                                <label for="is_kdb_pay">Is Kdb Pay :</label>
                                <select class="form-control select2-hidden-accessible" data-placeholder="Enter Is Kdb Pay" rel="select2" name="is_kdb_pay" tabindex="-1" aria-hidden="true">
                                    <option value="0" selected>否</option>
                                    <option value="1" >是</option>
                                </select>
                            </div>
                            <div class="form-group control-order">
                                <label for="is_wait_sjk">Is Wait Sjk :</label>
                                <select class="form-control select2-hidden-accessible" data-placeholder="Enter Is Wait Sjk" rel="select2" name="is_wait_sjk" tabindex="-1" aria-hidden="true">
                                    <option value="0" selected>否</option>
                                    <option value="1" >是</option>
                                </select>
                            </div>
                            <div class="form-group control-exchange">
                                <label for="prize_number">Prize Number :</label>
                                <input class="form-control valid" placeholder="Enter Prize Number" name="prize_number" type="number" value="" aria-invalid="false">
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    {!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!}
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
            }
        });
    });
</script>
@endpush