<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LoginController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $loginType = 'userid';//filter_var(request()->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        return $loginType;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request){
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/');
    }

    // public function login(Request $request) {
    //     $this->validate($request, [
    //         'userid' => 'required|string', //userid validation
    //         'password' => 'required|string|min:6',
    //     ]);

    //     //LAKUKAN PENGECEKAN, JIKA INPUTAN DARI USERNAME FORMATNYA ADALAH EMAIL, MAKA KITA AKAN MELAKUKAN PROSES AUTHENTICATION MENGGUNAKAN EMAIL, SELAIN ITU, AKAN MENGGUNAKAN USERNAME
    //     $loginType = 'userid';//filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    //     //TAMPUNG INFORMASI LOGINNYA, DIMANA KOLOM TYPE PERTAMA BERSIFAT DINAMIS BERDASARKAN VALUE DARI PENGECEKAN DIATAS
    //     $login = [
    //         $loginType => $request->userid,
    //         'password' => $request->password
    //     ];

    //     //CEK APAKAH userid ada dan masih valid
    //     $user = User::where('userid',$request->userid)->first();
    //     if(!is_null($user)){
    //         if(!is_null($user->extension)){
    //             if($user->extension->status==0){
    //                 // return redirect()->route('login')->with(['error' => 'Wrong User ID/Password!']);
    //                 session()->put('login_error', 'Wrong User ID/Password or User Inactive!');
    //                 return redirect('login')->with('error', 'Wrong User ID/Password or User Inactive!');
    //             }
    //         }
    //     }
    //     //LAKUKAN LOGIN
    //     if (auth()->attempt($login)) {
    //         //JIKA BERHASIL, MAKA REDIRECT KE HALAMAN HOME
    //         session()->forget('login_error');
    //         return redirect()->route('home');
    //     }
    //     //JIKA SALAH, MAKA KEMBALI KE LOGIN DAN TAMPILKAN NOTIFIKASI
    //     // return redirect()->route('login')->with(['error' => 'Wrong User ID/Password!']);
    //     session()->put('login_error', 'Wrong User ID/Password!');
    //     return redirect('login')->with('error', 'Wrong User ID/Password!');
    // }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $user = User::where('userid',$request->userid)->first();
        if($this->IsUserInActive($user)){
            session()->put('login_error', 'Wrong User ID/Password or User Inactive!');
            return redirect('login')->with('error', 'Wrong User ID/Password or User Inactive!');
        }
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    private function IsUserInActive($user)
    {
        //CEK APAKAH userid ada dan masih valid
        if(!is_null($user)){
            if(!is_null($user->extension)){
                return $user->extension->status==0;
            }
        }
        return false;
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        session()->put('login_error', 'Wrong User ID/Password!');
        return redirect('login')->with('error', 'Wrong User ID/Password!');
    }

    // protected function sendLoginResponse(Request $request){
    //     $request->session()->regenerate();
    //     $this->clearLoginAttempts($request);
    //     return $this->authenticated($request, $this->guard()->user())
    //             ?: redirect($this->redirectPath());
    // }

    // ONLY FOR DEV
    // TODO: remove this function
    public function forDevOnly(Request $request)
    {
        if( in_array(config('app.env'),['local', 'development'])){
            if ($request->isMethod('post')) {
                $user = User::where('userid',$request->input('userid', null))->first();
                if($user != null && $user->hasRole('Super Admin') == false){
                    if($this->IsUserInActive($user)){
                        session()->put('login_error', 'Wrong User ID/Password or User Inactive!');
                        return redirect('login')->with('error', 'Wrong User ID/Password or User Inactive!');
                    }

                    if ($this->guard()->loginUsingId($user->id)) {
                        return $this->sendLoginResponse($request);
                    }
                }
                return $this->sendFailedLoginResponse($request);
            }
            return view('auth.login_dev');
        }
        throw new NotFoundHttpException();
    }

}
