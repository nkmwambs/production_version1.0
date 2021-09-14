<div class="sidebar-menu">
    <header class="logo-env">

        <!-- logo -->
        <div class="logo" style="">
            <a href="<?php echo base_url(); ?>">
                <img src="<?php echo base_url('uploads/logo.png'); ?>" style="max-height:60px;" />
            </a>
        </div>

        <!-- logo collapse icon -->
        <div class="sidebar-collapse" style="">
            <a href="#" class="sidebar-collapse-icon with-animation">

                <i class="entypo-menu"></i>
            </a>
        </div>

        <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
        <div class="sidebar-mobile-menu visible-xs">
            <a href="#" class="with-animation">
                <i class="entypo-menu"></i>
            </a>
        </div>
    </header>

    <div style="border-top:1px solid rgba(69, 74, 84, 0.7);"></div>
    <ul id="main-menu" class="">
        <!-- add class "multiple-expanded" to allow multiple submenus to open -->
        <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->


        <!-- DASHBOARD -->
        <li class="<?php if ($page_name == 'dashboard') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>ifms.php/partner/dashboard">
                <i class="entypo-gauge"></i>
                <span><?php echo get_phrase('dashboard'); ?></span>
            </a>
        </li>

        <li class="<?php
                    if (
                        $page_name == 'new_voucher' ||
                        $page_name == 'new_budget_item' ||
                        $page_name == 'funds_transfer'
                    )
                        echo 'opened active has-sub';
                    ?> ">

            <a href="#">
                <i class="entypo-list-add"></i>
                <span><?php echo get_phrase('create_item'); ?></span>
            </a>
            <ul>

                <li class="<?php if ($page_name == 'new_voucher') echo 'active'; ?> ">
                    <a href="<?php echo base_url(); ?>ifms.php/partner/new_voucher">
                        <span><i class="fa fa-list-alt" aria-hidden="true"></i> <?php echo get_phrase('new_voucher'); ?></span>
                    </a>
                </li>

                <li class="<?php if ($page_name == 'new_budget_item') echo 'active'; ?> ">
                    <a href="<?php echo base_url(); ?>ifms.php/partner/new_budget_item">
                        <span><i class="fa fa-list-alt" aria-hidden="true"></i> <?php echo get_phrase('new_budget_item'); ?></span>
                    </a>
                </li>

                <li class="<?php if ($page_name == 'new_funds_transfer') echo 'active'; ?> ">
                    <a href="<?php echo base_url(); ?>ifms.php/partner/funds_transfer">
                        <span><i class="fa fa-list-alt" aria-hidden="true"></i> <?php echo get_phrase('new_funds_transfer'); ?></span>
                    </a>
                </li>

            </ul>

        </li>

        <!-- CASH JOURNAL -->
        <li class="<?php if ($page_name == 'cash_journal') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>ifms.php/partner/cash_journal">
                <i class="entypo-book"></i>
                <span><?php echo get_phrase('cash_journal'); ?></span>
            </a>
        </li>

        <!-- Financial Report -->
        <!-- <li class="<?php if ($page_name == 'financial_report') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>ifms.php/partner/financial_report">
                <i class="entypo-chart-pie"></i>
                <span><?php echo get_phrase('financial_report'); ?></span>
            </a>
        </li> -->


        <li class="<?php
                    if (
                        $page_name == 'budget_limits' ||
                        $page_name == 'budget_summary' ||
                        $page_name == 'budget_schedules'
                    )
                        echo 'opened active has-sub';
                    ?> ">
            <a href="#">
                <i class="entypo-picture"></i>
                <span><?php echo get_phrase('budget'); ?></span>
            </a>
            <ul>

                <!-- <li class="<?php if ($page_name == 'budget_limits') echo 'active'; ?> ">
                    <a href="<?php echo base_url(); ?>ifms.php/partner/budget_limits">
                        <span><i class="fa fa-tachometer" aria-hidden="true"></i> <?php echo get_phrase('budget_limits'); ?></span>
                    </a>
                </li> -->

                <li class="<?php if ($page_name == 'budget_summary') echo 'active'; ?> ">
                    <a href="<?php echo base_url(); ?>ifms.php/partner/budget_summary">
                        <span><i class="fa fa-list-alt" aria-hidden="true"></i> <?php echo get_phrase('budget_summary'); ?></span>
                    </a>
                </li>


                <li class="<?php if ($page_name == 'budget_schedules') echo 'active'; ?> ">
                    <a href="<?php echo base_url(); ?>ifms.php/partner/budget_schedules">
                        <span><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo get_phrase('budget_schedules'); ?></span>
                    </a>

                </li>


                <!-- <li class="<?php if ($page_name == 'plans') echo 'active'; ?> ">
                    <a href="<?php echo base_url(); ?>ifms.php/partner/plans">
                        <span><i class="fa fa-folder-open-o"></i> <?php echo get_phrase('complete_budget'); ?></span>
                    </a>

                </li> -->

            </ul>


        </li>


        <li class="<?php if ($page_name == 'civ_report') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>ifms.php/partner/civ_report">
                <i class="entypo-chart-pie"></i>
                <span><?php echo get_phrase('C.I.Vs'); ?></span>
            </a>
        </li>

        <li class="<?php if ($page_name == 'funds_transfer_requests') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>ifms.php/partner/list_funds_transfer_requests">
                <i class="fa fa-exchange"></i>
                <span><?php echo get_phrase('funds_transfer_requests'); ?></span>
            </a>
        </li>



    </ul>

</div>