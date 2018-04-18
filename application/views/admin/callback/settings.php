<?php
/**
 * User: danubise@gmail.com
 * Date: 15.11.17
 * Time: 10:54

 */
$settings = getSettings();
if($callbackstatus == 1){
    $btnAction ='btn-danger';
    $btnText = "Отключить Callback";
    $status = "Включен";
    $alertClass = "alert-success";
}else{
    $btnAction ='btn-success';
    $btnText = "Включить Callback";
    $status = "Отключен";
    $alertClass = "alert-danger";
}

if ($this->user_model->owner->login == "admin"){
    $activateAction = "<a href=".baseurl('callbacksettings/enablecallback').
    " class=\"btn ".$btnAction."\">".$btnText."</a>";
}else{
    $activateAction = "<div class=\"alert ".$alertClass."\">Статус callback : <strong>".$status."</strong></div>";
}
 ?>

    <table>
        <tr>
            <td><?=$activateAction?>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>
            <form method="post" action="<?=baseurl('callbacksettings/setCallbackInterval') ?>">
                Интервал дозвона&nbsp;
                <input name="CallBackIntervalMinute" value="<?=$settings['CallBackIntervalMinute'] ?>">
                &nbsp;
                <button class="btn btn-primary">Сохранить</button>
            </form>
            </td>
        </tr>
    </table>


 <?

    $htmlCode = "<table class=\"table table-striped\" id=\"tableNum\"><thead><tr><th><h4>Callback для номеров:</h4></th></tr><tr>".
                "<th>Номер</th><th>Кол-во попыток</th><th>Время последней попытки</th><th>CallBack</th></tr></thead><tbody>";
    if(is_array($addednumbers)){
        foreach($addednumbers as $key=>$value){
            $htmlCode.="<tr><td>"
                .$value['phonenumber']."&nbsp;</td><td>"
                .$value['attempt']."&nbsp;</td><td>"
                .gmdate("Y-m-d H:i:s", $value['lasttimedial'])."&nbsp;</td><td>"
                ."<a href=".baseurl('callbacksettings/delFromSettings/').$value['phonenumber'].">Отключить</a>&nbsp;</td></tr>";
        }
    }
    $htmlCode.="</tbody></table>";

    echo $htmlCode;
?>