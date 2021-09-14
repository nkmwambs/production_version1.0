<?php
//print_r($transfer_request); 
?>
<div class="row">
    <div class="col-sm-12">

        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="fa fa-search"></i>
                    <?php echo get_phrase('funds_transfer_request'); ?>
                </div>
            </div>
            <div class="panel-body" style="max-width:50; overflow: auto;">
                <a href="<?= base_url(); ?>ifms.php/facilitator/list_funds_transfer_requests" class="btn btn-primary" id="list_transfer">List Fund Transfer Requests</a>
                <hr />

                <table class="table table-striped ">
                    <thead>
                        <tr>
                            <td colspan="2" style="text-align:center;">
                                <?= $transfer_request['fcp_number']; ?> <br />
                                Transfer Request
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span style="font-weight:bold;">Request Date:</span> <?= $transfer_request['raise_date']; ?></td>
                            <td><span style="font-weight:bold;">Voucher Number:</span> <?= !$transfer_request['voucher_number'] ? "Not yet assigned" : $transfer_request['voucher_number']; ?></td>
                        </tr>
                        <tr>
                            <td><span style="font-weight:bold;">Vendor Payee:</span> <?= $transfer_request['fcp_number']; ?></td>
                            <td><span style="font-weight:bold;">Request Raised By:</span> <?= $transfer_request['requestor']; ?></td>
                        </tr>
                        <tr>
                            <td><span style="font-weight:bold;">Request Status:</span> <?= $transfer_request['transfer_status_label']; ?></td>
                            <td><span style="font-weight:bold;">Amount:</span> <?= $transfer_request['amount']; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <span style="font-weight:bold;">Description:</span>
                                <?= $transfer_request['description']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><span style="font-weight:bold;">Source Account:</span>
                                <?php
                                echo $transfer_request['source_account'];
                                echo $transfer_request['source_civa_account'] != null ? ' [' . $transfer_request['source_civa_account'] . '] ' : null
                                ?>
                            </td>

                            <td><span style="font-weight:bold;">Destination Account</span>
                                <?php
                                echo $transfer_request['destination_account'];
                                echo $transfer_request['destination_civa_account'] != null ? ' [' . $transfer_request['destination_civa_account'] . '] ' : null
                                ?>
                            </td>

                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>