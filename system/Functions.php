<?php
/**
 * Created by Unix develop team.
 * User: vlad
 * Date: 19.02.15
 * Time: 22:14
 */
require 'PHPMailer_5.2.0/class.phpmailer.php';
require 'PHPMailer_5.2.0/class.smtp.php';

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
function logger($data,$id=""){
    $file="/var/log/asterisk/agi.log";
    $td=date('Y-m-d H:i:s');
    $scriptname="class_dm.php";

    $head="$td $scriptname $id ";
    $data=$head.$data;
    $data=str_replace("\n","\n".$head,$data);
    $data=trim($data)."\n";
    file_put_contents($file, $data, FILE_APPEND );
}
function sendMiscallReport(){
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions

    //Server settings
    $mail->SMTPDebug = 2;
    $mail->IsSMTP();                                      // set mailer to use SMTP
    $mail->Host = "smtp.yandex.com";  // specify main and backup server
    $mail->SMTPAuth = true;     // turn on SMTP authentication
    $mail->Username = "zvonki.ats";  // SMTP username
    $mail->Password = "secret"; // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->From = "zvonki.ats@yandex.ru";
    $mail->FromName = "zvonki.ats";
    $mail->AddAddress("danubise@gmail.com", "danubise");

    $mail->WordWrap = 50;                                 // set word wrap to 50 characters
    $mail->IsHTML(true);                                  // set email format to HTML

    $mail->Subject = "Отчет о пропущеных звонках";

    $mail->Body    = "This is the HTML message body in bold!";
    $mail->AltBody = "";

    if(!$mail->Send())
    {
       echo "Message could not be sent.
    ";
       echo "Mailer Error: " . $mail->ErrorInfo;
       exit;
    }
}
function formatHtmlPage(){
    $lastMiscallCDR = getMiscallReport();
    $htmlCode = "<table class=\"table table-striped\" id=\"tableNum\"><tr><h4>Пропущенные номера</h4></tr><thead><tr>".
                "<th>Номер</th><th>Время</th><th>Канал</th></tr></thead><tbody>";
        foreach($lastMiscallCDR as $key=>$value){
            $htmlCode.="<tr><td>"
                .$key."&nbsp;</td><td>"
                .$value['calldate']."&nbsp;</td><td>"
                .$value['did']."&nbsp;</td></tr>";
            }
            $htmlCode.="</tbody></table>";
    return $htmlCode;
}
function getMiscallReport(){
    $date = new DateTime();

    $days4 =  gmdate("Y-m-d H:i:s", $date->getTimestamp() - 3600*24*4);
    global $_config_CDR;
    $cdrdb = new db(
        $_config_CDR['mysql']['host'],
        $_config_CDR['mysql']['user'],
        $_config_CDR['mysql']['password'],
        $_config_CDR['mysql']['base']
    );
    $cdrdb->set_charset("utf8");
    $allCdrRecodrs = $cdrdb->select("src , calldate, did  FROM cdr WHERE calldate>'".$days4."' AND  disposition = 'NO ANSWER'");

    $lastMiscallCDR = array();
    foreach($allCdrRecodrs as $key=>$valueArray){

        if(strlen($valueArray['src'])>4 && is_numeric($valueArray['src'])){
            $src = $valueArray['src'];
            if($src[0] =="7" && strlen($src)==11){
                $src[0] ="8";
            }
            $lastMiscallCDR[$src]=$valueArray;
        }
    }

    $allCdrRecodrs = $cdrdb->select("src , calldate  FROM cdr WHERE calldate>'".$days4."'  AND  disposition = 'ANSWERED' AND billsec>4");

    $lastAnsweredcallCDR = array();
    foreach($allCdrRecodrs as $key=>$valueArray){
         if(strlen($valueArray['src'])>4){
             $src = $valueArray['src'];
            if($src[0] =="7" && strlen($src)==11){
                $src[0] ="8";
            }
            $lastAnsweredcallCDR[$src]=$valueArray['calldate'];
         }
    }
    $allCdrRecodrs = $cdrdb->select("dst , calldate FROM cdr WHERE  calldate>'".$days4."' AND disposition = 'ANSWERED' AND billsec>4 ");

    $lastDialAnsweredcallCDR = array();
    foreach($allCdrRecodrs as $key=>$valueArray){
         if(strlen($valueArray['dst'])>4){
              $src = $valueArray['dst'];
            if($src[0] =="7" && strlen($src)==11){
                $src[0] ="8";
            }
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
    return $lastMiscallCDR;
}