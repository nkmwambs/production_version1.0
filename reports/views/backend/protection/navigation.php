<div class="sidebar-menu">
    <header class="logo-env" >
    	
        <!-- logo -->
        <div class="logo" style="">
            <a href="<?php echo base_url(); ?>">
                <img src="<?php echo base_url();?>uploads/logo.png"  style="max-height:60px;"/>
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
        <li class="<?php if ($page_name == 'external_links') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>resources.php/admin/external_links">
                <i class="fa fa-link"></i>
                <span><?php echo get_phrase('external_links'); ?></span>
            </a>
        </li>
        
      	
        <li class="<?php if ($page_name == 'documents') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>resources.php/admin/documents">
                <i class="fa fa-folder-open-o"></i>
                <span><?php echo get_phrase('documents'); ?></span>
            </a>
        </li>

          <!-- UDCT report -->
          <li class="<?php if ($page_name == 'dct_report') echo 'active'; ?> ">
            <a href="<?php echo base_url(); ?>reports.php/admin/dct_report">
                <i class="fa fa-list"></i>
                <span><?php echo get_phrase('DCT_expense_report'); ?></span>
            </a>
        </li>
       

    </ul>

</div>