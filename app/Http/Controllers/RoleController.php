<?php

namespace App\Http\Controllers;

use App\Role;

class RoleController extends Controller
{
    function all()
    {
        $Roles = Role::where('role.status','A')->get(['id','name','status']);
        foreach ($Roles as $k => $role) {
            $Roles[$k]['name'] = strtoupper(str_replace(' ', '_', $role->name));
        }
        return response()->json($Roles, 200);
    }
}
