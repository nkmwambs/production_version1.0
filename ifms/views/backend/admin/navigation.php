<div class="sidebar-menu">
    <header class="logo-env" >

        <!-- logo -->
        <div class="logo" style="">
            <a href="<?php echo base_url(); ?>">
                <img src="<?php echo base_url("uploads/logo.png");?>"  class="img-circle" width="80"/>
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

    <div style=""></div>	
    <ul id="main-menu" class="">
        <!-- add class "multiple-expanded" to allow multiple submenus to open -->
        <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
		

        <!-- DASHBOARD -->
        <li class="<?php if ($page_name == 'dashboard') echo 'active'; ?> ">
            <a href="<?php echo base_url('ifms.php/admin/dashboard'); ?>">
                <i class="entypo-gauge"></i>
                <span><?php echo get_phrase('dashboard'); ?></span>
            </a>
        </li>
        
        
        <!-- FINANCE -->
        <li class="<?php
        		if (
	        			$page_name == 'opening_balances' ||
	                    $page_name == 'finance_settings'||
                        $page_name == 'budget_settings' ||
                        $page_name == 'dct_settings'
					)
                        echo 'opened active';
        ?> ">
            <a href="#">
                <i class="fa fa-money"></i>
                <span><?php echo get_phrase('finance'); ?></span>
            </a>
            <ul>
                <li class="<?php if ($page_name == 'opening_balances') echo 'active'; ?> ">
                    <a href="<?php echo base_url("ifms.php/admin/opening_balances"); ?>">
                        <span><i class="fa fa-bell-o"></i> <?php echo get_phrase('opening_balances'); ?></span>
                    </a>
                </li>

                <li class="<?php if ($page_name == 'finance_settings') echo 'active'; ?> ">
                    <a href="<?php echo base_url("ifms.php/admin/finance_settings"); ?>">
                        <span><i class="entypo-briefcase"></i> <?php echo get_phrase('finance_accounts_settings'); ?></span>
                    </a>
                </li> 
                
                <li class="<?php if ($page_name == 'budget_settings') echo 'active'; ?> ">
                    <a href="<?php echo base_url("ifms.php/admin/budget_settings"); ?>">
                        <span><i class="fa fa-adjust"></i> <?php echo get_phrase('budget_settings'); ?></span>
                    </a>
                </li> 

                <?php if($this->config->item('use_dct_detail_row')){?>
                
                <li class="<?php if ($page_name == 'dct_settings') echo 'active'; ?> ">
                    <a href="<?php echo base_url("ifms.php/dct/dct_settings"); ?>">
                        <span><i class="fa fa-flag"></i> <?php echo get_phrase('dct_settings'); ?></span>
                    </a>
                </li> 
                
                <?php }?>
             
            </ul>
        </li>
      
        <!-- NOTICEBOARD -->
        <li class="<?php if ($page_name == 'project_set_up') echo 'active'; ?> ">
            <a href="<?php echo base_url('ifms.php/admin/project_set_up'); ?>">
                <i class="fa fa-cog"></i>
                <span><?php echo get_phrase('project_set_up'); ?></span>
            </a>
        </li>

        <!-- MESSAGE 
        <li class="<?php if ($page_name == 'message') echo 'active'; ?> ">
            <a href="<?php echo base_url("ifms.php/admin/message"); ?>">
                <i class="entypo-mail"></i>
                <span><?php echo get_phrase('message'); ?></span>
            </a>
        </li>

        <!-- SETTINGS 
        <li class="<?php
        if ($page_name == 'system_settings' ||
                $page_name == 'manage_language' ||
                    $page_name == 'sms_settings'||
					$page_name == 'admin_test')
                        echo 'opened active';
        ?> ">
            <a href="#">
                <i class="entypo-lifebuoy"></i>
                <span><?php echo get_phrase('settings'); ?></span>
            </a>
            <ul>
                <li class="<?php if ($page_name == 'system_settings') echo 'active'; ?> ">
                    <a href="<?php echo base_url("ifms.php/admin/system_settings"); ?>">
                        <span><i class="entypo-cog"></i> <?php echo get_phrase('general_settings'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'sms_settings') echo 'active'; ?> ">
                    <a href="<?php echo base_url("ifms.php/admin/sms_settings"); ?>">
                        <span><i class="entypo-mobile"></i> <?php echo get_phrase('sms_settings'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'manage_language') echo 'active'; ?> ">
                    <a href="<?php echo base_url("ifms.php/admin/manage_language"); ?>">
                        <span><i class="entypo-language"></i> <?php echo get_phrase('language_settings'); ?></span>
                    </a>
                </li>

                <li class="<?php if ($page_name == 'admin_test') echo 'active'; ?> ">
                    <a href="<?php echo base_url("ifms.php/admin/admin_test"); ?>">
                        <span><i class="entypo-briefcase"></i> Admin Test</span>
                    </a>
                </li>               
            </ul>
        </li>

        <!-- ACCOUNT 
        <li class="<?php if ($page_name == 'manage_profile') echo 'active'; ?> ">
            <a href="<?php echo base_url("admin/admin/manage_profile"); ?>">
                <i class="entypo-lock"></i>
                <span><?php echo get_phrase('account'); ?></span>
            </a>
        </li>-->

    </ul>

</div>