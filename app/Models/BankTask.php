<?php
/**
 * Model genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankTask extends Model
{
    use SoftDeletes;
	
	protected $table = 'bank_tasks';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];

	const WORK_ABC = 1;
	const WORK_ICBC = 2;

	public static $work_list = [
        self::WORK_ABC => '农行',
        self::WORK_ICBC => '工行',
    ];
}
