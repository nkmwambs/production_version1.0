<?php
// print_r($transfer_request);

$request_id = 0;
$transfer_type = 0;
$source_civa_account = null;
$destination_civa_account = null;
$description = null;
$amount = null;
$source_accounts = [];
$destination_accounts = [];

if (!empty($transfer_request)) {
    extract($transfer_request);
    extract($accounts);
}
?>
<div class="row">
    <div class="col-sm-12">

        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('new_funds_transfer'); ?>
                </div>
            </div>
            <div class="panel-body" style="max-width:50; overflow: auto;">
                <a href="<?= base_url(); ?>ifms.php/partner/list_funds_transfer_requests" class="btn btn-primary" id="list_transfer">List Fund Transfer Requests</a>
                <hr />
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td>
                                <?= get_phrase("transfer_type"); ?>
                            </td>
                            <td>
                                <select class="form-control" name="transfer_type" id="transfer_type">
                                    <option value="0"><?= get_phrase('select_transfer_type'); ?></option>
                                    <option value="1" <?= $transfer_type == 1 ? 'selected' : null; ?>><?= get_phrase("income_transfer"); ?></option>
                                    <option value="2" <?= $transfer_type == 2 ? 'selected' : null; ?>><?= get_phrase("expense_transfer"); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?= get_phrase("is_source_account_a_CIV?"); ?>
                            </td>
                            <td colspan="2">
                                <input type="checkbox" <?= $source_civa_account != null ? 'checked = "checked"' : null; ?> name="is_source_account_civ" id="is_source_account_civ" value="1" />
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?= get_phrase("is_destination_account_a_CIV?"); ?>
                            </td>
                            <td colspan="2">
                                <input type="checkbox" <?= $destination_civa_account != null ? 'checked = "checked"' : null; ?> name="is_destination_civ" id="is_destination_civ" value="1" />
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <textarea class="form-control" name="transfer_description" id="transfer_description" rows="5" placeholder="<?= get_phrase('enter_transfer_details_here'); ?>"><?= $description != null ? $description : null; ?></textarea>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <button class="btn btn-danger" type="button" id="show_accounts" <?= $transfer_type == 0 ? "disabled" : null ?>><?= get_phrase('show_accounts'); ?></button>
                            </td>
                        </tr>

                        <tr>
                            <td><?= get_phrase('source_account'); ?></td>
                            <td>
                                <select class="form-control type_dependant type_dependant_select" name="source_account" id="source_account" <?= $transfer_type == 0 ? "disabled" : null ?>>
                                    <option value=""><?= get_phrase("select_account"); ?></option>
                                    <?php
                                    if (!empty($source_accounts)) {
                                        foreach ($source_accounts as $source_account_item => $source_account_code) {
                                            $selected = "";

                                            if ($source_civa_account != null && $source_account_item == $civa_from) {
                                                $selected = "selected";
                                            } elseif ($source_civa_account == null && $source_account_item == $acfrom) {
                                                $selected = "selected";
                                            }
                                    ?>
                                            <option value="<?= $source_account_item; ?>" <?= $selected; ?>><?= $source_account_code; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>


                        <tr>
                            <td><?= get_phrase("destination_account"); ?></td>
                            <td>
                                <select class="form-control type_dependant type_dependant_select" name="destination_account" id="destination_account" <?= $transfer_type == 0 ? "disabled" : null ?>>
                                    <option value=""><?= get_phrase("select_account"); ?></option>
                                    <?php
                                    if (!empty($destination_accounts)) {
                                        foreach ($destination_accounts as $destination_account_item => $destination_account_code) {
                                            $selected = "";

                                            if ($destination_civa_account != null && $destination_account_item == $civa_to) {
                                                $selected = "selected";
                                            } elseif ($destination_civa_account == null && $destination_account_item == $acto) {
                                                $selected = "selected";
                                            }
                                    ?>
                                            <option value="<?= $destination_account_item; ?>" <?= $selected; ?>><?= $destination_account_code; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td><?= get_phrase("amount_to_be_transferred"); ?></td>
                            <td>
                                <input type="text" class="form-control type_dependant type_dependant_input" name="transfer_amount" id="transfer_amount" value="<?= $amount != null ? $amount : 0; ?>" <?= $transfer_type == 0 ? "disabled" : null ?> />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <button <?= $transfer_type == 0 ? "disabled" : null ?> id="submit" class="btn btn-success"><?= get_phrase("submit"); ?></button>
                                <button id="clear" class="btn btn-success"><?= get_phrase("clear"); ?></button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function reset_form() {
        // Disable accounts and remove their options
        $(".type_dependant").each(function(i, elem) {
            if ($(elem).is("select")) {
                $(elem).find('option').not(':first').remove();
            }
            $(elem).attr("disabled", "disabled");
            $("#transfer_amount").val(0);
            $("#transfer_description").val("");
            $("#submit").attr('disabled', 'disabled');

        })

        $("#transfer_type").val(0);

        $("#is_source_account_civ").prop("checked", false);
        $("#is_destination_civ").prop("checked", false);

        $("#show_accounts").attr('disabled', "disabled");


    }


    $("#transfer_type, #is_source_account_civ, #is_destination_civ").on('change', function() {

        // Disable accounts and remove their options
        $(".type_dependant").each(function(i, elem) {
            if ($(elem).is("select")) {
                $(elem).find('option').not(':first').remove();
            }
            $(elem).attr("disabled", "disabled");
            $("#transfer_amount").val(0);
            $("#transfer_description").val("");
            $("#submit").attr('disabled', 'disabled');

        })

        if ($(this).attr("id") == "transfer_type") {
            $("#is_source_account_civ").prop("checked", false);
            $("#is_destination_civ").prop("checked", false);
        }


        if ($("#transfer_type").val() != 0) {
            $("#show_accounts").removeAttr('disabled');
        } else {
            $("#show_accounts").attr('disabled', "disabled");
        }
    })

    $("#show_accounts").on('click', function() {

        const url = '<?= base_url(); ?>ifms.php/partner/funds_transfer_accounts'
        const data = {
            transfer_type: $("#transfer_type").val(),
            is_source_account_civ: $("#is_source_account_civ").is(":checked") ? 1 : 0,
            is_destination_civ: $("#is_destination_civ").is(":checked") ? 1 : 0,
        };

        $.post(url, data, (response) => {
            //alert(response);

            // Enable type_dependant elements and populate accounts
            $(".type_dependant").each(function(i, elem) {
                $(elem).removeAttr('disabled');
            })

            const response_json = JSON.parse(response);

            let options = '';

            $.each(response_json.source_accounts, function(source_account, source_account_code) {
                $("#source_account").append("<option value='" + source_account + "'>" + source_account_code + "</option>");
            })

            $.each(response_json.destination_accounts, function(destination_account, destination_account_code) {
                $("#destination_account").append("<option value='" + destination_account + "'>" + destination_account_code + "</option>");
            })

            $("#submit").removeAttr("disabled");

        });
    })

    $("#clear").on("click", function() {
        reset_form();
    })

    $("#submit").on("click", function() {
        $(this).attr("disabled", true);
        $("#clear").attr("disabled", true);
        // Prevent posting amount <= 0, no transfer type and source anmd destination accounts selected
        const data = {
            transfer_type: $("#transfer_type").val(),
            is_source_account_civ: $("#is_source_account_civ").is(":checked") ? 1 : 0,
            is_destination_civ: $("#is_destination_civ").is(":checked") ? 1 : 0,
            transfer_description: $("#transfer_description").val(),
            source_account: $("#source_account").val(),
            destination_account: $("#destination_account").val(),
            transfer_amount: $("#transfer_amount").val()
        };

        const url = '<?= base_url(); ?>ifms.php/partner/post_funds_transfer/<?= $request_id; ?>';


        $.post(url, data, function(response) {
            alert(response);
            //window.location.reload();
            window.location.href = "<?= base_url(); ?>ifms.php/partner/list_funds_transfer_requests";
        });
    })
</script>