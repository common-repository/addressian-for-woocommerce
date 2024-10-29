<?php
/*
Addressian for Woocommerce is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Addressian for Woocommerce is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Addressian for Woocommerce. If not, see {License URI}.
*/

add_action('admin_menu', 'addressian_add_admin_menu');
add_action('admin_init', 'addressian_settings_init');


function addressian_add_admin_menu() {

   add_menu_page('Addressian', 'Addressian', 'manage_options', 'addressian', 'addressian_options_page', plugin_dir_url(__FILE__) . '../admin/images/favicon-gray-16x16.png');

}


function addressian_settings_init() {

   register_setting('pluginPage', 'saddr_settings');

   add_settings_section('saddr_pluginPage_section', __('', 'saddr'), 'addressian_settings_section_callback', 'pluginPage');

   add_settings_field('saddr_text_field_0', __('API Token: ', 'saddr'), 'addressian_text_field_0_render', 'pluginPage', 'saddr_pluginPage_section');

   add_settings_field('saddr_select_field_1', __('Strategy', 'saddr'), 'addressian_select_field_1_render', 'pluginPage', 'saddr_pluginPage_section');

   add_settings_field('saddr_text_field_2', __('Suggestion box height(px)', 'saddr'), 'addressian_text_field_2_render', 'pluginPage', 'saddr_pluginPage_section', array('class' => 'maxselector_label'));

   /*add_settings_field(
     'saddr_select_field_3',
     __( 'Theme', 'saddr' ),
     'addressian_select_field_3_render',
     'pluginPage',
     'saddr_pluginPage_section'
  );*/


   add_settings_field('saddr_select_field_4', __('dropdown Background', 'saddr'), 'addressian_text_field_4_render', 'pluginPage', 'saddr_pluginPage_section', array('class' => 'bg_selector'));

   add_settings_field('saddr_select_field_5', __('Item hover colour', 'saddr'), 'addressian_text_field_5_render', 'pluginPage', 'saddr_pluginPage_section', array('class' => 'hv_selector'));
   add_settings_field('saddr_select_field_6', __('Font', 'saddr'), 'addressian_select_field_6_render', 'pluginPage', 'saddr_pluginPage_section', array('class' => 'font_selector'));
   add_settings_field('saddr_select_field_7', __('Font Size(px)', 'saddr'), 'addressian_select_field_7_render', 'pluginPage', 'saddr_pluginPage_section', array('class' => 'size_selector'));
}


function addressian_text_field_0_render() {

   $options = get_option('saddr_settings');
   ?>
    <input type='text' class="saddress" name='saddr_settings[saddr_text_field_0]'
           value='<?php echo $options['saddr_text_field_0']; ?>'><br/>
    <span class="descriptions" style=""><a href="https://addressian.co.uk/#pricing-plans" class="" target="_link">Purchase an API token key</a></span>
   <?php

}


function addressian_select_field_1_render() {

   $options = get_option('saddr_settings');
   ?>
    <select class="saddress" id="strategy" name='saddr_settings[saddr_select_field_1]'>
        <option value='1' <?php selected($options['saddr_select_field_1'], 1); ?>>Address Autocomplete</option>
        <option value='2' <?php selected($options['saddr_select_field_1'], 2); ?>> Address finder by Postcode</option>
    </select>

   <?php

}


function addressian_text_field_2_render() {

   $options = get_option('saddr_settings');
   ?>
    <input type='number' id="maxheight" class="saddress" min="1" max="2000" name='saddr_settings[saddr_text_field_2]'
           value='<?php echo empty($options['saddr_text_field_2']) ? '' : $options['saddr_text_field_2']; ?>'><br/>
    <small><a href="" id="resmaxheight">Clear</a></small>
   <?php

}


function addressian_select_field_3_render() {

   $options = get_option('saddr_settings');
   ?>
    <select class="saddress" name='saddr_settings[saddr_select_field_3]'>
        <option value='blue-light' <?php selected($options['saddr_select_field_3'], 1); ?>>Blue Light</option>
        <option value='plate-dark' <?php selected($options['saddr_select_field_3'], 2); ?>>Plate Dark</option>
    </select>

   <?php

}

function addressian_text_field_4_render() {

   $options = get_option('saddr_settings');
   ?>
    <input type='text' class="saddress" id="color-picker" name='saddr_settings[saddr_text_field_4]'
           value='<?php echo empty($options['saddr_text_field_4']) ? '#fff' : $options['saddr_text_field_4']; ?>'>
   <?php

}

function addressian_text_field_5_render() {

   $options = get_option('saddr_settings');
   ?>
    <input type='text' class="saddress" id="color-picker-hover" name='saddr_settings[saddr_text_field_5]'
           value='<?php echo empty($options['saddr_text_field_5']) ? '#fff' : $options['saddr_text_field_5']; ?>'>
   <?php

}
function addressian_select_field_6_render() {

   $options = get_option('saddr_settings');
   ?>
    <input type='text' class="safont" id="safont" name='saddr_settings[saddr_select_field_6]'
           value='<?php echo empty($options['saddr_select_field_6']) ? '' : $options['saddr_select_field_6']; ?>'><br/>
    <small><a href="" id="resfont">Clear</a></small>

   <?php

}

function addressian_select_field_7_render() {

   $options = get_option('saddr_settings');
   ?>
    <input type='number' id="fontsizeselector" class="saddress" min="1" max="120"
           name='saddr_settings[saddr_text_field_7]'
           value='<?php echo empty($options['saddr_text_field_7']) ? '' : $options['saddr_text_field_7']; ?>'><br/>
    <small><a href="" id="resfontsize">Clear</a></small>

   <?php

}

function addressian_settings_section_callback() {

   //echo __( 'Setting ', 'saddr' );

}


function addressian_options_page() {

   ?>
    <img src="<?php echo plugin_dir_url(__FILE__) . '../admin/images/logo.png'; ?>" width="353" height="54"/>
    <hr/>
    <form action='options.php' method='post'>

        <h2><u>Settings</u></h2>

       <?php
       settings_fields('pluginPage');
       do_settings_sections('pluginPage');
       submit_button();
       ?>

    </form>
   <?php

}
add_action('admin_footer', 'addressian_admin_footer');
function addressian_admin_footer() {
   $options = get_option('saddr_settings');
   if ($options['saddr_select_field_1'] == 2) {

      ?>
       <script>
           jQuery(document).ready(function ($) {
               $(".maxselector_label").hide();
               $(".bg_selector").hide();
               $(".hv_selector").hide();
               $(".font_selector").hide();
               $(".size_selector").hide();
           });
       </script>
      <?php
   }

}