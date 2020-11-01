<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use DB;
use DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function index(){
        $permissions = Permission::all();
        //dd($permissions);
        return view('admin.roles.list', [
            'fields' => explode(',','name,created_at'),
            'permissions' => $permissions,
        ]);

    }
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'permissions' => 'required|array|exists:permissions,name'
        ]);
        if(isset($request->id)){
            //update
            $role = Role::find($request->id);
            $role->name = $request->name;
            $role->save();
            $role->syncPermissions($request->permissions);
        }else{
            $role = Role::firstOrCreate(['name' => $request->name]);
            $role->syncPermissions($request->permissions);
        }
        $success = $this->refreshUserRoles($role);

        return response()->json([
            'success'=>true, 
            'message' => 'Role: <strong>' . $role->name . '</strong> saved'
        ]);
    }
    public function refreshUserRoles($role){
        $users = User::role($role->name);
        foreach($users as $user){
            $roles = $user->getRoleNames()->pluck('name');
            $user->syncRoles($roles);
        }
        return true;
    }
    public function delete($id){
        $role = Role::findOrFail($id);
        $role->syncPermissions();
        $role->delete();
        //reset permission cache.
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
        return response()->json([
            'success'=>true, 
            'message' => 'Role: <strong>' . $role->name . '</strong> deleted'
        ]);
    }
    public function datatable_serverside(Request $request) {
        if (request()->ajax()) {
            $tmp = DB::table('roles')->where('name','<>','Super Admin')->get();
            $data = [];
            foreach($tmp as $key=>$row){
                $data[] = [
                    'id'=>$row->id,
                    'name'=>$row->name,
                    'created_at'=>$row->created_at,
                    'permissions'=>Role::findByName($row->name)->getAllPermissions(),
                ];
            }
            return DataTables::of($data)
            ->make(true);
        }
    }
}
