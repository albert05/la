<?php
/**
 * Model genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
	
	protected $table = 'tasks';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];

	public static $statusText = [
        0 => "已创建",
        1 => "运行中",
        2 => "失败",
        3 => "成功",
        4 => "已取消",
    ];
}
