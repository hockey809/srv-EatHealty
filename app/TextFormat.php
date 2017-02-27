<?php namespace App;

use App;
use Carbon\Carbon;


class TextFormat
{
	public static function fromDbToDisplayTime($val) {
		return Carbon::createFromFormat('Y-m-d H:i:s', $val)->format('H:i:s');
	}
	
	public static function fromDbToDisplayDate($val) {
		return Carbon::createFromFormat('Y-m-d H:i:s', $val)->format('d.m.Y');
	}
	
	public static function fromDbToDisplayDateTime($val) {
		return Carbon::createFromFormat('Y-m-d H:i:s', $val)->format('d.m.Y H:i:s');
	}
	
	public static function fromDbToDisplayForHumans($val) {
		return Carbon::createFromFormat('Y-m-d H:i:s', $val)->diffForHumans();
	}
	
	public static function fromCarbonToDisplayDate($val) {
		return $val->format('d.m.Y');
	}
        
        public static function fromDbToDisplayTimeNoSec($val) {
		return Carbon::createFromFormat('Y-m-d H:i:s', $val)->format('H:i');
	}
}