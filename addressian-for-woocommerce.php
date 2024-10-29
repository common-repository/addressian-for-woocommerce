<?php
/*
Plugin Name: Addressian for Woocommerce
Description: UK address autocomplete and postcode finder for Woocommerce
WordPress admin interface
Version: 1.4
Author: Addressian
Author URI: https://addressian.co.uk
License: GPL2
WC requires at least: 2.0.0
WC tested up to: 8.8

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

include_once("include/requests.php");
include_once("include/admin.php");


function addressian_admin_scripts() {
   wp_enqueue_style('style-sa', plugin_dir_url(__FILE__) . 'admin/css/style.css');
   wp_enqueue_style('style-font-selector', plugin_dir_url(__FILE__) . 'admin/css/jquery.fontselect.css');
   wp_enqueue_style('wp-color-picker');
   //wp_enqueue_script( 'colorpicker');
   wp_enqueue_script('script-font-selector', plugin_dir_url(__FILE__) . 'admin/js/jquery.fontselect.js', array("jquery"), '1.0.0', true);
   wp_enqueue_script('script-name', plugin_dir_url(__FILE__) . 'admin/js/main-admin.js', array("jquery", "wp-color-picker", "script-font-selector"), '1.0.0', true);;
}
add_action('admin_enqueue_scripts', 'addressian_admin_scripts');

function addressian_scripts() {
   if (is_checkout() || is_cart()) {
      wp_enqueue_style('style-sa-front', plugin_dir_url(__FILE__) . 'public/css/style-front.css');
      wp_enqueue_style('style-typeahead-style', plugin_dir_url(__FILE__) . 'public/css/jquery.typeahead.min.css');
      $options = get_option('saddr_settings');
      if (isset($options['saddr_select_field_6']) && !empty($options['saddr_select_field_6']) && is_google_font($options['saddr_select_field_6']))
         wp_enqueue_style('style-font-selector', 'https://fonts.googleapis.com/css?family=' . $options['saddr_select_field_6']);
      wp_localize_script('ajax-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
      wp_enqueue_script('script-typeahead', plugin_dir_url(__FILE__) . 'public/js/jquery.typeahead.min.js', array("jquery"), '1.0.0', true);
      wp_enqueue_script('script-name', plugin_dir_url(__FILE__) . 'public/js/main.js', array("jquery", "script-typeahead"), '1.0.0', true);
      wp_enqueue_script('script-disableautofill', plugin_dir_url(__FILE__) . 'public/js/jquery.disableAutoFill.min.js', array("jquery"), '1.0.0', true);
   }
}
add_action('wp_enqueue_scripts', 'addressian_scripts');

function addressian_monitor_action() {
   global $wpdb;
   header('content-type: application/json');
   $options = get_option('saddr_settings');

   $phrase = sanitize_text_field($_GET['phrase']);
   $endpoint = "https://api.addressian.co.uk/v2";
   $api_key = $options['saddr_text_field_0'];
   if (empty($api_key)) {
      wp_die();
   }
   $request = new Addressian_Request($api_key, $endpoint);

   $user_address = sanitize_text_field($_GET['phrase']);
   $matched_addresses = $request->addressian_get_response(array(), "autocomplete", $user_address);
   $json_data = json_decode($matched_addresses);
   $all_data = array();
   $all_data2 = array();
   $all_data_json = array();
   if (is_array($json_data)) {
      foreach ($json_data as $data) {
         $address = $data->address;
         $county = isset($data->county) ? $data->county : "";
         $city = $data->city;
         $company = isset($data->company) ? $data->company : "";
         $postcode = $data->postcode;
         $data_label = array();
         if (!empty($company))
            $data_label [] = $company;
         if (!empty($address))
            $data_label [] = implode(", ", $address);
         if (!empty($address))
            $data_label [] = $postcode;
         if (!empty($city))
            $data_label [] = $city;
         if (!empty($county))
            $data_label [] = $county;
         $all_data[] = array("label" => implode(", ", $data_label), "data" => $data);
         $all_data2[] = implode(", ", $data_label);
         $all_data_json[] = $data;
      }
   }
   $sortArray = array();

   echo(json_encode(array("data" => array("country" => $all_data2, "data" => $all_data_json))));
   wp_die();
}
add_action('wp_ajax_addressian_monitor_action', 'addressian_monitor_action');
add_action('wp_ajax_nopriv_addressian_monitor_action', 'addressian_monitor_action');


add_filter('woocommerce_checkout_fields', 'addressian_custom_override_checkout_fields');
function addressian_custom_override_checkout_fields($fields) {
   if (!is_checkout()) {
      return $fields;
   }
   $options = get_option('saddr_settings');
   $strategy = $options['saddr_select_field_1'];
   if ($strategy == 1) {
      $fields['billing']['find_address'] = array('label' => __('Address Finder', 'woocommerce'), 'placeholder' => _x('Type an address in here', 'placeholder', 'woocommerce'), 'required' => false, 'class' => array('form-row-wide', 'js-typeahead'), 'clear' => true, 'priority' => 40, 'label_class' => array('fndadress'));
      $fields['shipping']['shipping_phone'] = array('label' => __('Address Finder', 'woocommerce'), 'placeholder' => _x('Type an address in here', 'placeholder', 'woocommerce'), 'required' => false, 'class' => array('form-row-wide'), 'clear' => true, 'priority' => 40, 'label_class' => array('fndadresssh'));
   } else if ($strategy == 2) {
      $fields['billing']['find_address_postcode'] = array('label' => __('Postcode Finder', 'woocommerce'), 'placeholder' => _x('Type a postcode', 'placeholder', 'woocommerce'), 'required' => false, 'class' => array('form-row-wide'), 'clear' => true, 'priority' => 40, 'label_class' => array('fndpostcode'));
      $fields['shipping']['shipping_phone'/*'shipping_phone'*/] = array('label' => __('Postcode Finder', 'woocommerce'), 'placeholder' => _x('Type a postcode', 'placeholder', 'woocommerce'), 'required' => false, 'class' => array('form-row-wide'), 'clear' => true, 'priority' => 40, 'label_class' => array('fndpostcodesh'));
   }
   return $fields;
}
function is_google_font($font) {
   $googleFonts = 'Abril+Fatface|Aclonica|Alfa+Slab+One|Allan|Amarante|Annie+Use+Your+Telescope|Anonymous+Pro|Allerta+Stencil|Allerta|Amaranth|Anton|Arbutus|Architects+Daughter|Archivo+Black|Arimo|Artifika|Arvo|Asset|Astloch|Audiowide|Bangers|Baumans|Bentham|Bevan|Bigshot+One|Black+Ops+One|Bowlby+One|Bowlby+One+SC|Brawler|Bubblegum+Sans|Buda:300|Butcherman|Butterfly+Kids|Cabin|Caesar+Dressing|Calligraffitti|Candal|Cantarell|Cardo|Carter+One|Caudex|Cedarville+Cursive|Changa+One|Cherry+Cream+Soda|Chewy|Coda|Codystar|Comfortaa|Coming+Soon|Copse|Corben|Cousine|Covered+By+Your+Grace|Crafty+Girls|Crimson+Text|Crushed|Cuprum|Damion|Dancing+Script|Dawning+of+a+New+Day|Days+One|Didact+Gothic|Diplomata|Droid+Sans|Droid+Serif|EB+Garamond|Ewert|Expletus+Sans|Faster+One|Fontdiner+Swanky|Forum|Francois+One|Fredoka+One|Fugaz+One|Glass+Antiqua|Geo|Give+You+Glory|Goblin+One|Gorditas|Goudy+Bookletter+1911|Gravitas+One|Gruppo|Hammersmith+One|Hanalei|Henny+Penny|Holtwood+One+SC|Homemade+Apple|Inconsolata|Indie+Flower|Irish+Grover|Istok+Web|Josefin+Sans|Josefin+Slab|Judson|Jura|Just+Another+Hand|Just+Me+Again+Down+Here|Kameron|Kenia|Kranky|Kreon|Kristi|La+Belle+Aurore|Lato|League+Script|Lekton|Limelight|Lobster|Lobster+Two|Lora|Love+Ya+Like+A+Sister|Loved+by+the+King|Luckiest+Guy|Maiden+Orange|Mako|Maven+Pro|Maven+Pro:900|Meddon|MedievalSharp|Megrim|Merriweather|Metrophobic|Michroma|Miltonian+Tattoo|Miltonian|Modern+Antiqua|Monofett|Molengo|Montserrat:300|Montserrat|Montserrat:700|Mountains+of+Christmas|Muli:300|Muli|Mystery+Quest|Neucha|Neuton|News+Cycle|Nixie+One|Nobile|Nova+Cut|Nova+Flat|Nova+Mono|Nova+Oval|Nova+Round|Nova+Script|Nova+Slim|Nova+Square|Nunito|Old+Standard+TT|Open+Sans:300|Open+Sans|Open+Sans:600|Open+Sans:800|Open+Sans+Condensed:300|Orbitron|Orbitron:500|Orbitron:700|Orbitron:900|Oswald|Over+the+Rainbow|Piedra|Prociono|Questrial|Reenie+Beanie|Pacifico|Patrick+Hand|Paytone+One|Permanent+Marker|Philosopher|Play|Playfair+Display|Podkova|Poiret+One|Press+Start+2P|Puritan|Quattrocento|Quattrocento+Sans|Racing+Sans+One|Radley|Raleway:100|Redressed|Ribeye|Ribeye+Marrow|Risque|Rock+Salt|Rokkitt|Ruslan+Display|Schoolbell|Shadows+Into+Light|Shanti|Sigmar+One|Six+Caps|Slackey|Smythe|Sniglet|Sniglet:800|Special+Elite|Stardos+Stencil|Sue+Elen+Francisco|Sunshiney|Swanky+and+Moo+Moo|Syncopate|Tangerine|Tenor+Sans|Terminal+Dosis+Light|The+Girl+Next+Door|Tinos|Ubuntu|Ultra|Unkempt|UnifrakturCook:bold|UnifrakturMaguntia|Varela|Varela+Round|Vast+Shadow|Vibur|Vollkorn|VT323|Waiting+for+the+Sunrise|Wallpoet|Walter+Turncoat|Wire+One|Yanone+Kaffeesatz|Yeseva+One|Zeyada';
   return in_array($font, explode("|", $googleFonts));
}
function addressian_footer_function() {
   if (!is_checkout()) {
      return;
   }
   global $wpdb;
   global $woocommerce;
   $options = get_option('saddr_settings');

   $ajax_admin = admin_url('admin-ajax.php');
   $strategy = $options['saddr_select_field_1'];
   if ($strategy == 1) {
      include_once("include/func-address.php");
      include_once("include/func-address-shipping.php");
   } else if ($strategy == 2) {
      include_once("include/func-postcode.php");
      include_once("include/func-postcode-shipping.php");
   }
   if ($woocommerce->customer->get_billing_country() != 'GB') {
      ?>
       <script>
           jQuery(document).ready(function ($) {
               $('#find_address_field').hide();
               //$('#shipping_phone_field').hide();
               //$('#find_address_postcode_field').hide();
               //$('#shipping_phone_field').hide();
               hide_billing_address_fields("show", "");
               //hide_shipping_address_fields("show", "");/**/
               hide_find_address_postcode('hide');
               $('.manually_address, .another_address').hide();
           });
       </script>
      <?php
   }
   if ($woocommerce->customer->get_shipping_country() != 'GB') {
      echo "";
      ?>
       <script>
           jQuery(document).ready(function ($) {
               $('#shipping_phone_field').hide();
               $('#shipping_phone_field').hide();
               //hide_billing_address_fields("show", "");
               hide_shipping_address_fields("show", "");/**/
               //hide_find_address_postcode('hide');
               $('.manually_address_shipping, .another_address').hide();
           });
       </script>
      <?php
   }
   ?>
    <script>
        jQuery(document).ready(function ($) {
            empty_billing_address_fields();
            empty_shipping_address_fields();
            $("#find_address, #find_address_postcode, #shipping_phone").val("");
            $("#billing_country").on("change", function (event) {
                jQuery('.emptypostcode').fadeOut("slow");
                $(".ntfound").hide();
                $(".ntfoundpostcode").hide();
                empty_billing_address_fields();
                var selected_country = $(this).val();
                if (selected_country != 'GB') {
                    $('#find_address_field').hide();
                    $('.postcode_addr').hide();
                    $('.manually_address, .another_address').hide();
                    hide_billing_address_fields("show", "");
                    hide_find_address_postcode('hide');
                    billing_fields.forEach(function (element) {
                        jQuery('#' + element).removeClass("add_hide");
                        jQuery('#' + element).addClass("add_show");
                    });
                    $("#postcode-dropdown").addClass("add_hide");
                    $("#postcode-dropdown").removeClass("add_show");

                } else {
                    $('#find_address_field').show();
                    $('#find_address_field label, #find_address_field input').fadeIn("slow");
                    $('.postcode_addr').show();
                    $(".descriptions a").text("Enter address manually");
                    $(".descriptions a").addClass("manually_address");
                    $(".descriptions a").removeClass("another_address");
                    $("#find_address").val("");
                    $("#find_address_postcode").val("");
                    $('.manually_address').show();
                    billing_fields.forEach(function (element) {
                        jQuery('#' + element).addClass("add_hide");
                        jQuery('#' + element).removeClass("add_show");
                    });
                    hide_find_address_postcode('show');

                }

            });
            $("#shipping_country").on("change", function (event) {
                var selected_country = $(this).val();
                $("#shipping_phone").val("");
                $(".ntfoundsh").hide();
                $(".ntfoundpostcodesh").hide();
                empty_shipping_address_fields();
                $("#postcode-dropdown-shipping").addClass("add_hide");
                $("#postcode-dropdown-shipping").removeClass("add_show");
                if (selected_country != 'GB') {
                    jQuery('#shipping_phone_field').addClass("add_hide");
                    jQuery('#shipping_phone_field').removeClass("add_show");
                    $('#shipping_phone_field').hide();
                    $('#shipping_phone_field').hide();
                    $('.postcode_addr_shipping').hide();
                    $('.manually_address_shipping').hide();
                    //$('#shipping_phone_field').hide();
                    hide_shipping_address_fields("show", "");
                } else {
                    jQuery('#shipping_phone_field').removeClass("add_hide");
                    jQuery('#shipping_phone_field').addClass("add_show");
                    $('#shipping_phone_field').show();
                    jQuery('#shipping_phone_field label, #shipping_phone_field input').fadeIn("slow");
                    $(".descriptions_shipping a").text("Enter address manually");
                    $(".descriptions_shipping a").addClass("manually_address_shipping");
                    $(".descriptions_shipping a").removeClass("another_address_shipping");
                    $("#shipping_phone").val("");
                    $("#shipping_phone").val("");
                    $('#shipping_phone_field').show();
                    $('.postcode_addr_shipping').show();
                    $('.manually_address_shipping').show();
                    hide_shipping_address_fields("hide", "");
                    hide_shipping_phone('show');

                }

            });
            let r_name = Math.random().toString(36).substring(7);
            $('#shipping_phone, #find_address_postcode, #find_address').attr('name', r_name);
        });
    </script>
   <?php
}
add_action('wp_footer', 'addressian_footer_function', 100);

?>