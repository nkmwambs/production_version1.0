<?php
//print_r($transfer_requests);
?>
<div class="row">
    <div class="col-sm-12">

        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('funds_transfers'); ?>
                </div>
            </div>
            <div class="panel-body" style="max-width:50; overflow: auto;">
                <a href="<?= base_url(); ?>ifms.php/partner/funds_transfer" class="btn btn-primary" id="list_transfer">New Funds Transfer Requests</a>
                <hr />

                <table class="table table-striped datatable">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>FCP ID</th>
                            <th>Date</th>
                            <th>Source Account</th>
                            <th>Destination Account</th>
                            <th>Amount</th>
                            <th>Request Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transfer_requests as $transfer_request) { ?>
                            <tr>
                                <td>
                                    <div class="btn-group">
                                        <button id="" type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                            <?= $transfer_request['transfer_status_label']; ?> <span class="caret"></span>
                                        </button>

                                        <ul class="dropdown-menu dropdown-default pull-left" role="menu">
                                            <li>
                                                <a target="_blank" href="<?= base_url(); ?>ifms.php/partner/view_funds_transfer_request/<?= $transfer_request['request_id']; ?>"><?php echo get_phrase('view'); ?></a>
                                            </li>
                                            <?php if ($transfer_request['transfer_status'] == 0) { ?>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="<?= base_url(); ?>ifms.php/partner/funds_transfer/<?= $transfer_request['request_id']; ?>"><?php echo get_phrase('edit'); ?></a>
                                                </li>

                                                <li class="divider"></li>
                                                <li>
                                                    <!-- <a href="#" onclick="confirm_modal('<?php echo base_url(); ?>ifms.php/partner/delete_transfer_request/<?= $transfer_request['request_id']; ?>')"><?php echo get_phrase('delete'); ?></a> -->
                                                    <a href="#" id="delete_<?= $transfer_request['request_id']; ?>" class="btn_delete"><?php echo get_phrase('delete'); ?></a>
                                                </li>

                                            <?php } ?>
                                        </ul>

                                    </div>
                                </td>
                                <td><?= $transfer_request['fcp_number']; ?></td>
                                <td><?= $transfer_request['raise_date']; ?></td>
                                <td>
                                    <?php
                                    echo $transfer_request['source_account'];
                                    echo $transfer_request['source_civa_account'] != null ? ' [' . $transfer_request['source_civa_account'] . '] ' : null
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $transfer_request['destination_account'];
                                    echo $transfer_request['destination_civa_account'] != null ? ' [' . $transfer_request['destination_civa_account'] . '] ' : null
                                    ?>
                                <td><?= number_format($transfer_request['amount'], 2); ?></td>
                                <td><?= $transfer_request['created_date']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>

    $(".datatable").DataTable();

    $(".btn_delete").on("click", function() {
        const id = $(this).attr('id').split("_")[1];
        const btn = $(this);

        const cfrm = confirm("Are you sure you want to delete this?");

        if (!cfrm) {
            alert("Delete aborted");
        } else {
            const url = "<?= base_url(); ?>ifms.php/partner/delete_transfer_request/" + id;

            $.get(url, function(response) {
                if (response == 1) {
                    alert("Request deleted successfully");
                    btn.closest('tr').remove();
                } else {
                    alert("Request deletion failed");
                }

            })
        }
    });
</script>