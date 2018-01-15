<?php
/**
 * Created by Unix develop team.
 * User: vlad
 * Date: 19.02.15
 * Time: 22:14
 */
require 'PHPMailer_5.2.0/class.phpmailer.php';
require 'PHPMailer_5.2.0/class.smtp.php';
require 'ami.php';
function printarray($out) {
    echo"<pre>";
    print_r($out);
    echo"</pre>";
}

function baseurl($url = '') {
    global $core_dir;
    return 'http://'.$_SERVER['HTTP_HOST'].'/'.(($core_dir)? $core_dir : '').$url;
}

function check_controller($controller) {
    if(file_exists(controllers.$controller.EXT)) {
        require_once controllers.$controller.EXT;
        return true;
    }
    return false;
}

function connect_mysql() {
    global $_config;
    if(empty($_config['mysql']['user']) or empty($_config['mysql']['password'])) {
        return false;
    }
    $connect = new db($_config['mysql']['host'],$_config['mysql']['user'],$_config['mysql']['password'],$_config['mysql']['base']);
    $connect->set_charset("utf8");
    return $connect;
}

function &get_instance() {
    return Core::get_instance();
}

function get_month() {
	return array(
		'Январь',
		'Февраль',
		'Март',
		'Апрель',
		'Май',
		'Июнь',
		'Июль',
		'Август',
		'Сентябрь',
		'Октябрь',
		'Ноябрь',
		'Декабрь'
	);
}

function get_alias($route) {
    if(!empty($route)) {
        global $Core;
        if(isset($Core->config->route->{$route})) {
            return $Core->config->route->{$route};
        }
    }
    return false;
}
function cp1251_to_utf8($s)
{
    if ((mb_detect_encoding($s,'UTF-8,CP1251')) == "WINDOWS-1251")
    {
        $c209 = chr(209); $c208 = chr(208); $c129 = chr(129);
        for($i=0; $i<strlen($s); $i++)
        {
            $c=ord($s[$i]);
            if ($c>=192 and $c<=239) $t.=$c208.chr($c-48);
            elseif ($c>239) $t.=$c209.chr($c-112);
            elseif ($c==184) $t.=$c209.$c209;
            elseif ($c==168)    $t.=$c208.$c129;
            else $t.=$s[$i];
        }
        return $t;
    }
    else
    {
        return $s;
    }
}

function utf8_to_cp1251($s)
{
    if ((mb_detect_encoding($s,'UTF-8,CP1251')) == "UTF-8")
    {
        for ($c=0;$c<strlen($s);$c++)
        {
            $i=ord($s[$c]);
            if ($i<=127) $out.=$s[$c];
            if ($byte2)
            {
                $new_c2=($c1&3)*64+($i&63);
                $new_c1=($c1>>2)&5;
                $new_i=$new_c1*256+$new_c2;
                if ($new_i==1025)
                {
                    $out_i=168;
                } else {
                    if ($new_i==1105)
                    {
                        $out_i=184;
                    } else {
                        $out_i=$new_i-848;
                    }
                }
                $out.=chr($out_i);
                $byte2=false;
            }
            if (($i>>5)==6)
            {
                $c1=$i;
                $byte2=true;
            }
        }
        return $out;
    }
    else
    {
        return $s;
    }
}
function settingsArrayConvert($arrayFromMysql){
    $settings = array();
    foreach($arrayFromMysql as $key=>$valueArray){
        $settings[$valueArray['key']]= $valueArray['value'];
    }
    return $settings;
}

function getSettings(){
    $db = connect_mysql();
    $settings = settingsArrayConvert($db->select( "* FROM  `settings`", true ));
    return $settings;
}

