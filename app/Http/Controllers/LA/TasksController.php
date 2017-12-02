<?php
/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Common\Helper;
use App\Http\Controllers\Controller;
use App\Models\WorkConfig;
use App\Models\WorkList;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;

use Dwij\Laraadmin\Helpers\LAHelper;

use App\User;
use App\Models\Task;
use App\Role;
use Mail;
use Log;

class TasksController extends Controller
{
    public $show_action = true;
    public $view_col = 'title';
    public $listing_cols = ['id', 'title', 'work_id', 'user_key', 'run_time', 'time_point', 'product_id', 'money', 'code', 'voucher_id', 'is_kdb_pay', 'prize_number', 'is_wait_sjk', 'status'];

    public function __construct() {

        // Field Access of Listing Columns
        if(\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('Tasks', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('Tasks', $this->listing_cols);
        }
    }

    /**
     * Display a listing of the WorkLists.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $module = Module::get('Tasks');

        if(Module::hasAccess($module->id)) {
            $work_list = DB::table('worklists')
                ->lists('name', 'work_id');
            $user_list = DB::table('userinfos')
                ->lists('user_key', 'user_key');

            return View('la.tasks.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->listing_cols,
                'module' => $module,
                'work_list' => $work_list,
                'user_list' => $user_list,
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
    }


    /**
     * Show the form for creating a new worklist.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created worklist in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Module::hasAccess("Tasks", "create")) {

            $rules = Module::validateRules("Tasks", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Create Task
            $task = Task::create([
                'title' => $request->title,
                'user_name' => Auth::user()->name,
                'cmd' => '',
                'work_id' => $request->work_id,
                'user_key' => $request->user_key,
                'time_point' => $request->time_point,
                'product_id' => $request->product_id,
                'code' => $request->code,
                'money' => $request->money,
                'voucher_id' => $request->voucher_id,
                'is_kdb_pay' => $request->is_kdb_pay,
                'is_wait_sjk' => $request->is_wait_sjk,
                'prize_number' => $request->prize_number,
                'run_time' => $request->run_time,
                'status' => 0,
            ]);

            Log::info("Task created: title: ".$task->title." work_id: ".$task->work_id);

            return redirect()->route(config('laraadmin.adminRoute') . '.tasks.index');

        } else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
    }

    /**
     * Display the specified worklist.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Module::hasAccess("Tasks", "view")) {

            $task = Task::find($id);
            if(isset($task->id)) {
                $module = Module::get('Tasks');
                $module->row = $task;

                return view('la.tasks.show', [
                    'module' => $module,
                    'view_col' => $this->view_col,
                    'no_header' => true,
                    'no_padding' => "no-padding"
                ])->with('task', $task);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("task"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
    }

    /**
     * Show the form for editing the specified worklist.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Module::hasAccess("Tasks", "edit")) {

            $task = Task::find($id);
            if(isset($task->id)) {
                $module = Module::get('Tasks');

                $module->row = $task;

                $work_list = DB::table('worklists')
                    ->lists('name', 'work_id');
                $user_list = DB::table('userinfos')
                    ->lists('user_key', 'user_key');

                return view('la.tasks.edit', [
                    'module' => $module,
                    'view_col' => $this->view_col,
                    'work_list' => $work_list,
                    'user_list' => $user_list,
                ])->with('task', $task);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("task"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
    }

    /**
     * Update the specified worklist in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Module::hasAccess("Tasks", "edit")) {

            $rules = Module::validateRules("Tasks", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();;
            }

            $params = [
                'title' => $request->title,
                'work_id' => $request->work_id,
                'user_key' => $request->user_key,
                'time_point' => $request->time_point,
                'product_id' => $request->product_id,
                'code' => $request->code,
                'money' => $request->money,
                'voucher_id' => $request->voucher_id,
                'is_kdb_pay' => $request->is_kdb_pay,
                'is_wait_sjk' => $request->is_wait_sjk,
                'prize_number' => $request->prize_number,
                'run_time' => $request->run_time,
            ];

            if (isset($request->status)) {
                $params['status'] = $request->status;
            }

            Task::where('id', $id)->update($params);

//            Module::updateRow("Tasks", $request, $id);

            return redirect()->route(config('laraadmin.adminRoute') . '.tasks.index');

        } else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
    }

    /**
     * Remove the specified worklist from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Module::hasAccess("Tasks", "delete")) {
            Task::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.tasks.index');
        } else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
    }

    /**
     * Datatable Ajax fetch
     *
     * @return
     */
    public function dtajax()
    {
        $values = DB::table('tasks')->select($this->listing_cols)->orderBy('id', 'desc')->whereNull('deleted_at');
        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('Tasks');

        for($i=0; $i < count($data->data); $i++) {
            for ($j=0; $j < count($this->listing_cols); $j++) {
                $col = $this->listing_cols[$j];
                if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="'.url(config('laraadmin.adminRoute') . '/tasks/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
                }
            }

            if($this->show_action) {
                $output = '';
                if(Module::hasAccess("Tasks", "edit")) {
                    $output .= '<a href="'.url(config('laraadmin.adminRoute') . '/tasks/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if(Module::hasAccess("Tasks", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.tasks.destroy', $data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
                    $output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
                    $output .= Form::close();
                }
                $data->data[$i][] = (string)$output;
            }
        }
        $out->setData($data);
        return $out;
    }

}
