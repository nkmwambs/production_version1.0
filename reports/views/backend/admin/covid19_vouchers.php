
<div class="row">
	<div class="col-xs-12">
	
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title" >
                    <i class="fa fa-list"></i>
                        <?php echo get_phrase('expense_breakdown');?>
                </div>
            </div>
                <div class="panel-body"  style="max-width:50; overflow: auto;">
                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th><?=get_phrase('fcp_number');?></th>
                                <th><?=get_phrase('date');?></th>
                                <th><?=get_phrase('voucher_number');?></th>
                                <th><?=get_phrase('amount');?></th>
                            </tr>
                        <thead>
                        <tbody>
                            <?php foreach($vouchers as $voucher){?>
                                <tr>
                                    <td><?=$voucher['fcp_number'];?></td>
                                    <td><?=$voucher['transaction_date'];?></td>
                                    <td><a href="#" onclick="showAjaxModal('<?php echo base_url();?>ifms.php/modal/popup/partner-modal_view_voucher/<?=$voucher['voucher_id'];?>');"><?=$voucher['voucher_number'];?></a></td>
                                    <td><?=$voucher['Cost'];?></td>
                                </tr>
                            <?php }?>
                        </tbody>

                        <tfoot>
                                <tr>
                                    <td><?=get_phrase('total');?></td>
                                    <td colspan="3"><?=array_sum(array_column($vouchers,'Cost'));?></td>
                                </tr>
                        </tfoot>
                    </table>
                </div>
        </div>
    </div>    
</div>

<div class="row">
	<div class="col-xs-12" id='show_voucher_placeholder'>

	</div>
</div>

<script>
    // $('.voucher_number').on('click',function(){

    // });
</script>