<?php

class Callbacksettings extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'Настройки CallBack';
    }

    public function index() {
    $addednumbers = $this->db->select("* FROM  `schedule` ORDER BY `lasttimedial` DESC");
    $callBackStatus = $this->db->select("value FROM  `settings` WHERE `key` = 'callBackStatus'",false);

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
        $this->showMiscallReportPage();
    }

    public function del($phonenumber){
        $this->deteleNumberFromSchedule($phonenumber);
        $this->showMiscallReportPage();
    }

    private function showMiscallReportPage(){
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

    private function deteleNumberFromSchedule($phonenumber){
        $this->db->delete(" FROM `schedule` where `phonenumber`=  '".$phonenumber."'");
        addPhoneNumberToBlackList($phonenumber);
    }

    public function delFromSettings($phonenumber){
        $this->deteleNumberFromSchedule($phonenumber);
        $this->index();
    }

    public function addFromSettings(){

        $phonenumber = $_POST['phonenumber'];
        if(!empty($phonenumber)){
            $checkForNumberExists = $this->db->select("* FROM  `schedule` WHERE `phonenumber` =  '".$phonenumber."'", false);
            if (!is_array($checkForNumberExists)) {
                $this->db->insert("schedule",array(
                    'phonenumber' => $phonenumber ,
                    'attempt' => 0,
                    'lasttimedial' => 0,
                    'activate' => 0
                ));
            }
            delPhoneNumberFromBlackList($phonenumber);
        }

        $this->index();
    }

    public function enablecallback(){
        $callBackStatus = $this->db->select("`value` FROM  `settings` WHERE `key` = 'callBackStatus'",false);
        if($callBackStatus == 1){
            $setStatusForCallback=0;
        }else{
            $setStatusForCallback=1;
        }

        $this->db->update('settings', 'value,'.$setStatusForCallback, "`key` = 'callBackStatus'");
        $this->index();
    }

    public function check(){
        activateNewMiscall();
    }

    public function makeCallBack($count){
        if($count == "") $count = 1;
        makeCallBack($count);
    }


}