<?php
/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Common\Captcha\Image;
use App\Common\Captcha\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Support\Facades\Input;
use Collective\Html\FormFacade as Form;

use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Helpers\LAHelper;
use Zizaco\Entrust\EntrustFacade as Entrust;

use Auth;
use DB;
use File;
use Validator;
use Datatables;

class CaptchasController extends Controller
{
    public function __construct() {

    }

    /**
     * Display a listing of the Uploads.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $db=new Storage();
        $image_path = public_path() + 'la-assets/img/captcha/captcha.png';
        if(isset($_POST['send'])&&$_POST['send']=="send"){
            $image=new Image($image_path);
            $code=$_POST['code'];
            $code_arr=str_split($code);

            for($i=0;$i<$image::CHAR_NUM;$i++){
                $hash_img_data=implode("",$image->splitImage($i));
                $db->add($code_arr[$i],$hash_img_data);
            }

            $db->save();
        }else{
            $image = new Image("http://deposit.koudailc.com/user/captcha"); //http://www.169ol.com/Stream/Code/getCode
            imagepng($image->_in_img, $image_path);
        }

        return View('la.captchas.index');
    }
}
