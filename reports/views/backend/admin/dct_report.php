<hr>
<?php 
    //print_r($data);
?>
<div class='row'>
    <div class='col-xs-12'>
        <?php 
            echo form_open('' , array('class' => 'form-horizontal form-groups-bordered validate'));
        ?>
            <div class='form-group'>
                <label class='col-xs-3'>Aggregation type</label>
                <div class='col-xs-9'> 
                    <select class='form-control filters' id='aggregation_type'>
                        <option value=''>Select aggregation type</option>
                        <option value='1'>Amount Spent</option>
                        <option value='2'>Count of Beneficiary</option>
                        <option value='3'>Count of Caregivers</option>
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
        var month = $('#month').val();
        var data = {'aggregation_type':aggregation_type,'group_by':group_by,'month':month};

        if(aggregation_type == '' || group_by == '' || month == ''){
            alert('one or more filters are empty');
            return false;
        }

        $.post(url,data,function(response){
            $("#load_report").html(response);
        });

    });
</script>