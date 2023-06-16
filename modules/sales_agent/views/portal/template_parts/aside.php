
<aside id="menu" class="sidebar">
   <ul class="nav metis-menu" id="side-menu">
      <?php
         hooks()->do_action('sa_before_render_aside_menu');
         ?>
      
      <li class="menu-item-dashboard">
         <a href="<?php echo site_url('sales_agent/portal'); ?>" aria-expanded="false">
             <i class="fa fa-home menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('dashboard','', false); ?>
             </span>
         </a>
      </li>

      <li class="menu-item-programs">
         <a href="<?php echo site_url('sales_agent/portal/programs'); ?>" aria-expanded="false">
             <i class="fa fa-wrench menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('program','', false); ?>
             </span>
         </a>
      </li>
      <li class="menu-item-products-list">
         <a href="<?php echo site_url('sales_agent/portal/products_list'); ?>" aria-expanded="false">
             <i class="fa fa-cubes menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('products_list','', false); ?>
             </span>
         </a>
      </li>
      <li class="menu-item-my-customers">
         <a href="<?php echo site_url('sales_agent/portal/clients'); ?>" aria-expanded="false">
             <i class="fa fa-user-o menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('customers','', false); ?>
             </span>
         </a>
      </li>
      <li class="menu-item-purchase">
         <a href="#" aria-expanded="false">
             <i class="fa fa-shopping-cart menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('purchase','', false); ?>
             </span>
             <span class="fa arrow pleft5"></span>
         </a>
         <ul class="nav nav-second-level collapse" aria-expanded="false">
            <li class="sub-menu-item-purchase-orders">
                <a href="<?php echo site_url('sales_agent/portal/purchase_orders'); ?>">
                    <i class="fa fa-cart-plus menu-icon menu-icon"></i>
                    <span class="sub-menu-text">
                      <?php echo _l('purchase_orders'); ?>
                    </span>
                 </a>
            </li>

            <li class="sub-menu-item-purchase-contracts">
                <a href="<?php echo site_url('sales_agent/portal/purchase_contracts'); ?>">
                    <i class="fa fa-file menu-icon"></i>
                    <span class="sub-menu-text">
                      <?php echo _l('contracts'); ?>
                    </span>
                 </a>
            </li>


            <li class="sub-menu-item-purchase-debit">
                <a href="<?php echo site_url('sales_agent/portal/purchase_invoices'); ?>">
                    <i class="fa fa-clipboard menu-icon"></i>
                    <span class="sub-menu-text">
                      <?php echo _l('invoices'); ?>
                    </span>
                 </a>
            </li>

          </ul>
      </li> 

      <li class="menu-item-inventory">
         <a href="#" aria-expanded="false">
             <i class="fa fa-snowflake-o menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('sa_inventory','', false); ?>
             </span>
             <span class="fa arrow pleft5"></span>
         </a>
         <ul class="nav nav-second-level collapse" aria-expanded="false">
            <li class="sub-menu-item-inventory-receiving">
                <a href="<?php echo site_url('sales_agent/portal/receiving_vouchers'); ?>">
                    <i class="fa fa-object-group menu-icon"></i>
                    <span class="sub-menu-text">
                      <?php echo _l('receiving_vouchers'); ?>
                    </span>
                 </a>
            </li>
            <li class="sub-menu-item-inventory-delivery">
                <a href="<?php echo site_url('sales_agent/portal/delivery_vouchers'); ?>">
                    <i class="fa fa-object-ungroup menu-icon"></i>
                    <span class="sub-menu-text">
                      <?php echo _l('delivery_vouchers'); ?>
                    </span>
                 </a>
            </li>

            <li class="sub-menu-item-inventory-warehouse">
                <a href="<?php echo site_url('sales_agent/portal/warehouse'); ?>">
                    <i class="fa fa-home menu-icon menu-icon"></i>
                    <span class="sub-menu-text">
                      <?php echo _l('sa_warehouse'); ?>
                    </span>
                 </a>
            </li>

            <li class="sub-menu-item-inventory-history">
                <a href="<?php echo site_url('sales_agent/portal/inventory_history'); ?>">
                    <i class="fa fa-calendar menu-icon menu-icon"></i>
                    <span class="sub-menu-text">
                      <?php echo _l('inventory_history'); ?>
                    </span>
                 </a>
            </li>
          </ul>
      </li>

      <li class="menu-item-sale">
         <a href="#" aria-expanded="false">
             <i class="fa fa-balance-scale menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('sa_sales','', false); ?>
             </span>
             <span class="fa arrow pleft5"></span>
         </a>
         <ul class="nav nav-second-level collapse" aria-expanded="false">
            <li class="sub-menu-item-sale-receiving">
                <a href="<?php echo site_url('sales_agent/portal/sale_invoices'); ?>">
                    <i class="fa fa-clipboard menu-icon"></i>
                    <span class="sub-menu-text">
                      <?php echo _l('sa_invoices'); ?>
                    </span>
                 </a>
            </li>

            <li class="sub-menu-item-sale-payments">
                <a href="<?php echo site_url('sales_agent/portal/payments'); ?>">
                    <i class="fa fa-file menu-icon"></i>
                    <span class="sub-menu-text">
                      <?php echo _l('sa_payments'); ?>
                    </span>
                 </a>
            </li>
          </ul>
      </li>

      <li class="menu-item-my-reports">
         <a href="<?php echo site_url('sales_agent/portal/reports'); ?>" aria-expanded="false">
             <i class="fa fa-bar-chart menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('reports','', false); ?>
             </span>
         </a>
      </li>

      <li class="menu-item-my-reports">
         <a href="<?php echo site_url('sales_agent/portal/tickets'); ?>" aria-expanded="false">
             <i class="fa fa-ticket menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('support','', false); ?>
             </span>
         </a>
      </li>


      <li class="menu-item-my-reports">
         <a href="<?php echo site_url('sales_agent/portal/settings'); ?>" aria-expanded="false">
             <i class="fa fa-cog menu-icon"></i>
             <span class="menu-text">
             <?php echo _l('settings','', false); ?>
             </span>
         </a>
      </li>
      <?php hooks()->do_action('sa_after_render_single_aside_menu'); ?>
   </ul>
</aside>