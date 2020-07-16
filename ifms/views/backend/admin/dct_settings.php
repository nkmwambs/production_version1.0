<div class='row'>    
    <div class='col-xs-12'>
        <div class='col-xs-12'>
            <div class='col-xs-3'>
                <input type='text' class='form-control' id='input_item'  name='' placeholder='<?=get_phrase('create_recipient');?>'/>
            </div>
            <div class='col-xs-3'>
                <select class='form-control' id='recipient_type' name=''>
                    <option value=''><?=get_phrase('choose_recipient_type');?></option>
                    <option value='other'><?=get_phrase('other');?></option>
                    <option value='beneficiary'><?=get_phrase('is_beneficiary');?></option>
                    <option value='household'><?=get_phrase('is_household');?></option>
                </select>
            </div>
            <div class='col-xs-3'>
                <?=get_phrase('is_active');?>: <input type='checkbox' value='' id='item_status' name='' />
            </div>
            <div class='col-xs-3'>
                <div class='btn btn-success' id='btn_item'><?=get_phrase('create');?></div>
            </div>
        </div>
        <hr/>
        <div class='col-xs-12'>
            <table class='table table-striped datatable'>
                <thead>
                    <tr>
                        <th><?=get_phrase('recipient');?></th>
                        <th><?=get_phrase('recipient_status');?></th>
                        <th><?=get_phrase('is_beneficiary');?></th>
                        <th><?=get_phrase('is_household');?></th>
                        <th><?=get_phrase('accounts');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($voucher_type_item_accounts_matrix as $voucher_type_item => $accounts_and_status){
                            $voucher_type_item_array = explode('-',$voucher_type_item);
                            $id = array_shift($voucher_type_item_array);
                        ?>
                        <tr>
                            <td id='item_<?=$id;?>'><?=implode('-',$voucher_type_item_array);?></td>
                            <td>
                                <div class='label label-info'>
                                    <?=$accounts_and_status['status']?get_phrase('active'):get_phrase('inactive');?>
                                </div>
                            </td>
                            <td><?=$accounts_and_status['is_beneficiary']?get_phrase('yes'):get_phrase('no');?></td>
                            <td><?=$accounts_and_status['is_household']?get_phrase('yes'):get_phrase('no');?></td>
                            <td>
                                <select class='form-control select2' multiple>
                                    <?php foreach($accounts_and_status['accounts'] as $account){
                                        if(!$account[0]) continue;
                                        ?>
                                        <option value='<?=$account[0];?>' selected>
                                            <?=$account[1];?> 
                                        </option>
                                    <?php }?>
                                </select>
                            </td>
                        </tr>
                    <?php }?>
                <tbody>
            </table>
        </div>
    </div>
</div>

<hr/>

<div class='row'>
    <div class='col-xs-12'>
        <div class='col-xs-12'>
            <div class='col-xs-3'>
                <input type='text' class='form-control' id='input_mode'  name='' placeholder='<?=get_phrase('create_support_mode');?>'/>
            </div>
            <div class='col-xs-3'>
                <?=get_phrase('is_dct_mode');?>: <input type='checkbox' value='' id='is_dct_mode' name='' />
            </div>
            <div class='col-xs-3'>
                <?=get_phrase('is_active');?>: <input type='checkbox' value='' id='mode_status' name='' />           
            </div>
            <div class='col-xs-3'>
                <div class='btn btn-success' id='btn_mode'><?=get_phrase('create');?></div>
            </div>
        </div>
        <hr/>
        <div class='col-xs-12'>
            <table class='table table-striped datatable'>
                <thead>
                    <tr>
                        <th><?=get_phrase('support_mode');?></th>
                        <th><?=get_phrase('support_mode_status');?></th>
                        <th><?=get_phrase('is_dct');?></th>
                        <th><?=get_phrase('accounts');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($support_mode_accounts_matrix as $support_mode => $accounts_and_status){
                            $support_mode_array = explode('-',$support_mode);
                            $id = array_shift($support_mode_array);
                        ?>
                        <tr>
                            <td id='mode_<?=$id;?>'><?=implode('-',$support_mode_array);?></td>
                            <td>
                                <div class='label label-info'>
                                    <?=$accounts_and_status['status']?get_phrase('active'):get_phrase('inactive');?>
                                </div>
                            </td>
                            <td><?=$accounts_and_status['is_dct']?get_phrase('yes'):get_phrase('no');?></td>
                            <td>
                                <select class='form-control select2' multiple>
                                    <?php foreach($accounts_and_status['accounts'] as $account){
                                        if(!$account[0]) continue;
                                        ?>
                                        <option value='<?=$account[0];?>' selected>
                                            <?=$account[1];?> 
                                        </option>
                                    <?php }?>
                                </select>
                            </td>
                        </tr>
                    <?php }?>
                <tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $('#btn_item').on('click',function(){
        var item = $('#input_item').val();
        var recipient_type = $("#recipient_type").val();
        var item_status = $("#item_status").is(':checked')?1:0;

        alert(item);
    });

    $('#btn_mode').on('click',function(){
        var mode = $('#input_mode').val();
        var is_dct_mode = $('#is_dct_mode').is(':checked')?1:0;
        var is_dct_mode = $('#mode_status').is(':checked')?1:0;

        alert(mode);
    });

</script>