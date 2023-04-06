<?php
require_once("QueryManager.php");
require_once("JWT.php");
require_once("Users.php");
require_once("SecureLogin.php");

Class Tokens extends QueryManager {
	
    private $serverKey;
   
    protected $tablename = "tsd_users.tokens";

    public $input;
    public $userId;
    public $permissions;
    public $nbf;
    public $exp;

    //CONSTRUCTOR
	function __construct($input=null) {
        
        parent::__construct();
        
        $this->input = $input;
        
        if (
            isset($input) and 
            is_array($input) and 
            !array_key_exists("validity_days", $input)
        ) {
            $this->input["validity_days"] = 1;
        }
	}

    public function generate() {

        /** 
         * Create some payload data with user data we would normally retrieve from a
         * database with users credentials. Then when the client sends back the token,
         * this payload data is available for us to use to retrieve other data 
         * if necessary.
         */
        $now = new DateTime("now", new DateTimeZone("UTC"));

        /**
         * Uncomment the following line and add an appropriate date to enable the 
         * "not before" feature.
         */
        $this->nbf = $now->getTimestamp();
        //var_dump($nbf);
        
        /**
         * Uncomment the following line and add an appropriate date and time to enable the 
         * "expire" feature.
         */
        $this->exp = $now->add(new DateInterval('P'.$this->input["validity_days"].'D'))->getTimestamp(); // expire in days
        //var_dump($exp);

        /**
         * If scope is defined: Load user permissions
         */
        $this->permissions = $this->getPermissions();
        
        // Get our server-side secret key from a secure location.
        $this->serverKey = getenv("SERVER_KEY");

        /**
         * Create a token
         */
        $token = $this->createToken();

        /**
         * Save token into db
         */
        $this->saveTokenIntoDB($token);
        
        return $token;
	}

    public function generateOnTheFly($userId) {
		
		$this->userId = $userId;

        $now = new DateTime("now", new DateTimeZone("UTC"));
        
        $this->nbf = $now->getTimestamp();
        //var_dump($nbf);
        
        $this->exp = $now->add(new DateInterval('PT10S'))->getTimestamp(); // expire in 10 seconds
        //var_dump($exp);

        $this->permissions = $this->getPermissions();
        
        // Get our server-side secret key from a secure location.
        $this->serverKey = getenv("SERVER_KEY");

        //Create a token
        $token = $this->createToken();
        
        return $token;
	}

    public function login_phase($input) {
        
        $sl = new SecureLogin();
        
        $login = $sl->login($input['email'], $input['password']);

        if (array_key_exists("user_id", $login))
            $this->userId = $login["user_id"];

        return $login["status"];
    }

    public function getPermissions() {

        if (isset($this->input['scope'])) {
            
            // load users class
            $user = new Users($this->userId);

            $this->input["scope"] = explode('-', $this->input['scope']); // set scope as array
            
            // retrieve permissions
            return $user->getPermissions($this->input['scope']);
        }
    }

    public function createToken() {
        try {
            if (
                !isset($this->permissions) and 
                isset($this->input['scope']) and 
                !empty($this->input['scope']) and 
                !strtolower($this->input['scope'] == 'all')
            ) {
                return -1; 
            }
            $payloadArray = array(
                "userId" => $this->userId
            );
            if (isset($this->permissions)) {$payloadArray['rights'] = $this->permissions;}
            if (isset($this->nbf)) {$payloadArray['nbf'] = $this->nbf;}
            if (isset($this->exp)) {$payloadArray['exp'] = $this->exp;}

            return JWT::encode($payloadArray, $this->serverKey);

        } catch (Exception $e) {
            return null;
        }
    }

    public function saveTokenIntoDB($token) {
        if (is_string($token)) {
            $query = "INSERT INTO " . $this->tablename . " (token, remote_addr) VALUES ('" . $token . "','" . $_SERVER["REMOTE_ADDR"] . "')";
            $this->executeSQLCommand($query);
        }
    }

    public function flushInvalidTokens() {
       
        // Array containing ID of token records to delete
        $to_flush = array();

		// Get tokens
        $result = $this->getList();
        
        if ($result["status"]) {
			$rows = $result["data"];
            
            // Check for invalid tokens (to delete)
            foreach($rows as $row) {
                
                try { 
                    JWT::decode($row["token"], getenv("SERVER_KEY"), array('HS256')); 
                }
                catch(Exception $e) { 
                    array_push($to_flush, $row["id"]);
                }
            }
		}

        if (count($to_flush) > 0) {
            // Prepare delete sql
            $sql = "DELETE FROM " . $this->tablename . " WHERE id IN (" . implode(",", $to_flush) . ")";
            //echo $sql;
            return $this->executeSQLCommand($sql);
        } else {
            return array(
                "status" => true,
				"rows" => 0
            );
        }
    }
	
    public function getList() {
		
		$query = "SELECT * FROM " . $this->tablename;
		//echo $query;

		return $this->getRecordSet($query);
	}
}