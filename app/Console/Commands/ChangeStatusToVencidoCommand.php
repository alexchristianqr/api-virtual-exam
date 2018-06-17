<?php

namespace App\Console\Commands;

use App\Http\Services\UserSurveyThemeService;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Console\Command;
use Exception;

class ChangeStatusToVencidoCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'ChangeStatusToVencidoCommand:changestatustovencido';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Desactivar el Estado del Examen';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @param Request $request
   * @return mixed
   */
  public function handle()
  {
    return $this->changeStatusToVencido();
  }

  /**
   * Funcion que cambia el estado de un examen asignado a un usuario a vencido
   * @return mixed
   */
  function changeStatusToVencido()
  {
    try {
      $dataUserSurveyTheme = (new UserSurveyThemeService())->getUserSurveyTheme();
      foreach ($dataUserSurveyTheme as $k => $v) {
        $date_now = Carbon::now()->format('Y-m-d');
        $time_now = Carbon::now()->format('H:i:s');
        if ($date_now == $v->date_expired && $time_now == $v->time_expired) {
          //Cambiamos el estado del examen adigando a "VENCIDO"
          (new UserSurveyThemeService())->updateStatus($v->id,'V');
        }
      }
      return response()->json('Estados actualizados a VENCIDO.', 200);
    } catch (Exception $e) {
      return response($e->getMessage(), 412);
    }
  }

}
