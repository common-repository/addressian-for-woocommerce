var billing_fields = ["billing_address_1_field", "billing_address_2_field", "billing_city_field", "billing_state_field", "billing_postcode_field", "billing_company_field"]
var shipping_fields = ["shipping_address_1_field", "shipping_address_2_field", "shipping_city_field", "shipping_state_field", "shipping_postcode_field", "shipping_company_field"]

function hide_billing_address_fields(action, ignore = "") {
    billing_fields.forEach(function (element) {
        if (element === ignore)
            return;
        //console.log(action);
        if (action === 'show') {
            jQuery('#' + element).fadeIn("slow");//.show();
            jQuery('#' + element).removeClass("add_hide");
            jQuery('#' + element).addClass("add_show");
        } else if (action === 'hide') {
            //console.log('hide: '+'#'+element)
            jQuery('#' + element).fadeOut("slow");//.hide();
            jQuery('#' + element).addClass("add_hide");
            jQuery('#' + element).removeClass("add_show");
        }
    });


}

function hide_shipping_address_fields(action, ignore = "") {
    shipping_fields.forEach(function (element) {
        if (element === ignore)
            return;

        if (action === 'show') {
            //console.log("show shipping");
            jQuery('#' + element).fadeIn("slow");//.show();
            jQuery('#' + element).removeClass("add_hide");
            jQuery('#' + element).addClass("add_show");
        } else if (action === 'hide') {
            jQuery('#' + element).hide();
            jQuery('#' + element).addClass("add_hide");
            jQuery('#' + element).removeClass("add_show");
        }
    });


}

function hide_find_address(action) {

    if (action === 'show') {
        jQuery('#find_address_field label, #find_address_field input').fadeIn('slow', function () {
            hide_billing_address_fields("hide", "");
        })
    } else if (action === 'hide') {
        jQuery('#find_address_field label, #find_address_field input').fadeOut('slow', function () {
            hide_billing_address_fields("show", "");
        })
    }


}

function hide_shipping_phone(action) {
    /*
    if(action === 'show')
        jQuery('#shipping_phone_field label, #shipping_phone_field input').fadeIn( "slow" );//.show();
    else if(action === 'hide')
        jQuery('#shipping_phone_field label, #shipping_phone_field input').fadeOut( "slow" );//.hide();
    */
    if (action === 'show') {
        jQuery('#shipping_phone_field label, #shipping_phone_field input').fadeIn('slow', function () {
            hide_shipping_address_fields("hide", "");
        })
    } else if (action === 'hide') {
        jQuery('#shipping_phone_field label, #shipping_phone_field input').fadeOut('slow', function () {
            hide_shipping_address_fields("show", "");
        })
    }
}

function hide_find_address_postcode(action) {

    if (action === 'show') {
        jQuery('#find_address_postcode_field label, #find_address_postcode_field input, #find_address_field select, .findaddr').fadeIn('slow', function () {
            hide_billing_address_fields("hide", "");
        })
    } else if (action === 'hide') {
        jQuery('#find_address_postcode_field label, #find_address_postcode_field input, #find_address_field select, .findaddr').fadeOut('slow', function () {
            hide_billing_address_fields("show", "");
        })
    }
}

function hide_shipping_phone(action) {
    if (action === 'show') {
        jQuery('#shipping_phone_field label, #shipping_phone_field input, #shipping_phone_field select, .findaddr_shipping').fadeIn('slow', function () {
            hide_shipping_address_fields("hide", "");
        })
    } else if (action === 'hide') {
        jQuery('#shipping_phone_field label, #shipping_phone_field input, #shipping_phone_field select, .findaddr_shipping').fadeOut('slow', function () {
            hide_shipping_address_fields("show", "");
        })
    }
}

function empty_billing_address_fields(ignore = "") {
    billing_fields.forEach(function (element) {
        if (element === ignore)
            return;
        jQuery('#' + element + " input").val("");

    });


}

function empty_shipping_address_fields(ignore = "") {
    shipping_fields.forEach(function (element) {
        if (element === ignore)
            return;
        jQuery('#' + element + " input").val("");
    });


}

var re = /^(([gG][iI][rR] {0,}0[aA]{2})|((([a-pr-uwyzA-PR-UWYZ][a-hk-yA-HK-Y]?[0-9][0-9]?)|(([a-pr-uwyzA-PR-UWYZ][0-9][a-hjkstuwA-HJKSTUW])|([a-pr-uwyzA-PR-UWYZ][a-hk-yA-HK-Y][0-9][abehmnprv-yABEHMNPRV-Y])))( |%20){0,}[0-9][abd-hjlnp-uw-zABD-HJLNP-UW-Z]{2}))$/;

function validateAddr(address) {
    var OK = re.exec(address);
    if (!OK) {
        //window.alert(' Incorrect postcode format: '+address);
        //jQuery(".ntfoundpostcode").show();
        return false;
    } else {
        //jQuery(".ntfoundpostcode").hide();
        return true;
    }
}  