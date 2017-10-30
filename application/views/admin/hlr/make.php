<?php
/**
 * User: slava
 * Date: 30.10.17
 * Time: 10:54

 */

?>
<form method="post"  enctype="multipart/form-data" action="<?=baseurl('hlrrequest/make/')?>">
    <table>
        <tr>
            <td>
                Введите номер телефона:&nbsp;</td>
            <td>
                <input name="parametr[number]" class="form-control" value="<?=$parametr['number']?>"></td>
            <td>&nbsp;</td>
            <td>
                <button class="btn btn-primary">Выполнить</button></td>
        </tr>
        <tr><td>&nbsp;</td></tr>
    </table>
    <table>
        <tr>
            <td><?=$url?>&nbsp;</td>
        </tr>
    </table>
</form>
<?php
    if(!isset($arrayResponse)){
        die;
    }
?>
<form method="post"  enctype="multipart/form-data" action="<?=baseurl('hlrrequest/make/')?>">

    <table class="table table-striped" id="tableNum">
        <tr><h4>Результат ответа</h4></tr>
        <thead>
            <tr>
                <th>Ключ</th>
                <th>Значение</th>
            </tr>
        </thead>
        <tbody>
        <?
        if(isset($arrayResponse)):
            foreach($arrayResponse as $key=>$value):
              ?>
              <tr>
                  <td><input type='hidden' name='' value='<?=$key?>'><?=$key?>&nbsp;</td>
                  <td><?=$value?>&nbsp;</td>
              </tr>
            <?php
            endforeach;
        endif;
        ?>
        </tbody>
    </table>
</form>