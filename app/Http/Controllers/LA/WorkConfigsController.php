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
use App\Models\WorkConfig;
use App\Role;
use Mail;
use Log;

class WorkConfigsController extends Controller
{
	public $show_action = true;
	public $view_col = 'id';
	public $listing_cols = ['id', 'work_id', 'key', 'value'];
	
	public function __construct() {
		
		// Field Access of Listing Columns
		if(\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
			$this->middleware(function ($request, $next) {
				$this->listing_cols = ModuleFields::listingColumnAccessScan('WorkConfigs', $this->listing_cols);
				return $next($request);
			});
		} else {
			$this->listing_cols = ModuleFields::listingColumnAccessScan('WorkConfigs', $this->listing_cols);
		}
	}
	
	/**
	 * Display a listing of the WorkLists.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('WorkConfigs');

		if(Module::hasAccess($module->id)) {
			return View('la.workconfigs.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => $this->listing_cols,
				'module' => $module
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
		if(Module::hasAccess("WorkConfigs", "create")) {
		
			$rules = Module::validateRules("WorkConfigs", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}
			

			// Create WorkList
			$workconfig = WorkConfig::create([
				'work_id' => $request->work_id,
                'key' => $request->key,
                'value' => $request->value,
			]);


			Log::info("WorkConfigs created: work_id: ".$workconfig->work_id." key: ".$workconfig->key);

			return redirect()->route(config('laraadmin.adminRoute') . '.workconfigs.index');
			
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
		if(Module::hasAccess("WorkConfigs", "view")) {
			
			$workconfig = WorkConfig::find($id);
			if(isset($workconfig->id)) {
				$module = Module::get('WorkConfigs');
				$module->row = $workconfig;
				
				return view('la.workconfigs.show', [
					'module' => $module,
					'view_col' => $this->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('workconfig', $workconfig);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("workconfig"),
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
		if(Module::hasAccess("WorkConfigs", "edit")) {
			
			$workconfig = WorkConfig::find($id);
			if(isset($workconfig->id)) {
				$module = Module::get('WorkConfigs');
				
				$module->row = $workconfig;
				
				return view('la.worklists.edit', [
					'module' => $module,
					'view_col' => $this->view_col,
				])->with('workconfig', $workconfig);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("workconfig"),
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
		if(Module::hasAccess("WorkConfigs", "edit")) {
			
			$rules = Module::validateRules("WorkConfigs", $request, true);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}
			
			Module::updateRow("WorkLists", $request, $id);
        	
			return redirect()->route(config('laraadmin.adminRoute') . '.workconfigs.index');
			
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
		if(Module::hasAccess("WorkConfigs", "delete")) {
            WorkConfig::find($id)->delete();
			
			// Redirecting to index() method
			return redirect()->route(config('laraadmin.adminRoute') . '.workconfigs.index');
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
		$values = DB::table('workconfigs')->select($this->listing_cols)->whereNull('deleted_at');
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('WorkConfigs');
		
		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($this->listing_cols); $j++) { 
				$col = $this->listing_cols[$j];
				if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
				if($col == $this->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('laraadmin.adminRoute') . '/workconfigs/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
				}
			}
			
			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("WorkConfigs", "edit")) {
					$output .= '<a href="'.url(config('laraadmin.adminRoute') . '/workconfigs/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}
				
				if(Module::hasAccess("WorkConfigs", "delete")) {
					$output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.workconfigs.destroy', $data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
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
