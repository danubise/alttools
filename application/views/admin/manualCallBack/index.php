<?php
if( $settings['manualCallBack'] == 1 ){
    $buttonAction = "stop";
    $btnAction ='btn-danger';
    $btnText = "Отключить обзвон";
    $status = "Включен";
    $alertClass = "alert-success";
}else{
    $buttonAction = "start";
    $btnAction ='btn-success';
    $btnText = "Включить обзвон";
    $status = "Отключен";
    $alertClass = "alert-danger";
}

?>

<table>
    <tr>
        <td>
        &nbsp;<a href="<?=baseurl('manualCallBack/'.$buttonAction)?>" class="btn <?=$btnAction?>"><?=$btnText?></a>
    </td>
    </tr>
</table>

<?

$htmlCode = "<table class=\"table table-striped\" id=\"tableNum\"><thead><tr><th><h4>Обзвон для номеров:</h4></th></tr><tr>".
            "<th>Номер</th><th>Дата Время</th></tr></thead><tbody>";
if(is_array($dialStatistic)){
    foreach($dialStatistic as $key=>$value){
        $htmlCode.="<tr><td>"
            .$value['phonenumber']."&nbsp;</td><td>"
            .$value['manualDialDateTime']."&nbsp;</td></tr>";
    }
}
$htmlCode.="</tbody></table>";

echo $htmlCode;
?>