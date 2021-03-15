<div class="btn-group">
    <button id="" type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <?php echo get_phrase('action'); ?> <span class="caret"></span>
    </button>

    <ul class="dropdown-menu dropdown-default pull-right" role="menu">
        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_outstanding_cheques/<?php echo date('Y-m-t', $tym); ?>/<?= $fcp; ?>')"><?php echo get_phrase('outstanding_cheques'); ?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_transit_deposits/<?php echo date('Y-m-t', $tym); ?>/<?= $fcp; ?>')"><?php echo get_phrase('deposits_in_transit'); ?></a>
        </li>


        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_bank_reconcile/<?php echo date('Y-m-t', $tym); ?>/<?= $fcp; ?>')"><?php echo get_phrase('bank_reconciliation'); ?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_variance_explanation/<?php echo date('Y-m-t', $tym); ?>/<?= $fcp; ?>')"><?php echo get_phrase('variance_explanation'); ?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_proof_of_cash/<?php echo date('Y-m-t', $tym); ?>/<?= $fcp; ?>')"><?php echo get_phrase('proof_of_cash'); ?></a>

        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_fund_balances/<?php echo date('Y-m-t', $tym); ?>/<?= $fcp; ?>')"><?php echo get_phrase('fund_balance_report'); ?></a>

        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_expense_report/<?php echo date('Y-m-t', $tym); ?>/<?= $fcp; ?>')"><?php echo get_phrase('expense_report'); ?></a>

        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_fund_ratios/<?php echo date('Y-m-t', $tym); ?>/<?= $fcp; ?>')"><?php echo get_phrase('financial_ratios'); ?></a>

        </li>


        <li class="divider"></li>

        <li>
            <a target='__blank' href="<?php echo base_url(); ?>ifms.php/facilitator/cash_journal/<?php echo $tym; ?>/<?= $fcp; ?>"><?php echo get_phrase('cash_journal'); ?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a target='__blank' href="<?php echo base_url(); ?>ifms.php/facilitator/bank_statements/<?php echo $tym; ?>/<?= $fcp; ?>"><?php echo get_phrase('bank_statements'); ?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a href="#" onclick="confirm_dialog('<?php echo base_url(); ?>ifms.php/facilitator/decline_mfr/<?= $tym; ?>/<?= $fcp; ?>');"><?php echo get_phrase('decline_financial_report'); ?></a>
        </li>

        <li class="divider"></li>

        <li>
            <a target='__blank' href="<?php echo base_url(); ?>ifms.php/facilitator/plans/<?= $tym; ?>/<?= $fcp; ?>"><?php echo get_phrase('budget'); ?></a>
        </li>

        <li class="divider"></li>
    </ul>
</div>