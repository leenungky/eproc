<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use App\Http\Controllers\PagesController;
use App\Traits\AccessLog;

class SchedulerTesterController extends Controller
{
    use AccessLog;

    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Change session locale
     * @param  Request $request
     * @return Response
     */
    public function index() {
        return View::make('schedule.index');
    }

    public function docExpiry(Request $request){
        if (request()->ajax()) {
            \Artisan::call('expiry:status',[]);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => "Applicant has already exist by Tax Identification Number or ID Card!",
            ], 200);
        }
    }

    public function email(Request $request){
        $this->maillog("================email-begin");
        $obj = new PagesController;
        $obj->testEmail();
        $this->maillog("================email-end");
        die('testing');
    }

    public function sanctionExpiry(Request $request){
        if (request()->ajax()) {
            \Artisan::call('sanction:expiry',[]);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => "done",
            ], 200);
        }
    }
    public function sanctionStart(Request $request){
        if (request()->ajax()) {
            \Artisan::call('sanction:start',[]);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => "done",
            ], 200);
        }
    }

    public function test(Request $request){
        return view('schedule.test');
    }
}
