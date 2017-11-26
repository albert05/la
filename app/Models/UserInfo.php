<?php
/**
 * Model genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInfo extends Model
{
    use SoftDeletes;
	
	protected $table = 'userinfos';
	
	protected $hidden = [
        
    ];


	protected $guarded = [];

	protected $dates = ['deleted_at'];
}
