<?php 
print_r($voucher_type_support_mode_matrix);
?>
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
                            <td><?=implode('-',$voucher_type_item_array);?></td>
                            <td>
                                <div class='label label-info'>
                                    <?=$accounts_and_status['status']?get_phrase('active'):get_phrase('inactive');?>
                                </div>
                            </td>
                            <td><?=$accounts_and_status['is_beneficiary']?get_phrase('yes'):get_phrase('no');?></td>
                            <td><?=$accounts_and_status['is_household']?get_phrase('yes'):get_phrase('no');?></td>
                            <td>
                                <select class='form-control select2 type_account_selector' id='item_<?=$id;?>' multiple>
                                    <?php foreach($expense_accounts as $account_id => $account_text){?>
                                        <option value='<?=$account_id;?>' <?php if(array_key_exists($account_id,$accounts_and_status['accounts'])) echo "selected";?> >
                                            <?=$account_text;?> 
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
                            <td><?=implode('-',$support_mode_array);?></td>
                            <td>
                                <div class='label label-info'>
                                    <?=$accounts_and_status['status']?get_phrase('active'):get_phrase('inactive');?>
                                </div>
                            </td>
                            <td><?=$accounts_and_status['is_dct']?get_phrase('yes'):get_phrase('no');?></td>
                            <td>
                                <select class='form-control select2 mode_account_selector' id='mode_<?=$id;?>' multiple>
                                    <?php foreach($expense_accounts as $account_id => $account_text){?>
                                        <option value='<?=$account_id;?>' <?php if(array_key_exists($account_id,$accounts_and_status['accounts'])) echo "selected";?> >
                                            <?=$account_text;?> 
                                        </option>
                                    <?php }?>
                                </select>
                            </td>
                        </tr>
                    <?php }?>
                <tbody>
            </table>
        </div>

        <hr/>

        <div class='col-xs-12'>
            <table class='table table-striped datatable'>
                <thead>
                    <tr>
                        <th><?=get_phrase('voucher_type');?></th>
                        <th><?=get_phrase('voucher_type_is_active');?></th>
                        <th><?=get_phrase('allow_support_mode_and_recipient');?></th>
                        <th><?=get_phrase('support_modes');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($voucher_type_support_mode_matrix as $voucher_type => $support_modes_and_status){
                            $voucher_type_array = explode('-',$voucher_type);
                            $id = array_shift($voucher_type_array);
                        ?>
                        <tr>
                            <td><?=implode('-',$voucher_type_array);?></td>
                            <td>
                                <div class='label label-info'>
                                    <?=$support_modes_and_status['status']?get_phrase('active'):get_phrase('inactive');?>
                                </div>
                            </td>
                            <td><?=$support_modes_and_status['allow_support_mode_and_recipient']?get_phrase('yes'):get_phrase('no');?></td>
                            <td>
                                <select class='form-control select2 voucher_type_modes_selector' id='typemode_<?=$id;?>' multiple>
                                    <?php foreach($all_support_modes as $support_mode_id => $support_mode_name){?>
                                        <option value='<?=$support_mode_id;?>' <?php if(array_key_exists($support_mode_id,$support_modes_and_status['support_modes'])) echo "selected";?> >
                                            <?=$support_mode_name;?> 
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

    $(".mode_account_selector").on('change',function(){
        var account_ids = $(this).val();
        var mode_id = $(this).attr('id').split('_')[1];
        var url = "<?=base_url();?>ifms.php/dct/update_support_mode_accounts";
        var data = {'account_ids':account_ids,'mode_id':mode_id};
        //alert(mode_id);
        $.post(url,data,function(response){
            alert(response);
        });

    });

    $(".type_account_selector").on('change',function(){
        var account_ids = $(this).val();
        var type_id = $(this).attr('id').split('_')[1];
        var url = "<?=base_url();?>ifms.php/dct/update_voucher_item_type_accounts";
        var data = {'account_ids':account_ids,'type_id':type_id};
        //alert(type_id);
        $.post(url,data,function(response){
            alert(response);
        });
    });

    $(".voucher_type_modes_selector").on('change',function(){
        var support_mode_ids = $(this).val();
        var typemode_id = $(this).attr('id').split('_')[1];
        var url = "<?=base_url();?>ifms.php/dct/update_voucher_type_support_accounts";
        var data = {'support_mode_ids':support_mode_ids,'typemode_id':typemode_id};
        //alert(type_id);
        $.post(url,data,function(response){
            alert(response);
        });
    });

</script>