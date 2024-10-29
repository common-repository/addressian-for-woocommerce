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

$ajax_admin = admin_url('admin-ajax.php');
$options = get_option('saddr_settings');
$maxNumberOfElements = 10;
$max_height = 250;
$bgcolor = "#fff";
$bgcolor_hover = "";
$font_size = "";
$font_family = '';
if (isset($options['saddr_text_field_2']))
   $maxNumberOfElements = $options['saddr_text_field_2'];
if (!empty($maxNumberOfElements))
   $max_height = $maxNumberOfElements; // * 30;
if (isset($options['saddr_text_field_4']))
   $bgcolor = $options['saddr_text_field_4'];
if (isset($options['saddr_text_field_5']))
   $bgcolor_hover = $options['saddr_text_field_5'];
if (isset($options['saddr_text_field_7']))
   $font_size = $options['saddr_text_field_7'];
if (isset($options['saddr_select_field_6'])) {
   $font_family = $options['saddr_select_field_6'];
   $font_family = str_replace("+", " ", $font_family);
}
?>
<script>
    var b_auto;
    jQuery(document).ready(function ($) {
        //$(".fndadresssh").after('<span class ="ntfoundsh" style="color:red; display:None;">Address Not Found</span>');
        $("#shipping_phone").attr("role", "presentation");
        $("#shipping_phone").attr("autocomplete", "new-password");
        $('<span  class="descriptions_shipping" style=""><a href="#"  class="manually_address_shipping">Enter address manually</a></span>').insertAfter("#shipping_phone");
        $(".manually_address_shipping").before('<span class ="ntfoundsh" style="color:red; display:None;">Address Not Found<br /></span>');
        hide_shipping_address_fields("hide", "");

        $(".manually_address_shipping").on("click", function (event) {
            event.preventDefault();
            $("#shipping_phone").val("");
            if ($("#shipping_phone").is(":visible")) {
                $(".descriptions_shipping a").text("Address Finder");
                //hide_shipping_address_fields("show", "");
                hide_shipping_phone("hide");
            } else {
                $(".descriptions_shipping a").text("Enter address manually");
                //hide_shipping_address_fields("hide", "");
                hide_shipping_phone("show");
            }
        });
        $(".descriptions_shipping").on("click", '.another_address_shipping', function (event) {
            event.preventDefault();
            hide_shipping_address_fields("hide", "");

            $(".descriptions_shipping a").text("Enter address manually");
            $(".descriptions_shipping a").addClass("manually_address_shipping");
            $(".descriptions_shipping a").removeClass("another_address_shipping");
            $("#shipping_phone").val("");
            hide_shipping_phone("show");
        });
        $("#shipping_country").on("change", function (event) {
            var selected_country = $(this).val();

        });
        $('.manually_address_shipping').on("click", function (event) {
            empty_shipping_address_fields();
        })

        var selected_address = {}
        $("#shipping_phone").keyup(function () {
            if ($(this).val().trim() === "") {
                $(".ntfoundsh").fadeOut('slow');
            }
        });

        var myMap2 = new Map();
        $("#shipping_phone").addClass('js-typeahead');
        $("#shipping_phone").wrap('<div class="typeahead__container">');
        $("#shipping_phone").wrap('<div class="typeahead__field">');
        $("#shipping_phone").wrap('<div class="typeahead__query">');
        $(".typeahead__field").append('<div class="typeahead__button"><button type="submit"><span class="typeahead__search-icon"></span></button></div>');
        $("#shipping_phone").typeahead({
            input: '#shipping_phone',
            order: "desc",
            delay: 400,
            dynamic: true,
            source: {
                country: {
                    ajax: {
                        display: "address",
                        type: "GET",
                        data: {
                            phrase: "{{query}}",
                            format: "json",
                            action: "addressian_monitor_action"
                        },
                        url: "<?php echo $ajax_admin; ?>",
                        path: "data.country",
                        callback: {
                            done: function (data) {
                                for (var i = 0; i < data.data.country.length; i++) {
                                    user_data.push({allo: data.data.data[i]})
                                    myMap2.set(data.data.country[i], data.data.data[i])
                                }
                                return data;
                            }
                        }
                    }
                }
            },
            maxItem: 0,
            cancelButton: false,
            minLength: 1,
            backdropOnFocus: true,
            order: "asc",
            filter: false,
            callback: {
                onInit: function (node) {
                    //console.log('Typeahead Initiated on ' + node.selector);
                },
                onClickAfter: function (node, a, item, event) {

                    selectedItem = myMap2.get(item.display)
                    var address_1 = selectedItem.address[0];
                    var address_2 = "";
                    if (selectedItem.address.length > 1) {
                        address_2 = selectedItem.address.slice(1).join(", ");
                    }
                    var city = selectedItem.city
                    var county = selectedItem.county
                    var postcode = selectedItem.postcode
                    var company = selectedItem.hasOwnProperty('company') ? selectedItem.company : "";
                    $("#shipping_city").val(city).trigger("change");
                    $("#shipping_state").val(county).trigger("change");
                    $("#shipping_postcode").val(postcode).trigger("change");
                    $("#shipping_company").val(company).trigger("change");
                    $("#shipping_address_2").val(address_2).trigger("change");
                    $("#shipping_address_1").val(address_1).trigger("change");
                    $('body').trigger('update_checkout');

                    $(".descriptions_shipping a").text("Choose another address");
                    $(".descriptions_shipping a").removeClass("manually_address");
                    $(".descriptions_shipping a").addClass("another_address");
                    hide_shipping_phone("hide");

                },
                onReceiveRequest: function (node, query) {
                },
                onResult: function (node, query, result, resultCount) {
                    if (query === "") return;
                    if (resultCount <= 0) {
                        $(".ntfoundsh").fadeIn('slow');
                    } else {
                        $(".ntfoundsh").fadeOut('slow');
                    }
                }
            },
        });
    });
</script>
<?php
if (!empty($bgcolor) && $bgcolor != '#fff' && $bgcolor != '#ffffff') {

   ?>
    <!--style>
		.ui-widget.ui-widget-content {
			border: 2px solid #000;
			background: <?php echo $bgcolor; ?>;
		}	
	</style-->
   <?php
}
?>
<?php
if (!empty($bgcolor_hover) && $bgcolor_hover != '#fff' && $bgcolor_hover != '#ffffff') {

   ?>
    <!--style>
		.ui-menu-item:hover{
			background: <?php echo $bgcolor_hover; ?>;
		}	
		.ui-menu-item .ui-menu-item-wrapper:hover{
			background: <?php echo $bgcolor_hover; ?>;
		}					
	</style-->
   <?php
}
?>
<!--style>
		.ui-menu-item{
			font-size:<?php echo $font_size; ?>px;
		}	
		.ui-autocomplete {
					max-height: <?php echo $max_height; ?>px;
					overflow-y: auto;
					overflow-x: hidden;
					padding-right: 20px;
		} 
	  * html .ui-autocomplete {
		height: 100px;
	  }			
	</style--> 