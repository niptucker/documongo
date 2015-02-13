<?php

namespace documongo;

trait permission {

    protected $permissions;

    protected function fetchPermissions($userUuid = null, $action = null) {

        if (!isset($this->permissions[$action])) {
            $rule_search = array(
                "objects" => array('$regex' => $this->uuid . "(/.*)?", '$options' => 'i')
            );
            // where is xpath and lang?
            if (!is_null($userUuid)) {
                $rule_search["actors"] = array('$in' => array($userUuid, '*'));
            }
            if (!is_null($action)) {
                $rule_search["actions"] = array('$in' => array($action, "+$action", "-$action"));
            }
            $rules = $this->security->rules->find($rule_search);
            if ($rules->hasNext()) {
                foreach ($rules as $rule) {
                    $ruleObjects = $rule["objects"];
                    $ruleActions = $rule["actions"];

                    foreach ($ruleObjects as $ruleObject) {
                        $ruleObject = trim(trim($ruleObject), "/");

                        if (substr($ruleObject, 0, strlen($this->uuid)) === $this->uuid) {
                            foreach ($ruleActions as $ruleAction) {
                                $realAction = trim($ruleAction, "+-");

                                if ($ruleAction === $action || $ruleAction === "+$action") {
                                    $this->permissions[$realAction]["allow"][$ruleObject] = true;

                                } elseif ($ruleAction === "-$action") {

                                    $this->permissions[$realAction]["deny"][$ruleObject] = true;
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    function hasPermission($userUuid, $action, $xpath, $lang = null) {
        $isPermitted = null;

        if (empty($xpath) || $xpath == "/") {
            $xpath = "";
        }

        $this->fetchPermissions($userUuid, $action);


        $objectPathComponents = explode("/", trim($xpath, "/"));

        foreach (array("allow" => true, "deny" => false) as $permMode => $permValue) {
            if ($isPermitted === false) break;

            $curPath = $this->uuid;

            $foundRule = isset($this->permissions[$action][$permMode][$curPath])
                   || (!is_null($lang) && isset($this->permissions[$action][$permMode][$curPath . "_" . $lang]));
            if ($foundRule) {
                $isPermitted = $permValue;
            }

            foreach ($objectPathComponents as $pathComp) {
                if ($isPermitted === false) break;

                $curPath .= "/" . $pathComp;

                $foundRule = isset($this->permissions[$action][$permMode][$curPath])
                        || (!is_null($lang) && isset($this->permissions[$action][$permMode][$curPath . "_" . $lang]));

                if ($foundRule) {
                    $isPermitted = $permValue;
                }
            }
        }

        return $isPermitted;
    }

    public function getPermissions() {
        $this->fetchPermissions();

        return $this->permissions;
    }
}
