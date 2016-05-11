<?php
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 11.05.16
 * Time: 15:52
 */

namespace frontend\helpers;


class DateHelper
{

    /**
     * @param $string
     * @return string
     */
    public static function parseUkrainianDate($string){
        $uk_month = ['січ', 'лют', 'бер', 'квіт', 'трав', 'черв', 'лип', 'серп', 'вер', 'жовт', 'лист', 'груд'];
        $en_month = ['Jan', 'Feb', 'Mar', 'May', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];


        $date = date_parse_from_format("j M. Y р.", str_replace($uk_month, $en_month, $string));

        if(!$date || !empty($date['errors'])){
            return $string;
        }

        if(empty($date['month'])){
            var_dump($string);
            var_dump($date);
            die();
        }

        return $date['year'].'-'.(strlen($date['month']) >= 2 ? $date['month'] : "0".$date['month']).'-'.(strlen($date['day']) >= 2 ? $date['day'] : "0".$date['day']);
    }

    public static function parseEnglishDate($string){
        $date = date_parse_from_format("M, j Y", $string);

        return $date['year'].'-'.(strlen($date['month']) >= 2 ? $date['month'] : "0".$date['month']).'-'.(strlen($date['day']) >= 2 ? $date['day'] : "0".$date['day']);
    }

}