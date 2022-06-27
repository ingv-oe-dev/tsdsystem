<?php
require_once("QueryManager.php");

class SecureLogin extends QueryManager{

	function adminLogin($email, $password) {
		if ($email == getenv("ADMIN_EMAIL") and $password == getenv("ADMIN_PASSWORD")) {
			// Login amministratore eseguito con successo.
			return array(
				"status" => true,
				"message" => "Login successfull",
				"user_id" => getenv("ADMIN_ID") // user_id = getenv("ADMIN_ID") per utente amministratore
			); 
		}
		return array(
			"status" => false,
			"error" => "Login failed"
		); 
	}

	function login($email, $password) {
	   
		// controlla prima se è un login amministratore
		$adminLogin = $this->adminLogin($email, $password);
		if ($adminLogin["status"]) {
			return $adminLogin;
		}

		// se non è l'amministratore, login classico tramite database
		$query = "SELECT id, password, salt, confirmed FROM tsd_users.members WHERE email = :email AND deleted IS NULL LIMIT 1";
		$rs = $this->executeReadPreparedStatement($query, array(':email' => $email));
		if (isset($rs) and $rs["status"] and count($rs["data"]) > 0) { 
			$password = hash('sha512', $password.$rs["data"][0]["salt"]); // codifica la password usando una chiave univoca.
			if($rs["data"][0]["password"] == $password) { // Verifica che la password memorizzata nel database corrisponda alla password fornita dall'utente.
				if (isset($rs["data"][0]["confirmed"])) {
					// Login eseguito con successo.
					return array(
						"status" => true,
						"message" => "Login successfull",
						"user_id" => $rs["data"][0]["id"]
					);    
				} else {
					return array(
						"status" => false,
						"error" => "Login suspended. Your registration have to be convalidated from administrators."
					); 
				}
			} else {
				return array(
					"status" => false,
					"error" => "Login failed. Uncorrect username e/o password"
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
					$rows["status"] = true;
					$rows["message"] = 'Success: You have been registered!';
					$rows["salt"] = $random_salt;
				} else {
					$rows["status"] = false;
					$rows["error"] = $rs["error"];
				}
			} else {
				$rows["status"] = false;
				$rows["error"] = "There is a registration with this email";
			}
		} else {
			$rows["status"] = false;
			$rows["error"] = $rs["error"];
		}
		
		return $rows;
	}

	function confirm_registration($email, $salt) {
		
		$rows = array();
		
		$query = "SELECT id FROM tsd_users.members WHERE email = :email AND salt = :salt AND deleted IS NULL AND confirmed IS NULL LIMIT 1";
		$rs = $this->executeReadPreparedStatement($query, array(':email' => $email, ':salt' => $salt));
		if (isset($rs) and $rs["status"] and count($rs["data"]) > 0) {
			$sql = "UPDATE tsd_users.members SET confirmed = timezone('utc'::text, now()) WHERE id=:id";
			$rs = $this->executeWritePreparedStatement($sql, array(
				':id' => $rs["data"][0]["id"]
			));
			$rows["status"] = true;
			$rows["message"] = 'Registration confirmed!';
		} else {
			$rows["status"] = false;
			$rows["error"] = "The email " . $email . " does not exist or something gone wrong";
		}
		
		
		return $rows;
	}

	function create_temp_reset_key($email) {
		
		$rand_key = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		$reset_url = getenv("PUBLIC_URL") . "tsdws/login/reset-pwd.php?email=" . $email . "&rand_key=" . $rand_key;

		$sql = "INSERT INTO tsd_users.temp_reset_keys (email, rand_key) VALUES (:email, :rand_key)";
		$rs = $this->executeWritePreparedStatement($sql, array(':email' => $email, ':rand_key' => $rand_key));

		if ($rs["status"]) {
			return array(
				"status" => true,
				"reset_url" => $reset_url
			);
		} else {
			return $rs;
		}
	}

	function check_valid_reset_password_url($email, $rand_key) {
		
		$validTime = 86400; 

		$query = "SELECT tsd_users.temp_reset_keys.*
			FROM tsd_users.temp_reset_keys 
			INNER JOIN tsd_users.members ON tsd_users.members.email = tsd_users.temp_reset_keys.email 
			WHERE tsd_users.temp_reset_keys.email = :email 
			AND tsd_users.temp_reset_keys.rand_key = :rand_key 
			AND extract(EPOCH from (timezone('utc'::text, now()) - tsd_users.temp_reset_keys.create_time)) < $validTime 
			AND tsd_users.members.deleted is null 
			AND not tsd_users.members.confirmed is null 
			LIMIT 1";

		$rs = $this->executeReadPreparedStatement($query, array(':email' => $email, ':rand_key' => $rand_key));

		if (isset($rs) and $rs["status"] and count($rs["data"]) > 0) {
			return true;
		} else {
			return false;
		}
		
	}

	function reset_password($email, $password) {
		
		$rows = array();
		
		$query = "SELECT id FROM tsd_users.members WHERE email = :email AND deleted IS NULL AND NOT confirmed IS NULL LIMIT 1";
		$rs = $this->executeReadPreparedStatement($query, array(':email' => $email));
		
		if (isset($rs) and $rs["status"]) {
			if(count($rs["data"]) > 0) {
				$id = $rs["data"][0]["id"];
				// Crea una chiave casuale
				$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
				// Crea una password usando la chiave appena creata.
				$password = hash('sha512', $password.$random_salt);
				// Inserisci a questo punto il codice SQL per eseguire la INSERT nel tuo database
				// Assicurati di usare statement SQL 'prepared'.
				$sql = "UPDATE tsd_users.members SET password=:password, salt=:random_salt WHERE id=:id";
				$rs = $this->executeWritePreparedStatement($sql, array(
					':password' => $password,
					':random_salt' => $random_salt,
					':id' => $id
				));
				if (isset($rs) and $rs["status"]) {
					$rows["status"] = true;
					$rows["message"] = 'Success: You have been reset your password!';
				} else {
					$rows["status"] = false;
					$rows["error"] = $rs["error"];
				}
			} else {
				$rows["status"] = false;
				$rows["error"] = "There is not a registration with this email";
			}
		} else {
			$rows["status"] = false;
			$rows["error"] = $rs["error"];
		}
		
		return $rows;
	}
}