function sendMiscallReport(){
    $settings = getSettings();

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    $date = new DateTime();
    $today =  gmdate("Y-m-d H:i:s", $date->getTimestamp());
    //Server settings
    $mail->SMTPDebug = 2;
    $mail->IsSMTP();                                      // set mailer to use SMTP
    $mail->Host = "smtp.yandex.com";  // specify main and backup server
    $mail->SMTPAuth = true;     // turn on SMTP authentication
    $mail->Username = $settings['sendFromLogin'];  // SMTP username
    $mail->Password = $settings['sendFromPassword']; // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
    $mail->CharSet = 'UTF-8';
    $mail->From = $settings['sendFromEmail'];
    $mail->FromName = $settings['sendFromLogin'];
    $mail->AddAddress($settings['sendToEmail'], $settings['sendToName']);

    $mail->WordWrap = 50;                                 // set word wrap to 50 characters
    $mail->IsHTML(true);                                  // set email format to HTML

    $mail->Subject = "Пропущенные звонки ".$today;

    $mail->Body    = formatHtmlPageEmail();
    $mail->AltBody = "";

    if(!$mail->Send())
    {
       echo "Message could not be sent.
    ";
       echo "Mailer Error: " . $mail->ErrorInfo;
       exit;
    }
}
function formatHtmlPageEmail(){
    $lastMiscallCDR = checkForCallbackEnable();
    $htmlCode = "<table class=\"table table-striped\" id=\"tableNum\"><thead><tr><th><h4>Пропущенные номера</h4></th></tr><tr>".
                "<th>Номер</th><th>Время</th><th>DID</th><th>Канал</th></tr></thead><tbody>";
        foreach($lastMiscallCDR as $key=>$value){
            $htmlCode.="<tr><td>"
                .$value['src']."&nbsp;</td><td>"
                .$value['calldate']."&nbsp;</td><td>"
                .$value['did']."&nbsp;</td><td>"
                .$value['didname']."&nbsp;</td></tr>";
            }
            $htmlCode.="</tbody></table>";
    return $htmlCode;
}

function formatHtmlPageWeb(){
    $lastMiscallCDR = checkForCallbackEnable();
    $htmlCode = "<table class=\"table table-striped\" id=\"tableNum\"><thead><tr><th><h4>Пропущенные номера</h4></th></tr><tr>".
                "<th>Номер</th><th>Время</th><th>DID</th><th>Канал</th><th>CallBack</th></tr></thead><tbody>";
        foreach($lastMiscallCDR as $key=>$value){
            if($value['callBackEnable'] == 0){
                $callBackStatusLink = "<a href=".baseurl('callbacksettings/add/').$value['src'].">Включить</a>";
            }else{
                $callBackStatusLink = "<a href=".baseurl('callbacksettings/del/').$value['src'].">Отключить</a>";
            }
            $htmlCode.="<tr><td>"
                .$value['src']."&nbsp;</td><td>"
                .$value['calldate']."&nbsp;</td><td>"
                .$value['did']."&nbsp;</td><td>"
                .$value['didname']."&nbsp;</td><td>"
                .$callBackStatusLink."&nbsp;</td></tr>";
            }
            $htmlCode.="</tbody></table>";
    return $htmlCode;
}

function checkForCallbackEnable(){
    $sortedMiscallReport = getMiscallReport(false);
    $db = connect_mysql();

    $callBackEnableFrom = $db->select("phonenumber FROM `blacklist`");

    $callBackNumbersAsKey = array();
    if(is_array($callBackEnableFrom)){
        foreach($callBackEnableFrom as $key => $phonenumber){
            $callBackNumbersAsKey[$phonenumber] = 0;
        }
    }
    foreach($sortedMiscallReport as $id=> $arrayData){
        if(isset($callBackNumbersAsKey[$arrayData['src']])){
            $sortedMiscallReport[$id]['callBackEnable'] = 0;
        }else{
            $sortedMiscallReport[$id]['callBackEnable'] = 1;
        }
    }
    return $sortedMiscallReport;
}

