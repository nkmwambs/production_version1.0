<table class="table table-bordered table-hover table-responsive datatable" id="table_export">
    <thead>

        <tr>
            <th><?php echo get_phrase('cancel/_reuse_cheque'); ?></th>
            <th><?php echo get_phrase('clear'); ?></th>
            <th><?php echo get_phrase('date'); ?></th>
            <th><?php echo get_phrase('voucher_type'); ?></th>
            <th><?php echo get_phrase('payee_/_source'); ?></th>
            <th><?php echo get_phrase('voucher_no'); ?></th>
            <th><?php echo get_phrase('description_/_details'); ?></th>
            <th><?php echo get_phrase('chq_/_ref_no'); ?></th>
            <th><?php echo get_phrase('bank_deposits'); ?></th>
            <th><?php echo get_phrase('bank_payments'); ?></th>
            <th><?php echo get_phrase('bank_balance'); ?></th>
            <th><?php echo get_phrase('cash_deposits'); ?></th>
            <th><?php echo get_phrase('cash_payments'); ?></th>
            <th><?php echo get_phrase('cash_balance'); ?></th>

            <?php foreach ($month_utilized_income_accounts as $utilized) : ?>

                <th class="spread" title="<?= $utilized['account_name']; ?>"><?= $utilized['account_code']; ?></th>

            <?php endforeach; ?>

            <?php foreach ($month_utilized_expense_accounts as $utilized_exp) : ?>

                <th class="spread" title="<?= $utilized['account_name']; ?>"><?= $utilized_exp['account_code']; ?></th>

            <?php endforeach; ?>

        </tr>

    </thead>
    <tbody>
        <?php
        $bank_balance = 0;
        $pc_balance = 0;

        ?>

        <?php
        if (!empty($voucher_records)) {
            foreach ($voucher_records as $voucher_id => $voucher_record) : ?>
                <tr>
                    <?php
                    $cheque_number = $voucher_record['cheque_number'];

                    // $explode_chqno_with_hyphen=explode('-',$cheque_number);

                    //$voucher_is_reversed=sizeof($explode_chqno_with_hyphen)==3?true:false;

                    $voucher_is_reversed = $voucher_record['voucher_is_reversed'] == 1 ? true : false;

                    $is_cheque_payment = $voucher_record['voucher_type'] == 'CHQ' ? true : false;

                    $voucher_is_cleared = false; //to be completed

                    $voucher_reversal_from=$voucher_record['voucher_reversal_from']>0?true:false;

                    $voucher_reversal_to=$voucher_record['voucher_reversal_to']>0?true:false;
                    ?>


                    <td nowrap>

                        <?php
                        if ($voucher_reversal_from ||$voucher_reversal_to ) {

                            $reverse_btn_label = get_phrase('linked_source');

                            if (!$voucher_reversal_from) {

                                $reverse_btn_label = get_phrase('linked_destination');

                                
                            }

                            //Get the hID for the voucher
                            $hID=$voucher_reversal_to > 0 ? $voucher_record['voucher_reversal_to'] : $voucher_record['voucher_reversal_from']
                        ?>
                            <a class='btn btn-danger' href='#' onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_view_voucher/<?=$hID;?>');"><?= $reverse_btn_label; ?> [<?= get_related_voucher($hID); ?>]</a>
                        <?php } ?>

                         <?php 
                         if( $voucher_record['clear_state']==0){  ?>
                        <div data-voucher_id='<?= $voucher_id; ?>' class='btn btn-primary btn_reverse  <?= ($voucher_is_reversed ? "hidden" :$voucher_reversal_from)?"hidden": ""; ?> <?= $voucher_is_cleared ? "hidden" : ""; ?>'>
                            <i class='fa fa-undo' style='cursor:pointer;'></i>
                            <?= get_phrase('cancel'); ?>
                        </div>


                        <?php if ($is_cheque_payment) { ?>
                            <div data-voucher_id='<?= $voucher_id; ?>' class='btn btn-primary btn_reverse re_use  <?= ($voucher_is_reversed ? "hidden" :$voucher_reversal_from)?"hidden": ""; ?> <?= $voucher_is_cleared ? "hidden" : ""; ?>'>
                                <i class='fa fa-plus' style='cursor:pointer;'></i>
                                <?= get_phrase('re-use_cheque'); ?>

                            </div>
                        <?php } }?>
                        
                    </td>

                    <?php

                    if (
                        $voucher_record['voucher_type'] === 'CHQ' ||
                        $voucher_record['voucher_type'] === 'BCHG' ||
                        $voucher_record['voucher_type'] === 'CR' ||
                        $voucher_record['voucher_type'] === 'UDCTB'
                    ) {

                        $chk = 'checked';

                        if ($voucher_record['clear_state'] == 1) {
                            $chk = "";
                        }
                    ?>

                        <td>
                            <?php if ($voucher_record['is_editable']) { ?>

                                <div id="<?php echo $voucher_id; ?>" class="make-switch switch-small clr" data-on-label="<?php echo $voucher_record['voucher_type']; ?>" data-off-label="<?php echo $voucher_record['voucher_type']; ?>">
                                    <input type="checkbox" <?= $chk; ?> value="<?php echo $voucher_record['voucher_number']; ?>" disabled />
                                </div>

                            <?php } else { ?>

                                <div id="<?php echo $voucher_id; ?>" class="make-switch switch-small clr" data-on-label="<?php echo $voucher_record['voucher_type']; ?>" data-off-label="<?php echo $voucher_record['voucher_type']; ?>">
                                    <input type="checkbox" <?= $chk; ?> value="<?php echo $voucher_record['voucher_number']; ?>" />
                                </div>

                            <?php } ?>

                        </td>

                    <?php } else { ?>
                        <td>
                            <div class="make-switch switch-small" data-off-label="<?php echo $voucher_record['voucher_type']; ?>">
                                <input type="checkbox" disabled>
                            </div>

                        </td>

                    <?php } ?>

                    <td nowrap class="tdate"><?php echo $voucher_record['voucher_date']; ?></td>

                    <td>

                        <?php
                        $path = 'uploads/dct_documents/' . $this->session->center_id . '/' . date('Y-m', $tym) . '/' . $voucher_record['voucher_number'] . '/';

                        if (file_exists($path) && (new \FilesystemIterator($path))->valid() && ($voucher_record['voucher_type'] == 'UDCTB' || $voucher_record['voucher_type'] == 'UDCTC')) { ?>

                            <a href='<?php echo base_url(); ?>ifms.php/dct/dct_documents_download/<?= $this->session->center_id; ?>/<?= $tym; ?>/<?= $voucher_record['VNumber']; ?>'><?= $voucher_record['voucher_record']; ?></a>

                        <?php } else { ?>

                            <span><?= $voucher_record['voucher_type']; ?></span>

                        <?php } ?>
                    </td>

                    <td><?php echo $voucher_record['payee']; ?></td>

                    <td>
                        <div class="btn btn-green popover-primary popup-ajax" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= get_phrase('click_on_the_button_to_view_a_voucher'); ?>" data-original-title="<?= get_phrase('tooltip'); ?>">
                            <input type="checkbox" class="chk_voucher" name="voucher_<?= $voucher_record['voucher_number'] ?>" value="<?= $voucher_record['voucher_number'] ?>" id="<?= $voucher_record['voucher_number'] ?>" />
                            <a href="#" onclick="showAjaxModal('<?php echo base_url(); ?>ifms.php/modal/popup/modal_view_voucher/<?php echo $voucher_id; ?>');">
                                <?php echo $voucher_record['voucher_number']; ?>
                            </a>
                        </div>
                    </td>

                    <td><?php echo $voucher_record['description']; ?></td>

                    <td>
                        <?php
                        $mixed_chq = "";

                        if ($voucher_record['voucher_type'] !== 'UDCTB') {
                            $mixed_chq_arr = explode('-', $voucher_record['cheque_number']);

                            if (sizeof($mixed_chq_arr) == 3) {
                                $mixed_chq = -$mixed_chq_arr[1];
                            } else {
                                $mixed_chq = $mixed_chq_arr[0];
                            }
                        } else {
                            $arr = explode('-', $voucher_record['cheque_number']);
                            array_pop($arr);
                            $mixed_chq = implode('-', $arr);
                        }

                        echo $mixed_chq;
                        ?>
                    </td>

                    <?php
                    $cr = '0';
                    $chq = '0';

                    if (
                        $voucher_record['voucher_type'] == 'CR' ||
                        $voucher_record['voucher_type'] == 'PCR'
                    )

                        $cr = isset($voucher_record['spread']) ? array_sum($voucher_record['spread']) : 0; //$voucher_record['running_balance']['income'];//$this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID']))->row()->Cost;

                    if (
                        $voucher_record['voucher_type'] == 'CHQ' ||
                        $voucher_record['voucher_type'] == 'BCHG' ||
                        $voucher_record['voucher_type'] == 'UDCTB'
                    )

                        $chq = isset($voucher_record['spread']) ? array_sum($voucher_record['spread']) : 0; //$voucher_record['running_balance']['expense'];//$this->db->select_sum('Cost')->get_where('voucher_body',array('hID'=>$row['hID']))->row()->Cost;
                    $bank_balance += $cr - $chq;

                    ?>

                    <td><?php echo number_format($cr, 2); ?></td>
                    <td><?php echo number_format($chq, 2); ?></td>
                    <td><?php echo number_format($bank['balance_bf'] + $bank_balance, 2); ?></td>

                    <?php
                    $pcr = '0';
                    $pc = '0';

                    if (
                        isset($voucher_record['spread']['2000']) || isset($voucher_record['spread']['2001'])
                    )

                        $pcr = isset($voucher_record['spread']) ? array_sum($voucher_record['spread']) : 0; //$voucher_record['spread']['Petty Cash'];

                    if (
                        $voucher_record['voucher_type'] == 'PC' ||
                        $voucher_record['voucher_type'] == 'PCR' ||
                        $voucher_record['voucher_type'] == 'UDCTC'
                    )
                        $pc = isset($voucher_record['spread']) ? array_sum($voucher_record['spread']) : 0; //$voucher_record['running_balance']['expense'];

                    $pc_balance += $pcr - $pc;
                    ?>
                    <td><?php echo number_format($pcr, 2); ?></td>
                    <td><?php echo number_format($pc, 2); ?></td>
                    <td><?php echo number_format($cash['balance_bf'] + $pc_balance, 2); ?></td>

                    <?php foreach ($month_utilized_income_accounts as $account_number => $income_account) { ?>
                        <td class="spread"><?= number_format(isset($voucher_record['spread'][$account_number]) ? $voucher_record['spread'][$account_number] : 0, 2); ?></td>
                    <?php } ?>

                    <?php foreach ($month_utilized_expense_accounts as $account_number => $income_account) { ?>
                        <td class="spread"><?= number_format(isset($voucher_record['spread'][$account_number]) ? $voucher_record['spread'][$account_number] : 0, 2); ?></td>
                    <?php } ?>

                </tr>
        <?php
            endforeach;
        }
        ?>
    </tbody>
</table>