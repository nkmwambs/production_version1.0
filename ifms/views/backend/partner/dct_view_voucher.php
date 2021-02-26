<?php
    extract($record);
?>

<style>
@media print {
  a[href]:after {
    content: none !important;
  }
}
</style>

<table class="table table-striped datatable">
    <thead>
        <tr>
            <th colspan="6" style="text-align:center;"><?php echo $icpNo;?><br><?=get_phrase('transaction_voucher');?>
            </th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td colspan="4"><span style="font-weight: bold;">Date: </span> <?php echo $TDate;?></td>
            <td colspan="3"><span style="font-weight: bold;">Number: </span> <?php echo $VNumber;?></td>
        </tr>

        <tr>
            <td colspan="4"><span style="font-weight: bold;">Vendor/Payee: </span> <?php echo $Payee;?></td>
            <?php $chqNo = explode("-",$chqNo);?>
            <td colspan="3"><span style="font-weight: bold;">Cheque Number: </span> <?php echo $chqNo[0];?></td>
        </tr>

        <tr>
            <td colspan="4"><span style="font-weight: bold;">Address: </span> <?php echo $Address;?></td>
            <td colspan="3"><span style="font-weight: bold;">Voucher Type: </span> <?php echo $VType;?></td>
        </tr>

        <tr>
            <td colspan="8"><span style="font-weight: bold;">Description: </span> <?php echo $TDescription;?>
            </td>
        </tr>

        <tr style="font-weight: bold;" id="tr_header">
            <th><?php echo get_phrase('quantity'); ?></th>
			<th><?php echo get_phrase('Recipient'); ?></th>
			<th><?php echo get_phrase('account'); ?></th>
			<th><?php echo get_phrase('support_mode'); ?></th>
			<th><?php echo get_phrase('details_/_particulars');?></th>
			<th><?php echo get_phrase('unit_cost'); ?></th>
			<th><?php echo get_phrase('cost'); ?></th>
        </tr>

        <?php
								
			$sum_cost = 0;
            $row_index = 2;
            foreach($body as $row):
		?>
        <tr>
            <td><?php echo $row['Qty'];?></td>
            <td><?php echo $row['voucher_item_type_name'];?></td>
            <td><?php echo $row['AccText'];?></td>
            <td><?php echo $row['support_mode_name'];?> 
                <?php 
                    if($row['support_mode_is_dct']){
                ?>
                <div class="hidden-print">
                <?php
                        echo list_s3_uploaded_documents($this->dct_model->uploaded_dct_documents($VNumber,$TDate,$row['support_mode_id'],$row_index),false);
                ?>
                </div>
                <?php
                    }
                ?>
                
                
            </td>
            <td><?php echo $row['Details'];?></td>
            <td><?php echo number_format($row['UnitCost'],2);?></td>
            <td><?php echo number_format($row['Cost'],2);?></td>
           
        </tr>
        <?php
                $sum_cost +=$row['Cost'];
                $row_index++;
			endforeach;
		?>
        <tr>
            <td colspan="4" style="font-weight: bold;">Totals</td>
            <td colspan="2"><?php echo number_format($sum_cost,2);?></td>
        </tr>

        <tr>
            <td colspan='2'><span style="font-weight: bold;">Raised By</span></td>
            <td colspan="2"><span style="font-weight: bold;">Name: </span> </td>
            <td colspan="3"><span style="font-weight: bold;">Signature: </span></td>
        </tr>
        <tr>
            <td colspan="4"><span style="font-weight: bold;">Verified By</span></td>
            <td colspan="3"><span style="font-weight: bold;">Approved By</span></td>
        </tr>
        <tr>
            <td colspan='2'>Name: </td>
            <td colspan="2">Signature: </td>
            <td>Name: </td>
            <td colspan="2">Signature</td>
        </tr>
        <tr>
            <td colspan='2'>Name: </td>
            <td colspan="2">Signature: </td>
            <td>Name: </td>
            <td colspan="2">Signature</td>
        </tr>

        <tr>
            <td colspan='2'>Name: </td>
            <td colspan="2">Signature: </td>
            <td>Name: </td>
            <td colspan="2">Signature</td>
        </tr>
    </tbody>
</table>
