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

	public function isValidUUID($uuid) {
		return (is_string($uuid) and (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid) == 1));
	}

	public function object_to_array($obj) {
		//only process if it's an object or array being passed to the function
		if(is_object($obj) || is_array($obj)) {
			$ret = (array) $obj;
			foreach($ret as &$item) {
				//recursively process EACH element regardless of type
				$item = $this->object_to_array($item);
			}
			return $ret;
		}
		//otherwise (i.e. for scalar values) return without modification
		else {
			return $obj;
		}
	}
}