<?php
require_once("QueryManager.php");

Class Users extends QueryManager {
	
    private $user_id;

    //CONSTRUCTOR
	function __construct($user_id=null) {
		$this->user_id = $user_id;
        parent::__construct();
	}

	public function getPermissions($scope=array()) {

        // check if administrator (user_id = getenv("ADMIN_ID"))
        if ($this->user_id == getenv("ADMIN_ID")) { 
            return array("admin" => true);
        }

        // check if administrator (based on permissions)
        if (count($scope) == 1 and $scope[0] == "admin") {
            return $this->getAdminPermissions();
        }

        /**
         * Define json path where extract permissions
         */
        $json_extract_path_str = "rp.settings as rp, mp.settings as mp"; // all resources

        switch (count($scope)) {
            case 1:  
                if ($scope[0] != "all") { // specific resource
                    $json_extract_path_str = "json_extract_path(rp.settings::json, 'resources', '" . $scope[0] . "') as rp, 
                        json_extract_path(mp.settings::json, 'resources', '" . $scope[0] . "') as mp";
                }
                break;
            case 2: // edit or read
                $json_extract_path_str = "json_extract_path(rp.settings::json, 'resources', '" . $scope[0] . "', '" . $scope[1] . "') as rp, 
                    json_extract_path(mp.settings::json, 'resources', '" . $scope[0] . "', '" . $scope[1] . "') as mp";
                break;
            default:
                break;
        }

        // query 
        $query = "select
            public.jsonb_recursive_merge(p.rp::jsonb, p.mp::jsonb)::jsonb as permissions
        from (
            select 
                $json_extract_path_str
            from
                tsd_users.members m
            left join tsd_users.members_permissions mp on m.id = mp.member_id and mp.active = true and mp.remove_time is null
            left join tsd_users.members_mapping_roles mmr on m.id = mmr.member_id and mmr.remove_time is null
            left join tsd_users.roles_permissions rp on rp.role_id = mmr.role_id and rp.active = true and rp.remove_time is null 
            where m.id = " . $this->user_id . "
            order by mmr.priority, mmr.update_time DESC
        ) p";

        $result = $this->getSingleField($query);

        // return result
        if ($result["status"] and isset($result["data"])) {

            // FOR HOMOGENEOUS RESULTS
            $permissions = json_decode($result["data"], true);
            if (count($scope) > 0 and $scope[0] != "all") {
                $append = (count($scope) > 1) ? array($scope[1] => $permissions) : $permissions;
                $permissions = array("resources" => array($scope[0] => $append));
            }
            return $permissions;
        }
        return null;
	}

    public function getAdminPermissions() {

        // query 
        $query = "select
            json_extract_path(public.jsonb_recursive_merge(p.rp::jsonb, p.mp::jsonb)::json, 'admin') as permissions
        from (
            select 
                rp.settings as rp, mp.settings as mp
            from
                tsd_users.members m
            left join tsd_users.members_permissions mp on m.id = mp.member_id and mp.active = true and mp.remove_time is null
            left join tsd_users.members_mapping_roles mmr on m.id = mmr.member_id and mmr.remove_time is null
            left join tsd_users.roles_permissions rp on rp.role_id = mmr.role_id and rp.active = true and rp.remove_time is null 
            where m.id = " . $this->user_id . "
            order by mmr.priority, mmr.update_time DESC
        ) p";

        $result = $this->getSingleField($query);

        // return result
        if ($result["status"] and isset($result["data"])) {
            $admin = (intval($result["data"]) === 1 or $result["data"] === true or $result["data"] === "true");
            if ($admin) return array("admin" => true);
        }
        return null;
	}

    public function getList($input) {
        $query = "SELECT id, email as name, registered FROM tsd_users.members m WHERE deleted IS NULL AND NOT registered IS NULL AND NOT confirmed IS NULL";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false),
				"email" => array("quoted" => true)
			));

            if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => "id"),
					"name" => array("alias" => "name")
				));
			}
		}
		
		//echo $query;
		return $this->getRecordSet($query);
    }	
}