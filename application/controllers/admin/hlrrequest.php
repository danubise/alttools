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

    public function make() {
        $number ="";
        if(isset($_POST['parametr'])){
            $parametr= $_POST['parametr'];
            $property=$this->db->select("* from `hlr_property` ",true);
            $prop = "";

            foreach($property as $index=>$keyValueArray){
                $prop[$keyValueArray['propery']] = $keyValueArray['value'];
            }

            $url = "http://hlr.lanck.alarislabs.com/hlr.cgi?login=".$prop['login']."&password=".$prop['password']."&dnis=".$parametr['number'] ;
            $editedKeysList=$this->db->select("* from `hlr_keys`");
            $editedKeysArray = array();

            foreach($property as $index=>$keyValueArray){
                $editedKeysArray[$keyValueArray['original']] = $keyValueArray['edited'];
            }
            printarray($editedKeysArray);
            //$jsonString = file_get_contents($url);
            $jsonString = "{\"destination\":\"79811613861\",\"id\":\"xxta3hqi62n9urumdmzm\",\"stat\":\"DELIVRD\",\"IMSI\":\"250010027494861\",\"err\":\"0\",\"orn\":\"MTS (Mobile TeleSystems)\",\"pon\":\"MTS (Mobile TeleSystems)\",\"ron\":\"MTS (Mobile TeleSystems)\",\"roc\":\"RU\",\"mccmnc\":\"25001\",\"rcn\":\"Russian Federation\",\"ppm\":\"20\",\"onp\":\"9811\",\"ocn\":\"Russian Federation\",\"occ\":\"RU\",\"ocp\":\"7\",\"is_ported\":\"false\",\"rnp\":\"916\",\"rcp\":\"7\",\"is_roaming\":\"false\",\"pnp\":\"9145\",\"pcn\":\"Russian Federation\",\"pcp\":\"7\",\"pcc\":\"RU\"}";
            $arrayResponse = json_decode($jsonString , true);
            $this->db->delete(" from `hlr_temp`");
            foreach($arrayResponse as $key=>$value){
                $this->db->insert('hlr_temp', array("original"=>$key, "value"=>$value));
            }
            $arrayWithEditedName = array();
            unset($arrayResponse['id']);

            foreach($arrayResponse as $key=>$value){
                if(isset($editedKeysArray[$key])){
                    $arrayWithEditedName[$key] = $editedKeysArray[$key];
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
                        'editedkeys' => $arrayWithEditedName
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

    public function editFieldName(){
//SELECT hlrt.original, hlrk.edited, hlrt.value FROM `hlr_temp` as hlrt LEFT JOIN `hlr_keys`  as hlrk USING(`original`)
        $joinTables = $this->db->select("hlrt.original, hlrk.edited, hlrt.value FROM `hlr_temp` as hlrt LEFT JOIN `hlr_keys`  as hlrk USING(`original`)",true);
        printarray($joinTables);
    }

    public function logout() {
        $this->user_model->logout();
        header('Location: '.baseurl());
    }
}
