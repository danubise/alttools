<?php
/**
 * User: danubise@gmail.com
 * Date: 15.11.17
 * Time: 10:54

 */
if($callbackstatus == 1){
    $btnAction ='btn-danger';
    $btnText = "Отключить Callback";
}else{
    $btnAction ='btn-success';
    $btnText = "Включить Callback";
}
 ?>
 <form method='post' action="<?=baseurl('callbacksettings/addFromSettings/')?>">
    <table>
        <tr>
            <td><a href="<?=baseurl('callbacksettings/enablecallback')?>" class="btn <?=$btnAction?>"><?=$btnText?></a>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Новый номер:&nbsp;<input name="phonenumber"></td>
            <td>&nbsp;<button class="btn btn-primary">Добавить</button></td>
        </tr>
    </table>
</form>

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