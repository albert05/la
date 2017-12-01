<?php
/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Task;
use Illuminate\Http\Request;

/**
 * Class DashboardController
 * @package App\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $daily_count = Task::where('work_id', 'daily')->count();
        $exchange_count = Task::where('work_id', 'exchange')->count();
        $order_count = Task::where('work_id', 'order')->count();
        return view('la.dashboard', [
            'daily' => $daily_count,
            'exchange' => $exchange_count,
            'order' => $order_count,
        ]);
    }
}