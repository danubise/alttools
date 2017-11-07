<?php
/**
 * User: danubise@gmail.com
 * Date: 30.10.17
 * Time: 10:54

 */
 echo formatHtmlPage();
?>
<table class="table table-striped" id="tableNum">
    <tr><h4>Пропущенные номера</h4></tr>
    <thead>
        <tr>
            <th>Номер</th>
            <th>Время</th>
            <th>Канал</th>
        </tr>
    </thead>
    <tbody>
    <?
    if(isset($lastMiscallCDR)):
        foreach($lastMiscallCDR as $key=>$value):
          ?>
          <tr>
              <td><?=$key?>&nbsp;</td>
              <td><?=$value['calldate']?>&nbsp;</td>
              <td><?=$value['did']?>&nbsp;</td>
          </tr>
        <?php
        endforeach;
    endif;
    ?>
    </tbody>
</table>
