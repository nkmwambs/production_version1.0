<hr>
<?php 
    //print_r($this->dct_model->dct_report(1,2,'2018-06-01',4));
    //print_r($this->dct_model->fcp_list(4));
    //print_r($this->dct_model->dct_report_fcp_amount($this->dct_model->dct_data(1,1,'2018-06-01'),$this->dct_model->all_used_accounts_in_dct_report($this->dct_model->dct_data(1,1,'2018-06-01'))));
?>
<div class='row'>
    <div class='col-xs-12'>
        <?php 
            echo form_open('' , array('class' => 'form-horizontal form-groups-bordered validate'));
        ?>
            <?php 
                //if($this->session->logged_user_level > 2){ // Only show this dropdown to managers and non-PF Nat'O staff 
            ?>
            <div class='form-group'>
                <label class='col-xs-3'>Hierarchy</label>
                <div class='col-xs-9'> 
                    <select class='form-control filters' id='hierarchy_id'>
                        <option value=''>Select a hierarchy</option>
                        <?php foreach($fcp_hierarchy as $hierarchy){?>
                            <option value='<?=$hierarchy['hierarchy_id'];?>'><?=$hierarchy['hierarchy_name'];?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <?php //}?>

            <div class='form-group'>
                <label class='col-xs-3'>Aggregation type</label>
                <div class='col-xs-9'> 
                    <select class='form-control filters' id='aggregation_type'>
                        <option value=''>Select aggregation type</option>
                        <option value='1'>Amount Spent</option>
                        <option value='2'>Count of Beneficiaries</option>
                        <option value='3'>Count of Households</option>
                        <option value='4'>Count of Beneficiaries and Households</option>
                    </select>
                </div>
            </div>

            <div class='form-group'>
                <label class='col-xs-3'>Group data by</label>
                <div class='col-xs-9'> 
                    <select class='form-control filters' id='group_by'>
                        <option value=''>Select grouping</option>
                        <option value='1'>FCP By Fund</option>
                        <option value='2'>FCP By CIV</option>
                        <option value='3'>FCP By Support Mode</option>
                    </select>
                </div>
            </div>

            <div class='form-group'>
                <label class='col-xs-3'>Month</label>
                <div class='col-xs-9'> 
                    <input type='text' id='month' readonly='readonly' data-format='yyyy-mm-dd' value='<?=date('Y-m-t');?>' class='form-control datepicker filters'/>
                </div>
            </div>

            <div class='form-group'>
                <div class='col-xs-12'>
                    <div class='btn btn-success disabled' id='run'>Run</div>
                </div>
            </div>

        </form>
    </div>
</div>

<hr>

<div class='row'>
    <div class='col-xs-12' id='load_report'>
        <div class='well'>Select appropriate aggregation type and grouping and click run to populate data</div>
    </div>
</div>

<script>

    $('.filters').on('change',function(){
        var count_empty = 0;
        $('.filters').each(function(i,elem){
            if($(elem).val() == ''){
                count_empty++;
            }
        });

        if(count_empty == 0){
            $("#run").removeClass('disabled');
        }else{
            $("#run").addClass('disabled');
        }
    });


    $('#run').on('click',function(){
        var url = "<?=base_url();?>reports.php/admin/ajax_load_dct_expense_report";
        var aggregation_type = $('#aggregation_type').val();
        var group_by = $('#group_by').val();
        var hierarchy_id = $("#hierarchy_id") ? $("#hierarchy_id").val() : 0;
        var month = $('#month').val();
        var data = {'aggregation_type':aggregation_type,'group_by':group_by,'month':month,'hierarchy_id':hierarchy_id};
        
        if(aggregation_type == '' || group_by == '' || month == ''){
            alert('one or more filters are empty');
            return false;
        }

        $.post(url,data,function(response){
            $("#load_report").html(response);
        });

    });
</script>

