<?php
/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use App\Models\BankTask;
use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;
use Mail;
use Log;

class BankTasksController extends Controller
{
    public $show_action = true;
    public $view_col = 'title';
    public $listing_cols = [
        'id', 'title', 'work_id',
        'user_key', 'run_time',
        'time_point', 'product_id',
        'is_card', 'status', 'result'];

    public function __construct() {

        // Field Access of Listing Columns
        if(\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
            $this->middleware(function ($request, $next) {
                $this->listing_cols = ModuleFields::listingColumnAccessScan('BankTasks', $this->listing_cols);
                return $next($request);
            });
        } else {
            $this->listing_cols = ModuleFields::listingColumnAccessScan('BankTasks', $this->listing_cols);
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
            return View('la.banktasks.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => $this->listing_cols,
                'module' => $module,
                'work_list' => BankTask::$work_list,
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
        if(Module::hasAccess("BankTasks", "create")) {

            $rules = Module::validateRules("BankTasks", $request);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $params = [
                'title' => $request->title,
                'work_id' => $request->work_id,
                'user_key' => $request->user_key,
                'run_time' => $request->run_time,
                'status' => 0,
                'time_point' => $request->time_point,
                "product_id" => $request->product_id,
                "is_card" => $request->is_card
            ];

            // Create Task
            $task = BankTask::create($params);

            Log::info("Task created: title: ".$task->title." work_id: ".$task->work_id);

            return redirect()->route(config('laraadmin.adminRoute') . '.banktasks.index');

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
        if(Module::hasAccess("BankTasks", "edit")) {

            $task = BankTask::find($id);
            if(isset($task->id)) {
                $module = Module::get('BankTasks');
                $module->row = $task;

                return view('la.tasks.edit', [
                    'module' => $module,
                    'view_col' => $this->view_col,
                    'work_list' => BankTask::$work_list,
                ])->with('task', $task);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("banktask"),
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
        if(Module::hasAccess("BankTasks", "edit")) {

            $rules = Module::validateRules("BankTasks", $request, true);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();;
            }

            $params = [
                'title' => $request->title,
                'user_key' => $request->user_key,
                'run_time' => $request->run_time,
                'time_point' => $request->time_point,
                "product_id" => $request->product_id,
                "is_card" => $request->is_card
            ];

            BankTask::where('id', $id)->update($params);

            return redirect()->route(config('laraadmin.adminRoute') . '.banktasks.index');

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
        if(Module::hasAccess("BankTasks", "delete")) {
            BankTask::find($id)->delete();

            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.banktasks.index');
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
        $values = DB::table('banktasks')->select($this->listing_cols)->orderBy('id', 'desc')->whereNull('deleted_at');
        $out = Datatables::of($values)->make();
        $data = $out->getData();

        $fields_popup = ModuleFields::getModuleFields('BankTasks');

        $work_list = BankTask::$work_list;

        $status_arr = [
            0 => '已创建',
            1 => '运行中',
            2 => '失败',
            3 => '成功',
            4 => '已取消',
        ];

        for($i=0; $i < count($data->data); $i++) {
            for ($j=0; $j < count($this->listing_cols); $j++) {
                $col = $this->listing_cols[$j];
                if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }

                if ($col == 'work_id') {
                    $data->data[$i][$j] = $work_list[$data->data[$i][$j]];
                } elseif ($col == 'status') {
                    $data->data[$i][$j] = $status_arr[$data->data[$i][$j]];
                }
                if($col == $this->view_col) {
                    $data->data[$i][$j] = '<a href="'.url(config('laraadmin.adminRoute') . '/banktasks/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
                }
            }

            if($this->show_action) {
                $output = '';
                if(Module::hasAccess("BankTasks", "edit")) {
                    $output .= '<a href="'.url(config('laraadmin.adminRoute') . '/banktasks/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }

                if(Module::hasAccess("BankTasks", "delete")) {
                    $output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.banktasks.destroy', $data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
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
