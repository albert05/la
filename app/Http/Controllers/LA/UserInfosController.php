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
use App\Models\UserInfo;
use App\Role;
use Mail;
use Log;

class UserInfosController extends Controller
{
	public $show_action = true;
	public $view_col = 'user_key';
	public $listing_cols = ['user_key', 'user_name', 'password', 'pay_passwd'];
	
	public function __construct() {
		
		// Field Access of Listing Columns
		if(\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
			$this->middleware(function ($request, $next) {
				$this->listing_cols = ModuleFields::listingColumnAccessScan('UserInfos', $this->listing_cols);
				return $next($request);
			});
		} else {
			$this->listing_cols = ModuleFields::listingColumnAccessScan('UserInfos', $this->listing_cols);
		}
	}
	
	/**
	 * Display a listing of the WorkLists.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('UserInfos');

		if(Module::hasAccess($module->id)) {
			return View('la.userinfos.index', [
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
		if(Module::hasAccess("UserInfos", "create")) {
		
			$rules = Module::validateRules("UserInfos", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}
			

			$userinfo = UserInfo::create([
				'name' => $request->name,
				'work_id' => $request->work_id,
			]);


			Log::info("WorkLists created: user_key: ".$userinfo->user_key." name: ".$userinfo->user_name);

			return redirect()->route(config('laraadmin.adminRoute') . '.userinfos.index');
			
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
		if(Module::hasAccess("UserInfos", "view")) {

			$userinfo = UserInfo::find($id);
			if(isset($userinfo->id)) {
				$module = Module::get('UserInfos');
				$module->row = $userinfo;
				
				return view('la.userinfos.show', [
					'module' => $module,
					'view_col' => $this->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('userinfo', $userinfo);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("userinfo"),
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
		if(Module::hasAccess("UserInfos", "edit")) {
			
			$userinfo = UserInfo::find($id);
			if(isset($userinfo->id)) {
				$module = Module::get('UserInfos');
				
				$module->row = $userinfo;
				
				return view('la.userinfos.edit', [
					'module' => $module,
					'view_col' => $this->view_col,
				])->with('userinfo', $userinfo);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("userinfo"),
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
		if(Module::hasAccess("UserInfos", "edit")) {
			
			$rules = Module::validateRules("UserInfos", $request, true);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}
			
			Module::updateRow("UserInfos", $request, $id);
        	
			return redirect()->route(config('laraadmin.adminRoute') . '.userinfos.index');
			
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
		if(Module::hasAccess("UserInfos", "delete")) {
            UserInfo::find($id)->delete();
			
			// Redirecting to index() method
			return redirect()->route(config('laraadmin.adminRoute') . '.userinfos.index');
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
		$values = DB::table('userinfos')->select($this->listing_cols)->whereNull('deleted_at');
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('UserInfos');
		
		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($this->listing_cols); $j++) { 
				$col = $this->listing_cols[$j];
				if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
				if($col == $this->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('laraadmin.adminRoute') . '/userinfos/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
				}
			}
			
			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("UserInfos", "edit")) {
					$output .= '<a href="'.url(config('laraadmin.adminRoute') . '/userinfos/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}
				
				if(Module::hasAccess("UserInfos", "delete")) {
					$output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.userinfos.destroy', $data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
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
