<?php

namespace App\Http\Controllers;

use App\User;
use App\UserExtensions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;

class PersonnelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $role = Role::orderBy('name', 'ASC')->get();
        return view('admin.personnel.list', [
            'fields' => explode(',','userid,name,position,status,email'),
            'roles' => $role,
        ]);

    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'userid' => 'required|string|max:100',
            'roles' => 'required|array|exists:roles,name'
        ]);

        $success = false;
        try{
            DB::beginTransaction();
            $user = User::firstOrCreate([
                'userid' => $request->userid
            ], [
                'name' => $request->name,
                'password' => Hash::make('password123')
            ]);
            
            $extension = UserExtensions::firstOrCreate([
                'user_id' => $user->id
            ], [
                'status' => '1'
            ]);

            $user->assignRole($request->roles);
            DB::commit();
            $success = true;
        }catch(Exception $e){
            DB::rollback();
            $message = "data_not_saved";
        }

        return response()->json([
            'success'=> $success, 
            'message' => 'User: <strong>' . $request->name . '</strong> '. ($success ? 'berhasil':'gagal').' Ditambahkan',
            'data' => ['id'=>$success?$user->id:null],
        ]);
    }
    public function edit(Request $request, $id){
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'userid' => 'required|string|max:100',
            'email' => 'required|email',
            'status' => 'required|integer',
            'position' => 'required|string|max:64',
            'roles' => 'required|array|exists:roles,name'
        ]);

        $success = false;
        try{
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $password = !empty($request->password) ? Hash::make($request->password):$user->password;
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password
            ]);
            $ext = UserExtensions::where('user_id',$id)->first();
            if($ext==null){
                UserExtensions::insert([
                    'user_id' => $id,
                    'status' => $request->status,
                    'position' => $request->position,
                ]);
            }else{
                $ext->update([
                    'status' => $request->status,
                    'position' => $request->position,
                ]);
            }
            $user->syncRoles($request->roles);
            DB::commit();
            $success = true;
        }catch(Exception $e){
            DB::rollback();
            $message = "data_not_saved";
        }

        return response()->json([
            'success'=> $success, 
            'message' => 'User: <strong>' . $request->name . '</strong> '. ($success ? 'berhasil':'gagal').' Ditambahkan',
            'data' => ['id'=>$success?$user->id:null],
        ]);
    }

    public function delete($id){
        $user = User::findOrFail($id);
        UserExtensions::where('user_id',$id)->delete();
        $user->delete();
        return json_encode([
            'success'=>true, 
            'message' => 'User: <strong>' . $user->name . '</strong> Dihapus'
        ]);
    }
    
    public function datatable_serverside(Request $request) {
        if (request()->ajax()) {
            $data = User::with('roles')
            ->select(
                'users.id',
                'users.userid',
                'users.name',
                'user_extensions.position',
                'user_extensions.status',
                'users.email'
            )
            ->leftJoin('user_extensions', function ($join) {
                $join->on('users.id', '=', 'user_extensions.user_id')
                // ->whereNull('tender_items.deleted_at');
                ;
            })
            ->get();

            return DataTables::of($data)
            ->make(true);
        }
    }

    public function changePassword(Request $request){
        $id = Auth::user()->id;
        $success = false;
        $user = User::find($id);

        if($user==null){
            return response()->json([
                'success'=> $success, 
                'message' => 'User not found',
            ]);
        }

        if(!Hash::check($request->current_password, $user->password)){
            return response()->json([
                'success'=> $success, 
                'message' => 'Current password is invalid',
            ]);
        }
        if($request->new_password !== $request->repeat_new_password){
            return response()->json([
                'success'=> $success, 
                'message' => 'New password and retype password is not match',
            ]);
        }
        
        $password = !empty($request->new_password) ? Hash::make($request->new_password):$user->password;
        $user->update([
            'password' => $password
        ]);

        $success = true;

        return response()->json([
            'success'=> $success, 
            'message' => 'Password changed successfully',
        ]);
    }

    public function adminChangePassword(Request $request){
        if(Auth::user()->can('user_management')){
            $success = false;

            if($request->new_password !== $request->repeat_new_password){

                return response()->json([
                    'success'=> $success, 
                    'message' => 'New password and retype password is not match',
                ]);

            }else{

                $password = !empty($request->new_password) ? Hash::make($request->new_password):$user->password;
                $user = User::find($request->userid);
                if(!is_null($user)){
                    $user->update([
                        'password' => $password
                    ]);
                    $success = true;
        
                    return response()->json([
                        'success'=> $success, 
                        'message' => 'Password changed successfully',
                    ]);
                }else{
                    return response()->json([
                        'success'=> false, 
                        'message' => 'User Not Found',
                    ]);
                }

            }
            
        }else{

            return response()->json([
                'success'=> false, 
                'message' => 'Unauthorized',
            ]);
            
        }
    }

    public function userManagement(){
        $data = User::with('roles')
        ->select(
            'users.id',
            'users.userid',
            'users.name',
            'user_extensions.position',
            'user_extensions.status',
            'users.email'
        )
        ->leftJoin('user_extensions', function ($join) {
            $join->on('users.id', '=', 'user_extensions.user_id')
            // ->whereNull('tender_items.deleted_at');
            ;
        })
        ->where('users.id',auth()->user()->id)
        ->first();
        return view('admin.personnel.usermanagement', [
            'user'=>$data,
        ]);
    }

    public function changeUserAccount(Request $request){
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'userid' => 'required|string|max:100',
            'email' => 'required|email',
            'position' => 'required|string|max:64',
        ]);
        
        $success = false;
        try{
            DB::beginTransaction();
            $user = auth()->user();
            $data = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if($user->id != $request->id){
                return response()->json([
                    'success'=> $success, 
                    'message' => 'Unauthorized',
                ],401);
            }

            if(isset($request->current_password)){
                //password checking
                if(!Hash::check($request->current_password, $user->password)){
                    return response()->json([
                        'success'=> $success, 
                        'message' => 'Current password is invalid',
                    ]);
                }
                if($request->new_password !== $request->repeat_new_password){
                    return response()->json([
                        'success'=> $success, 
                        'message' => 'New password and retype password is not match',
                    ]);
                }

                $password = !empty($request->new_password) ? Hash::make($request->new_password):$user->password;
                $data['password'] = $password;
            }

            $user->update($data);

            $ext = UserExtensions::where('user_id',$request->id)->first();
            if($ext==null){
                UserExtensions::insert([
                    'user_id' => $request->id,
                    'status' => 1,
                    'position' => $request->position,
                ]);
            }else{
                $ext->update([
                    'position' => $request->position,
                ]);
            }

            DB::commit();
            $success = true;

        }catch(Exception $e){
            DB::rollback();
            $message = "data_not_saved";
        }

        return response()->json([
            'success'=> $success, 
            'message' => 'User: <strong>' . $request->name . '</strong> '. ($success ? 'saved':'not saved'),
            'data' => ['id'=>$success?$user->id:null],
        ]);

    }
}
