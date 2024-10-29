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
$bgcolor = "";
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
        //$("#find_address").after('<span class ="ntfound" style="color:red; display:None;">Address Not Found</span>');
        $("#find_address").attr("role", "presentation");
        $("#find_address").attr("autocomplete", "new-password");
        $('<span  class="descriptions" style=""><a href="#"  class="manually_address">Enter address manually</a></span>').insertAfter("#find_address");
        $(".manually_address").before('<span class ="ntfound" style="color:red; display:None;">Address Not Found<br /></span>');

        hide_billing_address_fields("hide", "");

        $(".manually_address").on("click", function (event) {
            event.preventDefault();
            if ($("#find_address").is(":visible")) {
                $(".descriptions a").text("Address Finder");
                //hide_billing_address_fields("show", "");
                hide_find_address("hide");
            } else {
                $(".descriptions a").text("Enter address manually");
                //hide_billing_address_fields("hide", "");
                hide_find_address("show");
            }
            $(".ntfound").fadeOut('slow');
        });
        $(".descriptions").on("click", '.another_address', function (event) {
            event.preventDefault();
            hide_billing_address_fields("hide", "");

            $(".descriptions a").text("Enter address manually");
            $(".descriptions a").addClass("manually_address");
            $(".descriptions a").removeClass("another_address");
            $("#find_address").val("");
            hide_find_address("show");
        });
        $("#billing_country").on("change", function (event) {
            var selected_country = $(this).val();

        });
        $('.manually_address').on("click", function (event) {
            empty_billing_address_fields();
        })

        var selected_address = {}
        $("form.checkout").attr("autocomplete", "off");
        $("#find_address").keyup(function () {
            if ($(this).val().trim() === "") {
                $(".ntfound").fadeOut('slow');
            }
        });

        user_data = []
        var myMap = new Map();
        $("#find_address").addClass('js-typeahead');
        $("#find_address").wrap('<div class="typeahead__container">');
        $("#find_address").wrap('<div class="typeahead__field">');
        $("#find_address").wrap('<div class="typeahead__query">');
        $(".typeahead__field").append('<div class="typeahead__button"><button type="submit"><span class="typeahead__search-icon"></span></button></div>');
        $("#find_address").typeahead({
            input: '#find_address',
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
                                    myMap.set(data.data.country[i], data.data.data[i])
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

                    //event.preventDefault();

                    selectedItem = myMap.get(item.display)
                    var address_1 = selectedItem.address[0];
                    var address_2 = "";
                    if (selectedItem.address.length > 1) {
                        address_2 = selectedItem.address.slice(1).join(", ");
                    }
                    var city = selectedItem.city
                    var county = selectedItem.county
                    var postcode = selectedItem.postcode
                    var company = selectedItem.hasOwnProperty('company') ? selectedItem.company : "";
                    $("#billing_city").val(city).trigger("change");
                    $("#billing_state").val(county).trigger("change");
                    $("#billing_postcode").val(postcode).trigger("change");
                    $("#billing_company").val(company).trigger("change");
                    $("#billing_address_2").val(address_2).trigger("change");
                    $("#billing_address_1").val(address_1).trigger("change");
                    $('body').trigger('update_checkout');

                    $(".descriptions a").text("Choose another address");
                    $(".descriptions a").removeClass("manually_address");
                    $(".descriptions a").addClass("another_address");
                    hide_find_address("hide");


                },
                onReceiveRequest: function (node, query) {
                },
                onResult: function (node, query, result, resultCount) {
                    if (query === "") return;
                    //console.dir(query);
                    if (result.length <= 0) {
                        $(".ntfound").fadeIn('slow');
                    } else {
                        $(".ntfound").fadeOut('slow');
                    }
                }
            },
        });

    });
</script>
<style>
    .ui-menu-item {
        width: 105%;
        word-wrap: break-word
    }

    .typeahead__container {
        font: unset;
        font-family: unset;
    }
</style>
<?php
if (!empty($bgcolor) && $bgcolor != '#fff' && $bgcolor != '#ffffff') {

   ?>
    <style>
        .ui-widget.ui-widget-content {
            border: 2px solid #000;
            background: <?php echo $bgcolor; ?>;
        }

        .woocommerce-checkout .typeahead__list {
            background-color: <?php echo $bgcolor; ?>;
        }
    </style>
   <?php
}
?>
<?php
if (!empty($bgcolor_hover) && $bgcolor_hover != '#fff' && $bgcolor_hover != '#ffffff') {

   ?>
    <style>
        .ui-menu-item:hover {
            background: <?php echo $bgcolor_hover; ?>;
            /*font-size:18px;
         font-family: "



        <?php echo $font_family; ?>    ";*/
        }

        .ui-menu-item .ui-menu-item-wrapper:hover {
            background: <?php echo $bgcolor_hover; ?>;
        }

        .ui-state-active,
        .ui-widget-content .ui-state-active,
        .ui-widget-header .ui-state-active,
        a.ui-button:active,
        .ui-button:active,
        .ui-button.ui-state-active:hover {
            background: <?php echo $bgcolor_hover; ?>;
        }

        .typeahead__dropdown .typeahead__dropdown-item:not([disabled]).active > a, .typeahead__dropdown .typeahead__dropdown-item:not([disabled]) > a:focus, .typeahead__dropdown .typeahead__dropdown-item:not([disabled]) > a:hover, .typeahead__list .typeahead__item:not([disabled]).active > a, .typeahead__list .typeahead__item:not([disabled]) > a:focus, .typeahead__list .typeahead__item:not([disabled]) > a:hover {
            background-color: <?php echo $bgcolor_hover; ?>;
        }
    </style>
   <?php
}
?>
<?php
if (!empty($font_family)) {

   ?>
    <style>
        .woocommerce-checkout .typeahead__list {
            font-family: "<?php echo $font_family; ?>";
        }
    </style>
   <?php
}
if (!empty($font_size)) {
   ?>
    <style>
        .woocommerce-checkout .typeahead__list {
            font-size: <?php echo $font_size; ?>px;
        }
    </style>
   <?php
}
?>
<style>
    .ui-menu-item {
        font-size: <?php echo $font_size; ?>px;
        font-family: "<?php echo $font_family; ?>";
        font-weight: 400;
    }

    .ui-autocomplete {
        max-height: <?php echo $max_height; ?>px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
        padding-right: 20px;
    }

    * html .ui-autocomplete {
        height: 100px;
    }

    .woocommerce-checkout .typeahead__list {
        max-height: <?php echo $max_height; ?>px;
        overflow-y: auto;
        overflow-x: hidden;
        /*background-color: blue;*/
    }
</style>
	