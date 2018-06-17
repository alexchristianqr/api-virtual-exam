<?php
/**
 * Created by PhpStorm.
 * User: aquispe
 * Date: 6/13/2018
 * Time: 4:15 PM
 */

namespace App\Http\Services;


use App\User;

class UserService
{
  /**
   * @param $request : Request
   * @param array $columns : Columns for Query in the Databse
   * @param bool $limit : First or Get
   * @return mixed
   */
  private function dataModel($request, $columns = ['users.*'], $limit = false)
  {
    $dataModel = (new User())->select($columns);
    if ($request->has('role_join')) {
      $dataModel = $dataModel->join('role', 'role.id', 'users.role_id');
    }
    if ($request->has('project_join')) {
      $dataModel = $dataModel->join('project', 'project.id', 'users.project_id');
    }
    if ($request->has('role_id')) {
      if ($request->role_id > 1) {
        $dataModel = $dataModel->whereIn('role.id', ['2', '3', '4', '5']);
      }
    }
    if ($request->has('username')) {
      $dataModel = $dataModel->where('users.username', $request->username);
    }
    if ($request->has('status')) {
      $dataModel = $dataModel->where('users.status', $request->status);
    }
    if ($limit) {
      return $dataModel->first();
    } else {
      return $dataModel->get();
    }
  }

  function all($request)
  {
    $request->request->add(['role_join' => true]);
    return $this->dataModel($request, ['users.*', 'role.name AS role_name']);
  }

  function getUsers($request)
  {
    $request->request->add(['status' => 'A']);
    return $this->dataModel($request, ['id', 'name']);
  }

  function searchUser($request)
  {
    $request->request->add([
      'role_join' => true,
      'project_join' => true,
      'status' => 'A',
    ]);
    $User = $this->dataModel($request, [
      'users.id',
      'users.role_id AS role',
      'users.project_id AS project',
      'users.name',
      'users.username',
      'users.email',
      'users.status',
      'role.name AS role_name',
      'role.status AS role_status',
      'project.name AS project_name',
      'project.status AS project_status'
    ], true);
    $User->role = ['id' => $User->role, 'name' => $User->role_name, 'status' => $User->role_status];
    $User->project = ['id' => $User->project, 'name' => $User->project_name, 'status' => $User->project_status];
    return $User;
  }

  function createUser($request)
  {
    $User = new User();
    $User->fill($request->all());
    $User->email = $request->username . '@sapia.com.pe';//siempre es invitado
    $User->role_id = 5;//inicializar como invitado
    $User->project_id = 1;//inicializar con ningun proyecto asignado
    $User->status = 'A';//inicializar como activo
    return $User->save();
  }
}