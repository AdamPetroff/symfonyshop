<?php
/**
 * Created by Adam The Great.
 * Date: 3. 1. 2017
 * Time: 17:19
 */

namespace AppBundle\Utils;


class Strings
{
    /**
     * @param string $str
     * @return string
     */
    public static function incrementDoubleDashUrl(string $str) : string
    {
        $array = explode('--', $str);
        $end = end($array);
        if ((int)$end > 0) {
            $str = str_replace($end, $end + 1, $str);
        } else {
            $str = $str . '--1';
        }

        return $str;
    }
}