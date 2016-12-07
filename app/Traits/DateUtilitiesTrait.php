<?php
/**
 * Created by PhpStorm.
 * User: JudeParfait
 * Date: 05/07/2016
 * Time: 15:26
 */

namespace App\Traits;

use Carbon\Carbon;

trait DateUtilitiesTrait
{
    private function defaultDateTimeFormated($date)
    {

        if (is_null($date) || empty($date)) {
            return $date;
        }

        return Carbon::parse($date)->format('d-m-Y h:m:s');
    }

    private function getNowDateTime()
    {
        list($zone['gmt'], $zone['af_cent'], $zone['usa']) = [3600 * 0, 3600 * 1, 3600 * -5];
        return gmdate('Y-m-d H:i:s', time() + $zone['af_cent']);
    }

    /**
     * Calculates the difference between $start and $s, returns a formatted sting Xd Yh Zm As, e.g. 15d 23h 54m 31s.
     * Empty sections will be strippeed, returning 12d 4s, not 12d 0h 0h 4s.
     * Argument order (begin date, end date) doesn't matter.
     *
     * @param $start    Start time stamp
     * @param $s        End time stamp
     * @return string
     */
    private function diffTimeStamp($start, $s)
    {
        $t = array(//suffixes
            'd' => 86400,
            'h' => 3600,
            'm' => 60,
        );
        $s = abs($s - $start);

        $str_result = "";
        foreach ($t as $key => &$val) {
            $$key = floor($s / $val);

            $s -= ($$key * $val);
            $str_result .= ($$key == 0) ? '' : $$key . "$key ";
        }
        return $str_result . $s . 's';
    }

    /**
     * Renvoie le time stamp d'une date-heure string
     * @param   string  $str_date   Date heure en string
     * @return int                  Le time stamp correspondant
     */
    private function tsUnixDate($str_date)
    {
        $date_seps = array('/', '-');
        $curr_sep = "";
        $month_name_to_int = array('Jan' => 1, 'Feb' => 2, 'Mar' => 3);

        $day = 0;
        $month = 0;
        $year = 0;
        $hour = 0;
        $minute = 0;
        $second = 0;

        for ($i = 0; $i < count($date_seps); $i++) {
            if (substr_count($str_date, $date_seps[$i]) == 2) {
                $curr_sep = $date_seps[$i];
                $i = count($date_seps);
            }
        }

        if (empty($curr_sep)) {
            return -1;
        }

        if (substr_count($str_date, ':') == 2) {
            // we got time
            list($date, $time) = explode(' ', $str_date);
            list($hour, $minute, $second) = explode(':', $time);
        } else {
            // no time, only date
            $date = $str_date;
        }

        list($day, $month, $year) = explode($curr_sep, $date);

        $unixtime = mktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);

        if ($unixtime == false) {
            return -1;
        }

        return $unixtime;
    }

    private function diffDateTime($start, $s)
    {
        return $this->diffTimeStamp($this->tsUnixDate($start), $this->tsUnixDate($s));
    }
}
