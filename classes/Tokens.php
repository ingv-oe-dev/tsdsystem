<?php
require_once("QueryManager.php");
require_once("JWT.php");
require_once("Users.php");
require_once("SecureLogin.php");

Class Tokens extends QueryManager {
	
    private $server_key_path = "../server_key";
    private $serverKey;
   
    public $input;
    public $userId;
    public $permissions;
    public $nbf;
    public $exp;

    //CONSTRUCTOR
	function __construct($input) {
        
        parent::__construct();
        
        $this->input = $input;
        
        if (!array_key_exists("validity_days", $input)) $this->input["validity_days"] = 1;
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
        $this->serverKey = file_get_contents($this->server_key_path);

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
        $query = "INSERT INTO tsd_users.tokens (token) VALUES ('" . $token . "')";
        $this->executeSQLCommand($query);
    }
	
}