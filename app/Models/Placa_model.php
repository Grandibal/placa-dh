<?php

namespace App\Models;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Google\Client as Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;

class Placa_model
{

    use AuthorizesRequests, ValidatesRequests;

    public $holidays = [];

    /**
     * Function to get holidays for the specified year from Google Calendar API
     *
     * @param int $year The year for which to retrieve holidays
     * @return array An array of holidays for the specified year
     */
    public function getHolidays($year)
    {

        if (!empty($year)) {

            // Authenticate with Google Calendar API
            $client = new Google_Client();
            $client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
            $client->setDeveloperKey(env('GOOGLE_DEV_KEY'));
            $service = new Google_Service_Calendar($client);
            $calendarId = 'sl.slovenian#holiday@group.v.calendar.google.com';

            $startDate = Carbon::createFromDate($year, 1, 1);
            $endDate = Carbon::createFromDate($year, 12, 31);

            $events = $service->events->listEvents($calendarId, [
                'timeMin' => $startDate->copy()->startOfMonth()->format('c'),
                'timeMax' => $endDate->copy()->endOfMonth()->format('c'),
            ]);

            foreach ($events->getItems() as $event) {
                $this->holidays[] = $event->start->date;
            }

            sort($this->holidays);

            return $this->holidays;

        } else {
            return [];
        }
    }

    /**
     * Function to check if a date is a work-free holiday in Slovenia
     *
     * @param string $date The date to check in 'Y-m-d' format.
     * @return bool Returns true if the date is a work-free holiday, false otherwise.
     */
    public function isSloveniaHoliday($date)
    {
        return in_array($date->format('Y-m-d'), $this->holidays);
    }

    /**
     * Function to calculate the 10th working day from the 1st day of the month.
     *
     * @param int $year
     * @param int $month
     * @return array An array containing the following keys:
     * - days: An array of days in the month with the following values:
     *     - 'W' for workday
     *     - 'F' for weekend
     *     - 'H' for work-free holiday
     *     - 'P' for payday
     * - payday: The 10th working day in the month
     * - workdaysCount: The number of workdays in the month
     * - holidaysMonthCount: The number of work-free holidays in the month
     */
    public function calculate10thWorkingDay($year, $month)
    {

        $startDate = Carbon::createFromDate($year, $month, 1);

        $currDate = $startDate->copy()->subDay()->startOfDay();
        $lastDay = $startDate->copy()->endOfMonth()->format('d');

        $j = 0; $k = 0;
        $pay = null;

        $days = [];

        for ($i = 1; $i <= $lastDay; $i++) {

            $j++;

            $currDate->addDay();

            // Check if the date is a work-free holiday in Slovenia
            if ($this->isSloveniaHoliday($currDate)) {
                $days[$currDate->format('Y-m-d')] = 'H';
                $k++;
                $j--;
                continue;
            }

            // Check if the date is a weekend (Saturday or Sunday)
            if ($currDate->isWeekend()) {
                $days[$currDate->format('Y-m-d')] = 'F';
                $j--;
                continue;
            }

            if ($j === 10) {
                $pay = $currDate->copy();
                $days[$currDate->format('Y-m-d')] = 'P';
            } else {
                $days[$currDate->format('Y-m-d')] = 'W';
            }

        }

        return [
            "days" => $days,
            "payday" => $pay,
            "workdaysCount" => $j,
            "holidaysMonthCount" => $k
        ];
    }
}
