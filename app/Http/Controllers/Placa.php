<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;
use App\Models\Placa_model;

class Placa extends BaseController
{

    public function index(Request $request) {

        $year = $request->input('year') ?? Carbon::now()->year;
        $month = $request->input('month') ?? Carbon::now()->month;

        $placa_model = new Placa_model();

        $holidays = $placa_model->getHolidays($year);
        $data =  $placa_model->calculate10thWorkingDay($year, $month);

        $days = $data["days"];
        $payday = $data["payday"];
        $workdaysCount = $data["workdaysCount"];
        $holidaysMonthCount = $data["holidaysMonthCount"];

        return view('placa')
            ->with(compact('payday', 'holidays', 'year', 'month', 'days', 'workdaysCount', 'holidaysMonthCount'));

    }
}

?>
