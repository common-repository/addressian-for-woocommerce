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
if (isset($options['saddr_text_field_2']))
   $maxNumberOfElements = $options['saddr_text_field_2'];
?>
<script>
    jQuery(document).ready(function ($) {
        //$(".fndpostcode").after('<span class ="ntfoundpostcode" style="color:red; display:None;">Incorrect postcode</span>');
        $("#find_address_postcode").attr("role", "presentation");
        $("#find_address_postcode").attr("autocomplete", "off");
        hide_billing_address_fields("hide", "find_address_postcode_field");
        $('<span  class="postcode_addr" style="float: left;margin-top: 1%;margin-bottom: 5%;"><button class="findaddr button alt" style="margin-bottom: 10px;">Find</button></span>').insertAfter("#find_address_postcode");
        $('.postcode_addr').append('<br /><span  class="descriptions" style=""><a href="#"  class="manually_address">Enter address manually </a></span>');
        $("#find_address_postcode").after('<span class ="ntfoundpostcode" style="color:red; display:None;">Incorrect postcode<br /></span>');
        $(".ntfoundpostcode").after('<span class ="emptypostcode" style="color:red; display:None;">Please enter a postcode<br /></span>');
        $('<select id="postcode-dropdown" style="display:none;"></select>').insertBefore(".descriptions");
        $(".postcode_addr").on("click", ".findaddr", function (event) {
            event.preventDefault();
            //$('#find_address_postcode').removeClass("loading_sa");
            if (jQuery("#find_address_postcode").val() == '') {
                jQuery('.emptypostcode').fadeIn("slow");
                return;
            }
            jQuery('.emptypostcode').fadeOut("slow");
            $('#find_address_postcode').addClass("loading_sa");
            if (!validateAddr(jQuery("#find_address_postcode").val())) {
                jQuery(".ntfoundpostcode").show();
                $('#find_address_postcode').removeClass("loading_sa");
                if (jQuery('#postcode-dropdown').is(":visible")) {
                    jQuery('#postcode-dropdown').fadeOut("slow");
                }
                return;
            }
            jQuery(".ntfoundpostcode").hide();
            let dropdown = jQuery('#postcode-dropdown');
            dropdown.empty();
            if (dropdown.is(":visible")) {
                dropdown.fadeOut("slow");
                dropdown.fadeIn("slow"); /*update*/
            }
            /*dropdown.addClass("add_show");
         dropdown.removeClass("add_hide");*/
            //$('#find_address_postcode').removeClass("loading_sa");
            dropdown.append('<option selected="true" disabled>Choose your address</option>');
            dropdown.prop('selectedIndex', 0);
            dropdown.change(function () {
                var selectedAddress = JSON.parse($(this).children("option:selected").val());
                var selectedItem = selectedAddress; //.data;
                var address_1 = selectedItem.address[0];
                var address_2 = "";
                if (selectedItem.address.length > 1) {
                    address_2 = selectedItem.address.slice(1).join(", ");//selectedItem.address[selectedItem.address.length - 1];
                }
                var city = selectedItem.hasOwnProperty('city') ? selectedItem.city : "";
                var county = selectedItem.hasOwnProperty('county') ? selectedItem.county : "";
                var postcode = selectedItem.hasOwnProperty('postcode') ? selectedItem.postcode : "";
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
                //hide_billing_address_fields("show", "");
                hide_find_address_postcode("hide");
                dropdown.fadeOut('slow');
                dropdown.addClass("add_hide");
                dropdown.removeClass("add_show");
                $("body").find(".findaddr").hide();
            });
            const url = '';
            const ajaxurl = "<?php echo $ajax_admin; ?>";
            jQuery.ajax({
                beforeSend: function (request) {
                },
                dataType: "json",
                url: ajaxurl + "?phrase=" + jQuery("#find_address_postcode").val() + "&format=json&action=addressian_monitor_action",
                success: function (data) {
                    //console.dir(data);
                    jQuery.each(data, function (key, entry) {
                        jQuery.each(entry.data, function (key2, entry2) {
                            var address_data = [];
                            if (entry2.hasOwnProperty('company'))
                                address_data.push(entry2.company);
                            address_data.push(entry2.address.join(', '));
                            if (entry2.hasOwnProperty('city'))
                                address_data.push(entry2.city);
                            if (entry2.hasOwnProperty('county'))
                                address_data.push(entry2.county);
                            if (entry2.hasOwnProperty('postcode'))
                                address_data.push(entry2.postcode);
                            dropdown.append(jQuery('<option></option>').attr('value', JSON.stringify(entry2)).text(address_data.join(", ")));
                        })
                    })
                    $('#find_address_postcode').removeClass("loading_sa");
                    dropdown.fadeIn("slow");
                    dropdown.removeClass("add_hide");
                }
            });
            jQuery.getJSON("", function (data) {
            });
        });
        $(".manually_address").on("click", function (event) {
            event.preventDefault();
            jQuery('.emptypostcode').fadeOut("slow");
            hide_billing_address_fields("show", "find_address_postcode_field");
            empty_billing_address_fields();
            $("#find_address_postcode").val("");
            var dropdown = $('#postcode-dropdown');
            dropdown.addClass("add_hide");
            dropdown.removeClass("add_show");
            if ($("#find_address_postcode").is(":visible")) {
                $(".descriptions a").text("Postcode Finder");
                //hide_billing_address_fields("show", "");
                //hide_find_address_postcode("hide");

                //dropdown.fadeOut('slow');
                //dropdown.addClass("add_hide");
                //dropdown.removeClass("add_show");
                //hide_billing_address_fields("show", "");
                hide_find_address_postcode("hide");
            } else {
                $(".descriptions a").text("Enter address manually");
                //hide_billing_address_fields("hide", "");
                //hide_find_address_postcode("show");

                //dropdown.fadeIn('slow');
                //dropdown.addClass("add_show");
                //dropdown.removeClass("add_hide");
                //hide_billing_address_fields("hide", "");
                hide_find_address_postcode("show");
            }
            jQuery(".ntfoundpostcode").hide();

        });
        $(".descriptions").on("click", '.another_address', function (event) {
            event.preventDefault();
            hide_billing_address_fields("hide", "find_address_postcode_field");

            $(".descriptions a").text("Enter address manually");
            $(".descriptions a").addClass("manually_address");
            $(".descriptions a").removeClass("another_address");
            $("#find_address_postcode").val("");
            //jQuery('#postcode-dropdown').fadeOut( "slow" );
            jQuery('#postcode-dropdown').addClass("add_hide");
            jQuery('#postcode-dropdown').removeClass("add_show");
            hide_find_address_postcode("show");
        });
        /*$( "#find_address_postcode" ).keypress(function() {
        $('#find_address_postcode').addClass("loading_sa");
     });
     $( "#find_address_postcode" ).keyup(function() {
        if($(this).val() === "")
           $('#find_address_postcode').removeClass("loading_sa");
       //alert( "Handler for .keyup() called." );
     });*/
        $("#find_address_postcode").keyup(function () {
            if ($(this).val().trim() === "") {
                $(".ntfoundpostcode").fadeOut('slow');
            }
            jQuery('.emptypostcode').fadeOut("slow");
        });
    });
</script>