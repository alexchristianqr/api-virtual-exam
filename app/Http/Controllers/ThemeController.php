<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Theme;
use Exception;
use Illuminate\Support\Facades\DB;

class ThemeController extends Controller
{
    function all(Request $request)
    {
        $query = new Theme();
        if ($request->has("user_survey_theme_id")) {
            $query = $query->select([
                "theme.id AS theme_id",
                "theme.name AS theme_name",
                "theme.updated_at AS theme_updated_at",
                "theme.duration AS theme_duration",
                "theme.status AS theme_status",
                "user_survey_theme.id AS user_survey_theme_id",
                "user_survey_theme.score AS user_survey_theme_score",
                "user_survey_theme.status AS user_survey_theme_status",
            ])
                ->join("user_survey_theme", "user_survey_theme.theme_id", "theme.id")
                ->leftJoin("user_survey", "user_survey.id", "user_survey_theme.user_survey_id")
                ->where("theme.status", "A")
                ->where("user_survey.id", $request->user_survey_theme_id)
                ->orderBy("theme.id");
        } else {
            $query = $query->select(["theme.*"]);
        }
        return $query->get()->toArray();
    }

    function create(Request $request)
    {
        $theme = new Theme();
        $this->validate($request,$theme->rules);
        DB::beginTransaction();
        try {
            $theme->fill($request->all())->save();
            $request->request->add(['theme_id' => $theme->id]);
            (new UserSurveyThemeController())->create($request);
            return DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return DB::statement("ALTER TABLE " . $theme->getTable() . " AUTO_INCREMENT = " . (count($theme->all()) + 1) . ";");
        }
    }
}
