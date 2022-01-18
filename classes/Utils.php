<?php
/* 
	Class containing utility functions
*/
Class Utils {
	
	public $DATE_ISO_FORMAT = 'Y-m-d H:i:s';
	
	public function verifyDate($date) {
		return (DateTime::createFromFormat($this->DATE_ISO_FORMAT, $date) !== false);
	}
	
	public static function get_error($err_msg) {
		return array("status" => false, "error" => $err_msg);
	}
	
	public function transpose($array_one) {
		$array_two = [];
		foreach ($array_one as $key => $item) {
			foreach ($item as $subkey => $subitem) {
				$array_two[$subkey][$key] = $subitem;
			}
		}
		return $array_two;
	}
	
	public function validate_json($json=NULL) {
		if (is_string($json)) {
			@json_decode($json);
			return (json_last_error() === JSON_ERROR_NONE);
		}
		if (is_array($json)) {
			@json_encode($json, JSON_NUMERIC_CHECK);
			return (json_last_error() === JSON_ERROR_NONE);
		}
		return false;
	}
}