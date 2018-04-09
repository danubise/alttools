<?php
/**
 * Created by PhpStorm.
 * User: Slava
 * Date: 14.10.2015
 * Time: 23:45
 */

class Hlrrequest extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'HLR Centre';
    }

    public function index() {
        $this->view(
            array(
                'view' => 'hlr/make',
                'var' => array(
                )
            )
        );
    }

    public function make($last) {
        $number ="";

        if(isset($_POST['parametr']) || $last =="1"){
            $parametr= $_POST['parametr'];
            $property=$this->db->select("* from `hlr_property` ",true);
            $prop = "";

            foreach($property as $index=>$keyValueArray){
                $prop[$keyValueArray['propery']] = $keyValueArray['value'];
            }

            $editedKeysList=$this->db->select("* from `hlr_keys`");
            $editedKeysArray = array();

            foreach($editedKeysList as $index=>$keyValueArray){
                $editedKeysArray[$keyValueArray['original']] = $keyValueArray['edited'];
            }

            $arrayResponse = array();
            if($last=="1"){
                $arrayResponsetmp=$this->db->select("* from `hlr_temp` ", true);

                foreach($arrayResponsetmp as $index=>$keyValueArray){
                    $arrayResponse[$keyValueArray['original']] = $keyValueArray['value'];
                    if($keyValueArray['original']=="destination"){
                        $parametr['number'] = $keyValueArray['value'];
                    }
                }


            }
            else{
                $url = "http://".$parametr['url']."/hlr.cgi?login=".$prop['login']."&password=".$prop['password']."&dnis=".$parametr['number'] ;

                $jsonString = file_get_contents($url);

                //$jsonString = "{\"destination\":\"79811613861\",\"id\":\"xxta3hqi62n9urumdmzm\",\"stat\":\"DELIVRD\",\"IMSI\":\"250010027494861\",\"err\":\"0\",\"orn\":\"MTS (Mobile TeleSystems)\",\"pon\":\"MTS (Mobile TeleSystems)\",\"ron\":\"MTS (Mobile TeleSystems)\",\"roc\":\"RU\",\"mccmnc\":\"25001\",\"rcn\":\"Russian Federation\",\"ppm\":\"20\",\"onp\":\"9811\",\"ocn\":\"Russian Federation\",\"occ\":\"RU\",\"ocp\":\"7\",\"is_ported\":\"false\",\"rnp\":\"916\",\"rcp\":\"7\",\"is_roaming\":\"false\",\"pnp\":\"9145\",\"pcn\":\"Russian Federation\",\"pcp\":\"7\",\"pcc\":\"RU\"}";
                $arrayResponse = json_decode($jsonString , true);
                if(isset($arrayResponse['results'][0])){
                    $arrayResponse= $this->treeToArray($arrayResponse['results'][0],"");
                }else{
                    $arrayResponse= $this->treeToArray($arrayResponse,"");
                }
                //printarray($jsonString2);
                $this->db->delete(" from `hlr_temp`");
                foreach($arrayResponse as $key=>$value){
                    $this->db->insert('hlr_temp', array("original"=>$key, "value"=>$value));
                }
            }

            $url = "http://".$parametr['url']."/hlr.cgi?login=".$prop['login']."&password=".$prop['password']."&dnis=".$parametr['number'] ;


            $arrayWithEditedName = array();
            //Пример исключения полей
            //unset($arrayResponse['id']);
            /*
            Array
            (
                [results] => Array
                    (
                        [0] => Array
                            (
                                [to] => 79878130785
                                [mccMnc] => 25001
                                [imsi] => 250010059119785
                                [originalNetwork] => Array
                                    (
                                        [networkName] => MTS (Mobile TeleSystems)
                                        [networkPrefix] => 987813
                                        [countryName] => Russian Federation
                                        [countryPrefix] => 7
                                    )

                                [ported] =>
                                [roaming] =>
                                [status] => Array
                                    (
                                        [groupId] => 3
                                        [groupName] => DELIVERED
                                        [id] => 5
                                        [name] => DELIVERED_TO_HANDSET
                                        [description] => Message delivered to handset
                                    )

                                [error] => Array
                                    (
                                        [groupId] => 0
                                        [groupName] => OK
                                        [id] => 0
                                        [name] => NO_ERROR
                                        [description] => No Error
                                        [permanent] =>
                                    )

                            )

                    )

            )
            */

            foreach($arrayResponse as $key=>$value){
                if(isset($editedKeysArray[$key]) && $editedKeysArray[$key]!==""){
                    $arrayWithEditedName[$editedKeysArray[$key]] = $value;
                }else{
                    $arrayWithEditedName[$key] = $value;
                }
            }

            $this->view(
                array(
                    'view' => 'hlr/make',
                    'var' => array(
                        'arrayResponse' => $arrayResponse,
                        'parametr' => $parametr,
                        'url' => $url,
                        'arrayWithEditedName' => $arrayWithEditedName
                    )
                )
            );
        }else{
            $this->view(
                array(
                    'view' => 'hlr/make',
                    'var' => array()
                )
            );
        }
    }
    function treeToArray($tree, $mainKey=""){
        $resultArray = array();
        foreach($tree as $key => $value){
            if(is_array($value)){
                $resultArray = array_merge($resultArray, $this->treeToArray($value, $key));
            }else{
                if(empty($mainKey)){
                    $separator="";
                }else{
                    $separator="_";
                }
                $resultArray[$mainKey.$separator.$key] = $value;
            }
        }
        return $resultArray;
    }

    public function save(){
        $this->db->delete(" from `hlr_keys`");
        foreach($_POST['edited'] as $key=>$value){
            $this->db->insert('hlr_keys', array("original"=>$key, "edited"=>$value));
        }
        $this->make(1);
    }

    public function editFieldName(){

        $joinTables = $this->db->select("hlrt.original, hlrk.edited, hlrt.value FROM `hlr_temp` as hlrt LEFT JOIN `hlr_keys`  as hlrk USING(`original`)",true);

        $this->view(
            array(
                'view' => 'hlr/edit',
                'var' => array(
                'joinTables' => $joinTables
                )
            )
        );

    }

    public function logout() {
        $this->user_model->logout();
        header('Location: '.baseurl());
    }
}
