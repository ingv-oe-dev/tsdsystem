<?php
/* 
	Class containing utility functions
*/
Class Utils {
	
	publiC $SECURE_DB_STRING_REGEX = '/^[a-z_]+[a-z0-9_]*$/';
	public $OUTPUT_PSQL_ISO8601_FORMAT = 'YYYY-MM-DD"T"HH24:MI:SS.MSOF'; // compliant to ISO 8601
	public $CORRECT_DATETIME_REGEX = '/^(\d{4})(-(0[1-9]|1[0-2])(-([12]\d|0[1-9]|3[01]))([T\s]((([01]\d|2[0-3])((:)[0-5]\d))([\:]\d+)?)?(:[0-5]\d([\.]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)$/'; // compliant to ISO 8601

	public static function getHostAddress() {
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		return $protocol . $_SERVER['HTTP_HOST'];
	}

	public function verifyDate($date) {
		return preg_match($this->CORRECT_DATETIME_REGEX, $date);
	}

	public function verifySecureDBString($str) {
		return preg_match($this->SECURE_DB_STRING_REGEX, $str);
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

	public function validate_json_by_schema($json_string, $schema) {

		require_once("..".DIRECTORY_SEPARATOR."json-schemas".DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php");

		$result = array();

		$json_to_validate = json_decode($json_string);
		$json_schema = json_decode($schema);

		// Validate
		$validator = new JsonSchema\Validator;
		$validator->validate($json_to_validate, $json_schema);

		if ($validator->isValid()) {
			$result["status"] = true;
			$result["message"] = "The supplied JSON validates against the schema.";
			$result["errors"] = [];
		} else {
			$result["status"] = false;
			$result["message"] = "The supplied JSON does not validate. Violations:";
			$result["errors"] = $validator->getErrors();
		}

		return $result;
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