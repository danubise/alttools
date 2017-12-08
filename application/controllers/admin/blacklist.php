<?php

class Blacklist extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'Черный список CallBack';
    }

    public function index() {
    $blacklist = $this->db->select("* FROM  `blacklist`");

    $this->view(
            array(
                'view' => 'callback/blacklist',
                'var' => array(
                    'blacklist' => $blacklist
                )
            )
        );
    }

    public function delFromBlacklist($phonenumber){
        $this->db->delete(" FROM `blacklist` where `phonenumber`=  '".$phonenumber."'");
        $this->index();
    }

    public function addFromSettings(){

        $phonenumber = $_POST['phonenumber'];
        if(!empty($phonenumber)){
            $checkForNumberExists = $this->db->select("* FROM  `blacklist` WHERE `phonenumber` =  '".$phonenumber."'", false);
            if (!is_array($checkForNumberExists)) {
                addPhoneNumberToBlackList($phonenumber);
            }
            $this->db->delete(" FROM `schedule` where `phonenumber`=  '".$phonenumber."'");
        }

        $this->index();
    }
}


?>