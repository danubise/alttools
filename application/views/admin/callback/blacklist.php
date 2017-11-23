<?php
/**
 * User: danubise@gmail.com
 * Date: 15.11.17
 * Time: 10:54

 */

 ?>
 <form method='post' action="<?=baseurl('blacklist/addFromSettings/')?>">
    <table>
        <tr>
            <td>Новый номер:&nbsp;<input name="phonenumber"></td>
            <td>&nbsp;<button class="btn btn-primary">Добавить</button></td>
        </tr>
    </table>
</form>

 <?

    $htmlCode = "<table class=\"table table-striped\" id=\"tableNum\"><thead><tr><th><h4>Callback для номеров:</h4></th></tr><tr>".
                "<th>Номер</th><th>Дата Время добавления</th><th>Удалить</th></tr></thead><tbody>";
    if(is_array($blacklist)){
        foreach($blacklist as $key=>$value){
            $htmlCode.="<tr><td>"
                .$value['phonenumber']."&nbsp;</td><td>"
                .gmdate("Y-m-d H:i:s", $value['addeddatetime'])."&nbsp;</td><td>"
                ."<a href=".baseurl('blacklist/delFromBlacklist/').$value['phonenumber'].">Удалить</a>&nbsp;</td></tr>";
        }
    }
    $htmlCode.="</tbody></table>";

    echo $htmlCode;
?>