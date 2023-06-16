<div class="tab-content">
     <div id="staff" class="tab-pane fade in active">
          <div id="contacts">
               <div id="bottom-bar">
                    <button id="switchTheme">
                         <svg class="theme_icon" viewBox="0 0 24 24">
                              <path d="M19,3H14V5H19V18L14,12V21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M10,18H5L10,12M10,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H10V23H12V1H10V3Z" />
                         </svg>
                         <span>
                              <div class="dropdown" id="theme_options">
                                   <span class="dropbtn"><?php echo _l('chat_theme_name'); ?></span>
                                   <div class="dropdown-content">
                                        <a id="_light" href="#"><?php echo _l('chat_theme_options_light'); ?></a>
                                        <a id="_dark" href="#"><?php echo _l('chat_theme_options_dark'); ?></a>
                                   </div>
                              </div>
                         </span></button>
                    <button id="settings">
                         <svg class="theme_icon" viewBox="0 0 24 24">
                              <path d="M11 24H13V22H11V24M7 24H9V22H7V24M15 24H17V22H15V24M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.11 18 22 17.11 22 16V4C22 2.9 21.11 2 20 2M20 16H6L4 18V4H20" />
                         </svg><span><?= _l('settings'); ?></span></button>
               </div>
               <ul class="chat_contacts_list">
                    <li class="contact">
                         <!-- Contacts list -->
                    </li>
               </ul>
          </div>
     </div>
     <div id="groups" class="tab-pane fade">
          <div id="groups_container">
               <ul class="chat_groups_list">
               </ul>
               <div id="bottom-bar">
                    <button id="add_group_btn">
                         <svg viewBox="0 0 24 24">
                              <path d="M13 11A3 3 0 1 0 10 8A3 3 0 0 0 13 11M13 7A1 1 0 1 1 12 8A1 1 0 0 1 13 7M17.11 10.86A5 5 0 0 0 17.11 5.14A2.91 2.91 0 0 1 18 5A3 3 0 0 1 18 11A2.91 2.91 0 0 1 17.11 10.86M13 13C7 13 7 17 7 17V19H19V17S19 13 13 13M9 17C9 16.71 9.32 15 13 15C16.5 15 16.94 16.56 17 17M24 17V19H21V17A5.6 5.6 0 0 0 19.2 13.06C24 13.55 24 17 24 17M8 12H5V15H3V12H0V10H3V7H5V10H8Z" />
                         </svg>
                         <span><?= _l('chat_message_groups_text'); ?></span></button>
               </div>
          </div>
     </div>
     <?php if (isClientsEnabled()) : ?>
          <div id="crm_clients" class="tab-pane fade">
               <div id="clients_container">
                    <ul class="chat_clients_list">
                    </ul>
                    <div id="bottom-bar">
                         <button id="clients_show">
                              <svg viewBox="0 0 24 24">
                                   <path d="M11 9C11 10.66 9.66 12 8 12C6.34 12 5 10.66 5 9C5 7.34 6.34 6 8 6C9.66 6 11 7.34 11 9M14 20H2V18C2 15.79 4.69 14 8 14C11.31 14 14 15.79 14 18M7 9C7 9.55 7.45 10 8 10C8.55 10 9 9.55 9 9C9 8.45 8.55 8 8 8C7.45 8 7 8.45 7 9M4 18H12C12 16.9 10.21 16 8 16C5.79 16 4 16.9 4 18M22 12V14H13V12M22 8V10H13V8M22 4V6H13V4Z" />
                              </svg>
                              <span><?= _l('chat_lang_show_clients'); ?></span></button>
                         <div class="clients_settings dropup">
                              <button class="btn btn-default dropdown-toggle" type="button" id="dropDownOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                   <svg class="c_settings" data-toggle="tooltip" title="<?= _l('chat_clients_bottom_options'); ?>" viewBox="0 0 24 24">
                                        <path d="M10.04,20.4H7.12C6.19,20.4 5.3,20 4.64,19.36C4,18.7 3.6,17.81 3.6,16.88V7.12C3.6,6.19 4,5.3 4.64,4.64C5.3,4 6.19,3.62 7.12,3.62H10.04V20.4M7.12,2A5.12,5.12 0 0,0 2,7.12V16.88C2,19.71 4.29,22 7.12,22H11.65V2H7.12M5.11,8C5.11,9.04 5.95,9.88 7,9.88C8.03,9.88 8.87,9.04 8.87,8C8.87,6.96 8.03,6.12 7,6.12C5.95,6.12 5.11,6.96 5.11,8M17.61,11C18.72,11 19.62,11.89 19.62,13C19.62,14.12 18.72,15 17.61,15C16.5,15 15.58,14.12 15.58,13C15.58,11.89 16.5,11 17.61,11M16.88,22A5.12,5.12 0 0,0 22,16.88V7.12C22,4.29 19.71,2 16.88,2H13.65V22H16.88Z" />
                                   </svg>
                              </button>
                              <ul class="dropdown-menu animated fadeIn" aria-labelledby="dropDownOptions">
                                   <li><a href="javascript:void(0)" id="showOnlineContacts"><i class="fa fa-circle" aria-hidden="true"></i><?= _l('chat_only_online_clients'); ?></a></li>
                                   <li><a href="javascript:void(0)" id="resetContacts"><i class="fa fa-refresh" aria-hidden="true"></i><?= _l('chat_reload_clients_list'); ?></a></li>
                              </ul>
                         </div>
                    </div>
               </div>
          </div>
     <?php endif; ?>
</div>