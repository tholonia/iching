<?php

class LoadIni {

    public static function init($group) {
        static $dbs = array();
        static $ret = array();
        
        static $runtime = "dev";
        if (isset($_SERVER['runtime'])) {
            $runtime = $_SERVER['runtime'];
        }
        
        if(!is_string($group)) {
            throw new Exception("Invalid group requested");
        }
        if(empty($dbs["group"])){
            $prefix = "${group}.${runtime}";
//            print_r($group);print("\n");
//            print_r($runtime);print("\n");
//            print_r($prefix);print("\n");
//            print_r("$prefix.root");print("\n");

            $ret['root'] = get_cfg_var("$prefix.root"); 
            $ret['testServer'] = get_cfg_var("$prefix.testServer");
            $ret['user'] = get_cfg_var("$prefix.user");
            $ret['db.name'] = get_cfg_var("$prefix.db.name");
            $ret['db.server'] = get_cfg_var("$prefix.db.server");
            $ret['db.user'] = get_cfg_var("$prefix.db.user");
            $ret['db.pass'] = get_cfg_var("$prefix.db.pass");
        }
        return $ret;
    }
}
?>
