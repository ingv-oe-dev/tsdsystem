<?php
require_once("QueryManager.php");

class SecureLogin extends QueryManager{

	function login($email, $password) {
	   
		$query = "SELECT id, password, salt FROM tsd_users.members WHERE email = :email AND deleted IS NULL AND NOT confirmed IS NULL LIMIT 1";
		$rs = $this->executeReadPreparedStatement($query, array(':email' => $email));
		if (isset($rs) and $rs["status"] and count($rs["data"]) > 0) { 
			$password = hash('sha512', $password.$rs["data"][0]["salt"]); // codifica la password usando una chiave univoca.
			if($rs["data"][0]["password"] == $password) { // Verifica che la password memorizzata nel database corrisponda alla password fornita dall'utente.
				// Login eseguito con successo.
				return array(
					"status" => true,
					"message" => "Login successfull",
					"user_id" => $rs["data"][0]["id"]
				);    
			}
		} 
		return array(
			"status" => false,
			"error" => "Login failed"
		); 
	}
	
	function registration($email, $password) {
		
		$rows = array();
		
		$query = "SELECT id FROM tsd_users.members WHERE email = :email AND NOT deleted IS NULL LIMIT 1";
		$rs = $this->executeReadPreparedStatement($query, array(':email' => $email));

		if (isset($rs) and $rs["status"]) {
			if(count($rs["data"]) < 1) {
				// Crea una chiave casuale
				$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
				// Crea una password usando la chiave appena creata.
				$password = hash('sha512', $password.$random_salt);
				// Inserisci a questo punto il codice SQL per eseguire la INSERT nel tuo database
				// Assicurati di usare statement SQL 'prepared'.
				$sql = "INSERT INTO tsd_users.members (email, password, salt) VALUES (:email, :password, :random_salt)";
				$rs = $this->executeWritePreparedStatement($sql, array(
					':email' => $email,
					':password' => $password,
					':random_salt' => $random_salt
				));
				if (isset($rs) and $rs["status"]) {
					$rows["message"] = 'Success: You have been registered!';
				} else {
					$rows["error"] = $rs["error"];
				}
			} else {
				$rows["error"] = "There is a registration with this email";
			}
		} else {
			$rows["error"] = $rs["error"];
		}
		
		return $rows;
	}
}