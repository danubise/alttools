<?php

class Callbacksettings extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'Настройки CallBack';
    }

    public function index() {
    $addednumbers = $this->db->select("* FROM  `schedule`");
    $callBackStatus = $this->db->select("* FROM  `settings` WHERE `key` = 'callBackStatus'",false);
    printarray($addednumbers);
    $this->view(
            array(
                'view' => 'callback/settings',
                'var' => array(
                    'addednumbers' => $addednumbers,
                    'callbackstatus' => $callBackStatus
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

    public function delFromSettings($phonenumber){
        $checkForNumberExists = $this->db->delete(" FROM `schedule` where `phonenumber`=  '".$phonenumber."'");
        $this->index();
    }

    public function addFromSettings(){

        $phonenumber = $_POST['phonenumber'];
        $checkForNumberExists = $this->db->select("* FROM  `schedule` WHERE `phonenumber` =  '".$phonenumber."'", false);
        if (!is_array($checkForNumberExists)) {
            $this->db->insert("schedule",array(
                'phonenumber' => $phonenumber ,
                'attempt' => 0,
                'lasttimedial' => 0,
                'activate' => 0
            ));
        }
        $this->index();
    }
}