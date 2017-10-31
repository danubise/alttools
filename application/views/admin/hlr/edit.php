<form method="post"  enctype="multipart/form-data" action="<?=baseurl('hlrrequest/save/')?>">

    <table class="table table-striped" id="tableNum">
        <tr><h4>Результат ответа</h4></tr>
        <thead>
            <tr>
                <th>Оригинальное</th>
                <th>Новое</th>
                <th>Значение</th>
            </tr>
        </thead>
        <tbody>
        <?
        if(isset($joinTables)):
            foreach($joinTables as $key=>$valuesArray):
              ?>
              <tr>
                <td><?=$valuesArray['original']?>&nbsp;</td>
                <td><input type='text' name='edited[<?=$valuesArray['original']?>]' value='<?=$valuesArray['edited']?>'>&nbsp;</td>
                <td><?=$valuesArray['value']?>&nbsp;</td>
              </tr>
            <?php
            endforeach;
        endif;
        ?>
        <tr><td><button class="btn btn-primary">Сохранить</button></td><td></tr>
        </tbody>
    </table>
</form>