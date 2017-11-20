<?php

class Callbacksettings extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'Настройки CallBack';
    }

    public function index() {
    $lastMiscallCDR = getMiscallReport();
    $addednumbers = $this->db->select("SELECT * FROM  `schedule`");
    $this->view(
            array(
                'view' => 'callback/settings',
                'var' => array(
                    'lastMiscallCDR' => $lastMiscallCDR,
                    'addednumbers' => $addednumbers
                )
            )
        );
    }

    public function add($phonenumber){
        $checkForNumberExists = $this->db->select("* FROM  `schedule` WHERE `phonenumber` =  '".$phonenumber."'", false);
        if (!is_array($checkForNumberExists)) {
            $this->db->insert("schedule",array(
                'phonenumber' => $phonenumber ,
                'attempt' => 0,
                'lasttimedial' => 0,
                'activate' => 0
            ));
        }

        $lastMiscallCDR = checkForCallbackEnable();

        $this->view(
            array(
                'view' => 'miscall/make',
                'var' => array(
                    'lastMiscallCDR' => $lastMiscallCDR
                )
            )
        );
    }

    public function del($phonenumber){
        $checkForNumberExists = $this->db->delete(" FROM `schedule` where `phonenumber`=  '".$phonenumber."'");

        $lastMiscallCDR = checkForCallbackEnable();

        $this->view(
            array(
                'view' => 'miscall/make',
                'var' => array(
                    'lastMiscallCDR' => $lastMiscallCDR
                )
            )
        );
    }
}