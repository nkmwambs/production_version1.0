<button style="display: none;margin-right: 10px;" class="btn btn-primary btn-icon pull-left" id="print_vouchers"><i class="fa fa-print"></i><?= get_phrase('print_vouchers'); ?></button>

<div class="col-sm-offset-1 btn-group right-dropdown">
    <button type="button" id="" class="btn btn-blue"><?php echo get_phrase('financial_reports'); ?></button>
    <button type="button" id="" class="btn btn-blue dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
    </button>

    <ul class="dropdown-menu dropdown-blue" role="menu">

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_outstanding_cheques/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>')"><?php echo get_phrase('outstanding_cheques'); ?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_transit_deposits/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>')"><?php echo get_phrase('deposits_in_transit'); ?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_cleared_effects/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>')"><?php echo get_phrase('cleared_effects'); ?></a>

        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_bank_reconcile/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>')"><?php echo get_phrase('bank_reconciliation'); ?> <span class="badge badge-<?= $rec_color; ?>"><?= $rec_chk; ?></span></a>
        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_variance_explanation/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>')"><?php echo get_phrase('variance_explanation'); ?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_proof_of_cash/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>')"><?php echo get_phrase('proof_of_cash'); ?> <span class="badge badge-<?= $proof_color; ?>"><?= $proof_chk ?></span></a>

        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_fund_balances/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>')"><?php echo get_phrase('fund_balance_report'); ?></a>

        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_expense_report/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>')"><?php echo get_phrase('expense_report'); ?></a>

        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_fund_ratios/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>')"><?php echo get_phrase('financial_ratios'); ?></a>

        </li>


        <li class="divider"></li>

        <li>
            <a href="<?php echo base_url(); ?>ifms.php/partner/bank_statements/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>"><?php echo get_phrase('upload_bank_statements'); ?></a>
        </li>

        <li class="divider"></li>

        <!--<li class="divider"></li>-->

        <li style="<?= $hide_status; ?>">
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_submit_mfr/<?php echo date('Y-m-t', $tym); ?>/<?=$fcp_number;?>');"><?php echo get_phrase('submit_financial_report'); ?></a>

        </li>


    </ul>
</div>

<div class="btn btn-default pull-right col-sm-3"><a href="#" class="fa fa-backward scroll" id="prev"></a> <?= get_phrase('you_are_in'); ?> <?= date('F Y', $tym); ?> <input type="text" class="form-control col-sm-1" id="cnt" placeholder="<?= get_phrase('enter_number_of_months'); ?>" /> <a href="#" class="fa fa-forward scroll" id="next"></a></div>