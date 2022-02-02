<?php
require_once("QueryManager.php");

Class Users extends QueryManager {
	
    private $user_id;

    //CONSTRUCTOR
	function __construct($user_id) {
		$this->user_id = $user_id;
        parent::__construct();
	}

	public function getPermissions($scope) {

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
            left join tsd_users.members_mapping_roles mmr on m.id = mmr.member_id 
            left join tsd_users.roles_permissions rp on rp.role_id = mmr.role_id and rp.active = true and mp.remove_time is null 
            where m.id = " . $this->user_id . "
        ) p";

        $result = $this->getSingleField($query);

        // return result
        if ($result["status"] and isset($result["data"])) {
            return json_decode($result["data"], true);
        }
        return null;
	}
	
}