function activateNewMiscall(){
    $currentReport =checkForCallbackEnable();
    deactivateOldMiscall($currentReport);
    $db = connect_mysql();
    $callBackEnableFrom = $db->select("phonenumber FROM `schedule`");
    if(is_array($callBackEnableFrom)){
        $callBackNumbersAsKey = array();
        foreach($callBackEnableFrom as $key => $phonenumber){
            $callBackNumbersAsKey[$phonenumber] = 0;
        }

    }
    unset($callBackEnableFrom);
    $date = new DateTime();
    $scheduleCurrentDial = array();
    foreach($currentReport as $key=>$arrayData){
        if($arrayData['callBackEnable'] == 0){
            unset($currentReport[$key]);
        }else{
            if(isset($callBackNumbersAsKey[$arrayData['src']])){

            }else{
            printarray($arrayData);
                echo "Activation for insert ".$arrayData['src']." at ".$arrayData['lasttimedial']. "<br>";
                $db->insert("schedule",array(
                    'phonenumber' => $arrayData['src'] ,
                    'attempt' => 0,
                    'lasttimedial' => $date->getTimestamp()+ 3600*3,
                    'activate' => 1
                ));
                unset($currentReport[$key]);
            }
        }
    }


}

function deactivateOldMiscall($currentReport){
    $db = connect_mysql();
    $callBackEnableFrom = $db->select("phonenumber FROM `schedule`");
    if(is_array($callBackEnableFrom)){
        $callBackNumbersAsKey = array();
        foreach($callBackEnableFrom as $key => $phonenumber){
            $callBackNumbersAsKey[$phonenumber] = 0;
        }
        unset($callBackEnableFrom);

        foreach($currentReport as $key=> $arrayData){
            if(isset($callBackNumbersAsKey[$arrayData['src']])){

                unset($callBackNumbersAsKey[$arrayData['src']]);
            }
        }
        if(sizeof($callBackNumbersAsKey) > 0 ){
            foreach($callBackNumbersAsKey as $activatedPhoneNumber =>$value){
                echo "Deactivation for ".$activatedPhoneNumber."<br>";

                $db->delete(" FROM `schedule` where `phonenumber`=  '".$activatedPhoneNumber."'");
            }
        }else{
                 echo "Have no any phone numbers for deactivation<br>";
        }
    }
}

