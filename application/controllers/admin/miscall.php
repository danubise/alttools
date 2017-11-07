<?php

class Miscall extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'Пропущенные Звонки';
    }

    public function index() {
    $date = new DateTime();
    echo $date->getTimestamp();
    global $_config_CDR;
    $cdrdb = new db(
        $_config_CDR['mysql']['host'],
        $_config_CDR['mysql']['user'],
        $_config_CDR['mysql']['password'],
        $_config_CDR['mysql']['base']
    );
    $cdrdb->set_charset("utf8");
    $allCdrRecodrs = $cdrdb->select("src , calldate, did  FROM cdr WHERE calldate>'2017-11-07 11:00:52' AND calldate<'2017-11-07 12:00:52' AND  disposition = 'NO ANSWER' limit 360");

    $lastMiscallCDR = array();
    foreach($allCdrRecodrs as $key=>$valueArray){

        if(strlen($valueArray['src'])>4){
            $src = $valueArray['src'];
            $src[0] ="8";
            $lastMiscallCDR[$src]=$valueArray;
        }
    }

    $allCdrRecodrs = $cdrdb->select("src , calldate  FROM cdr WHERE calldate>'2017-11-07 11:00:52' AND calldate<'2017-11-07 12:00:52'  AND  disposition = 'ANSWERED' AND billsec>4 limit 360");

    $lastAnsweredcallCDR = array();
    foreach($allCdrRecodrs as $key=>$valueArray){
         if(strlen($valueArray['src'])>4){
             $src = $valueArray['src'];
             $src[0] ="8";
            $lastAnsweredcallCDR[$src]=$valueArray['calldate'];
         }
    }
    $allCdrRecodrs = $cdrdb->select("dst , calldate FROM cdr WHERE  calldate>'2017-11-07 11:00:52' AND calldate<'2017-11-07 12:00:52' AND disposition = 'ANSWERED' AND billsec>4 limit 360");

    $lastDialAnsweredcallCDR = array();
    foreach($allCdrRecodrs as $key=>$valueArray){
         if(strlen($valueArray['dst'])>4){
              $src = $valueArray['dst'];
              $src[0] ="8";
            $lastDialAnsweredcallCDR[$src]=$valueArray['calldate'];
         }
    }

    foreach($lastMiscallCDR as $src=>$valueArray){
        if($lastAnsweredcallCDR[$src]> $valueArray['calldate']){
            unset($lastMiscallCDR[$src]);
        }
    }

    foreach($lastMiscallCDR as $src=>$valueArray){
        if($lastDialAnsweredcallCDR[$src]> $valueArray['calldate']){
            unset($lastMiscallCDR[$src]);
        }
    }

    foreach($lastMiscallCDR as $src=>$valueArray){
        if($valueArray['did'] == "777705") $lastMiscallCDR[$src]['did']="PK krim";
        if($valueArray['did'] == "777708") $lastMiscallCDR[$src]['did']="PK krnd";
        if($valueArray['did'] == "79280390600") $lastMiscallCDR[$src]['did']="PK krnd";
        if($valueArray['did'] == "79263123712") $lastMiscallCDR[$src]['did']="PK msk";
        if($valueArray['did'] == "777706" || $valueArray['did'] == "777707") $lastMiscallCDR[$src]['did']="PK rostov";
        if($valueArray['did'] == "777729" || $valueArray['did'] == "777730") $lastMiscallCDR[$src]['did']="Ross-Biz";
        if($valueArray['did'] == "777727" || $valueArray['did'] == "777728") $lastMiscallCDR[$src]['did']="Rostov-holod";
        if($valueArray['did'] == "777701" ) $lastMiscallCDR[$src]['did']="PK sochi";
        if($valueArray['did'] == "79282427127" ) $lastMiscallCDR[$src]['did']="PK sochi";
        if($valueArray['did'] == "777702" ) $lastMiscallCDR[$src]['did']="PK stavropol";
        if($valueArray['did'] == "777702" ) $lastMiscallCDR[$src]['did']="PK stavropol";
        if($valueArray['did'] >= "777731" && $valueArray['did'] <= "777735") $lastMiscallCDR[$src]['did']="KOMPLEKT krd";
        if($valueArray['did'] == "777704" ) $lastMiscallCDR[$src]['did']="KOMPLEKT krim";
        if($valueArray['did'] == "79282073771" ) $lastMiscallCDR[$src]['did']="KOMPLEKT krd";
        if($valueArray['did'] == "79263123727" ) $lastMiscallCDR[$src]['did']="KOMPLEKT msk";
        if($valueArray['did'] == "79263123727" ) $lastMiscallCDR[$src]['did']="KOMPLEKT msk";
        if($valueArray['did'] == "777736" ) $lastMiscallCDR[$src]['did']="KOMPLEKT sochi";
        if($valueArray['did'] == "79282427747" ) $lastMiscallCDR[$src]['did']="KOMPLEKT sochi";
        if($valueArray['did'] == "777703" ) $lastMiscallCDR[$src]['did']="KOMPLEKT stav";
        if($valueArray['did'] == "777703" ) $lastMiscallCDR[$src]['did']="KOMPLEKT stav";
        if($valueArray['did'] == "79281113070" ) $lastMiscallCDR[$src]['did']="KOMPLEKT-UG rst";
        if($valueArray['did'][0] == "7" &&
            $valueArray['did'][1] ==  "7" &&
            $valueArray['did'][2] == "0" &&
            $valueArray['did'][3] !== "") $lastMiscallCDR[$src]['did']="KOMPLEKT-UG rst";
        if($valueArray['did'] >= "777721" && $valueArray['did'] <= "777726") $lastMiscallCDR[$src]['did']="KOMPLEKT-UG";

    }
    $this->view(
            array(
                'view' => 'miscall/make',
                'var' => array(
                    'lastMiscallCDR' => $lastMiscallCDR
                )
            )
        );
    }

    public function make($last) {
        $this->view(
            array(
                'view' => 'miscall/make',
                'var' => array()
            )
        );

    }

    public function logout() {
        $this->user_model->logout();
        header('Location: '.baseurl());
    }
}
