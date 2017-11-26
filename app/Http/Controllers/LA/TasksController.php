<?php
/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
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
	public $listing_cols = ['id', 'title', 'work_id', 'user_name', 'cmd', 'status', 'result'];
	
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
	 * Display a listing of the Employees.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Tasks');
		
		if(Module::hasAccess($module->id)) {
			return View('la.tasks.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => $this->listing_cols,
				'module' => $module
			]);
		} else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
	}

	/**
	 * Show the form for creating a new employee.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created employee in database.
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
			

			// Create User
			$task = User::create([
				'name' => $request->title,
				'user_name' => $request->user_name,
				'cmd' => $request->cmd,
				'work_id' => $request->work_id,
				'status' => 0,
			]);


			Log::info("Task created: id: ".$task->id." work: ".$task->work_id);

			return redirect()->route(config('laraadmin.adminRoute') . '.tasks.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Display the specified employee.
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
				
				// Get User Table Information
				$user = User::where('context_id', '=', $id)->firstOrFail();
				
				return view('la.tasks.show', [
					'user' => $user,
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
	 * Show the form for editing the specified employee.
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
				
				// Get User Table Information
				$user = User::where('context_id', '=', $id)->firstOrFail();
				
				return view('la.tasks.edit', [
					'module' => $module,
					'view_col' => $this->view_col,
					'user' => $user,
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
	 * Update the specified employee in storage.
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
			
			$task_id = Module::updateRow("Tasks", $request, $id);
        	
			// Update User
			$user = User::where('context_id', $task_id)->first();
			$user->name = $request->name;
			$user->save();
			
			// update user role
			$user->detachRoles();
			$role = Role::find($request->role);
			$user->attachRole($role);
			
			return redirect()->route(config('laraadmin.adminRoute') . '.tasks.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified employee from storage.
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
		$values = DB::table('tasks')->select($this->listing_cols)->whereNull('deleted_at');
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
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
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
