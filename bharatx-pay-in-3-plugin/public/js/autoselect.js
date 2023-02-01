/**
 * Javascript code to autoselect BharatX Payment Option
 */


//preselects the bharatx option
setTimeout(()=>{
    console.log("selecting bharatx option");
    jQuery("#payment_method_bharatx-pay-in-3-feature-plugin").prop("checked", true).trigger("click");
}, 500);

