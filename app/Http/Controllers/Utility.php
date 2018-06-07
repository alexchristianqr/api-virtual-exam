<?php
/**
 * Created by PhpStorm.
 * User: aquispe
 * Date: 6/7/2018
 * Time: 11:04 AM
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;

trait Utility
{
  function setTableAutoInc($table)
  {
    $max = DB::table($table)->count() + 1;
    DB::statement("ALTER TABLE $table AUTO_INCREMENT =  $max");
  }
}