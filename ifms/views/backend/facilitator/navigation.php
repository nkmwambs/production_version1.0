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
            <a href="<?php echo base_url(); ?>ifms.php/facilitator/dashboard">
                <i class="entypo-gauge"></i>
                <span><?php echo get_phrase('dashboard'); ?></span>
            </a>
        </li>


        <li class="<?php if ($page_name == 'civ_report') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>ifms.php/facilitator/civ_report">
                <i class="entypo-chart-pie"></i>
                <span><?php echo get_phrase('C.I.Vs'); ?></span>
            </a>
        </li>

        <li class="<?php if ($page_name == 'funds_transfer_requests') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>ifms.php/facilitator/list_funds_transfer_requests">
                <i class="fa fa-exchange"></i>
                <span><?php echo get_phrase('funds_transfer_requests'); ?></span>
            </a>
        </li>


        <!-- CHART OF ACCOUNTS 
        <li class="<?php if ($page_name == 'dashboard') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>ifms.php/partner/finance_settings">
                <i class="entypo-gauge"></i>
                <span><?php echo get_phrase('accounts_chart'); ?></span>
            </a>
        </li>
        -->




    </ul>

</div>