function getMiscallReport($debug){
    $date = new DateTime();
    $currentTime = "";
    if($debug === true){
        echo "DEBUG <br>";
        $currentTime = " `calldate`< '2018-01-15 13:19:44' AND ";
    }

    $days4 =  gmdate("Y-m-d H:i:s", $date->getTimestamp() - 3600*24*4);
    global $_config_CDR;
    $cdrdb = new db(
        $_config_CDR['mysql']['host'],
        $_config_CDR['mysql']['user'],
        $_config_CDR['mysql']['password'],
        $_config_CDR['mysql']['base']
    );
    $cdrdb->set_charset("utf8");
    $allCdrRecodrs = $cdrdb->select("src , uniqueid, calldate, did  FROM cdr
        WHERE ".$currentTime." `calldate`>'".$days4."' AND `disposition` = 'NO ANSWER'
        ORDER BY `calldate`, `uniqueid` DESC ");


    $lastMiscallCDR = array();
    foreach($allCdrRecodrs as $key=>$valueArray){
        if(strlen($valueArray['src'])>4 && is_numeric($valueArray['src'])){

            $src = normalizePhoneNumber($valueArray['src']);

            if(isset($lastMiscallCDR[$src])){
                if(strlen($valueArray['did']) > 0 ){
                    $lastMiscallCDR[$src]['did']=$valueArray['did'];
                }
                $lastMiscallCDR[$src]['calldate']=$valueArray['calldate'];
                $lastMiscallCDR[$src]['uniqueid']=$valueArray['uniqueid'];
            }else{
                $lastMiscallCDR[$src]=$valueArray;
            }
        }
    }
        if($debug==true) {
            echo "lastMiscallcallCDR<br>";
            printarray($lastMiscallCDR);
        }
    $allCdrRecodrs = $cdrdb->select("src , calldate, uniqueid ,billsec  FROM cdr WHERE  ".$currentTime."  calldate>'"
        .$days4."'  AND  disposition = 'ANSWERED' AND billsec>4 ORDER BY `calldate`,`uniqueid` DESC ");

    $lastAnsweredcallCDR = array();
    foreach($allCdrRecodrs as $key=>$valueArray){
         if(strlen($valueArray['src'])>4){

            $src = normalizePhoneNumber($valueArray['src']);
            $lastAnsweredcallCDR[$src]=$valueArray;
         }
    }
        if($debug==true) {
            echo "lastAnsweredcallCDR<br>";
            printarray($lastAnsweredcallCDR);
        }
    $allCdrRecodrs = $cdrdb->select("dst , calldate, uniqueid, billsec FROM cdr WHERE  ".$currentTime."  calldate>'"
        .$days4."' AND disposition = 'ANSWERED' AND billsec>4 ORDER BY `calldate`,`uniqueid` DESC ");

    $lastDialAnsweredcallCDR = array();
    foreach($allCdrRecodrs as $key=>$valueArray){
         if(strlen($valueArray['dst'])>4){
            $src = normalizePhoneNumber($valueArray['dst']);
            $lastDialAnsweredcallCDR[$src]=$valueArray;
         }
    }
        if($debug==true) {
            echo "lastDialAnsweredcallCDR<br>";
            printarray($lastDialAnsweredcallCDR);
        }

    foreach($lastMiscallCDR as $src=>$valueArray){
        if($lastAnsweredcallCDR[$src]['calldate']>= $valueArray['calldate'] || $lastAnsweredcallCDR[$src]['uniqueid']>= $valueArray['uniqueid']){
            unset($lastMiscallCDR[$src]);
        }
    }

    $missedcalls = array();
    $i=0;

    foreach($lastMiscallCDR as $src=>$valueArray){
        if($lastDialAnsweredcallCDR[$src]['calldate']>= $valueArray['calldate']){

        }else{
            $missedcalls[$i] = $valueArray;
            $i++;
        }
    }
    if($debug==true) {
        echo "missedcalls<br>";
        printarray($missedcalls);
    }
    unset($lastMiscallCDR);
    usort($missedcalls, 'sortByDate');
    foreach($missedcalls as $src=>$valueArray){
        if($valueArray['did'] == "777705") $missedcalls[$src]['didname']="PK krim";
        if($valueArray['did'] == "777708") $missedcalls[$src]['didname']="PK krnd";
        if($valueArray['did'] == "79280390600") $missedcalls[$src]['didname']="PK krnd";
        if($valueArray['did'] == "79263123712") $missedcalls[$src]['didname']="PK msk";
        if($valueArray['did'] == "777706" || $valueArray['did'] == "777707") $missedcalls[$src]['didname']="PK rostov";
        if($valueArray['did'] == "777729" || $valueArray['did'] == "777730") $missedcalls[$src]['didname']="Ross-Biz";
        if($valueArray['did'] == "777727" || $valueArray['did'] == "777728") $missedcalls[$src]['didname']="Rostov-holod";
        if($valueArray['did'] == "777701" ) $missedcalls[$src]['didname']="PK sochi";
        if($valueArray['did'] == "79282427127" ) $missedcalls[$src]['didname']="PK sochi";
        if($valueArray['did'] == "777702" ) $missedcalls[$src]['didname']="PK stavropol";
        if($valueArray['did'] == "777702" ) $missedcalls[$src]['didname']="PK stavropol";
        if($valueArray['did'] >= "777731" && $valueArray['did'] <= "777735") $missedcalls[$src]['didname']="KOMPLEKT krd";
        if($valueArray['did'] == "777704" ) $missedcalls[$src]['didname']="KOMPLEKT krim";
        if($valueArray['did'] == "79282073771" ) $missedcalls[$src]['didname']="KOMPLEKT krd";
        if($valueArray['did'] == "79263123727" ) $missedcalls[$src]['didname']="KOMPLEKT msk";
        if($valueArray['did'] == "79263123727" ) $missedcalls[$src]['didname']="KOMPLEKT msk";
        if($valueArray['did'] == "777736" ) $missedcalls[$src]['didname']="KOMPLEKT sochi";
        if($valueArray['did'] == "79282427747" ) $missedcalls[$src]['didname']="KOMPLEKT sochi";
        if($valueArray['did'] == "777703" ) $missedcalls[$src]['didname']="KOMPLEKT stav";
        if($valueArray['did'] == "79281113070" ) $missedcalls[$src]['didname']="KOMPLEKT-UG rst";
        if($valueArray['did'] == "777713" ) $missedcalls[$src]['didname']="KOMPLEKT-UG";
        if($valueArray['did'][0] == "7" &&
            $valueArray['did'][1] == "7" &&
            $valueArray['did'][2] == "0" &&
            $valueArray['did'][3] !== "") $missedcalls[$src]['didname']="KOMPLEKT-UG rst";
        if($valueArray['did'] >= "777721" && $valueArray['did'] <= "777726") $missedcalls[$src]['didname']="KOMPLEKT-UG";

    }
    return $missedcalls;
}

function normalizePhoneNumber($src){
    if($src[0] =="7" && strlen($src)==11){
        $src[0] ="8";
    }elseif( $src[0] =="+"){

         if($src[1] =="7"){
             $src_new ="8";
             for($i =2; $i < strlen($src); $i++ ){
                 $src_new.= $src[$i];
             }
             $src=$src_new;
         }else{
             for($i =1; $i < strlen($src); $i++ ){
                 $src_new.= $src[$i];
             }
             $src=$src_new;
         }
    }
    return $src;
}

function sortByDate($a, $b)
{
    $a = $a['calldate'];
    $b = $b['calldate'];

    if ($a == $b) return 0;
    return ($a > $b) ? -1 : 1;
}

function addPhoneNumberToBlackList($phonenumber){
    $db = connect_mysql();
    $date = new DateTime();
    $currentTime =  $date->getTimestamp()+ 3600*3 ;
    $db->insert("blacklist",array(
        'phonenumber' => $phonenumber ,
        'addeddatetime' => $currentTime
    ));
}

function delPhoneNumberFromBlackList($phonenumber){
    $db = connect_mysql();
    $db->delete(" FROM `blacklist` where `phonenumber`=  '".$phonenumber."'");
}

function makeCallBack($count){
    $db = connect_mysql();
    $callBackStatus = $db->select("`value` FROM  `settings` WHERE `key` = 'callBackStatus'",false);
    if($callBackStatus == 1){
        $settings = getSettings();
        $date = new DateTime();
        $currentTime =  $date->getTimestamp()+ 3600*3 ;
        $time30m = $currentTime - 1800;
        $time2H = $currentTime - 3600*2;
        $scheduledCalls = $db->select("phonenumber FROM `schedule`".
        "WHERE `activate` = 1 AND ".
        "((`attempt` = 0 AND `lasttimedial` <= ".$time30m.")".
        " OR ( `attempt` = 1 AND `lasttimedial` <= ".$time2H.")) ORDER BY `lasttimedial` DESC LIMIT ".$count);
        echo $db->query->last."<br>";
        if(is_array($scheduledCalls)){
            $ami = new Ami();
            $status = $ami->getConnection($settings);
            printarray($scheduledCalls);
            foreach($scheduledCalls as $key => $phoneNumber){
                $dialToNumber = array(
                    //'Channel' => 'local/113@from-internal',
                    'Channel' => 'local/12345678@from-trunk',
                    'Exten' => $phoneNumber,
                    'Context' => 'callback',
                    'CallerID' => 'CallBack '.$phoneNumber,
                   // 'Application' => 'Dial local/113@from-internal'
                    'Variable' => array ('__DIALTONUMBER'=> 'local/'.$phonenumber.'@from-internal')
                );
                $event = $ami->Originate($dialToNumber);
                $ami->execute($event);
                $db->update('schedule',
                    array('attempt'=>array("attempt + 1", cmd),
                            'lasttimedial'=> $currentTime
                        ),
                    "`phonenumber`='".$phoneNumber."'" );
                echo $db->query->last."</br>";
            }
        }else{
            echo "No any number for callback";
        }

    }
}

