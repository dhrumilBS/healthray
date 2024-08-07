// (function(history){
//     var replaceState = history.replaceState;
//     history.replaceState = function(state) {
//         if (typeof history.onreplacestate == "function") {
//             history.onreplacestate({state: state});
//         }
//         return replaceState.apply(history, arguments);
//     };
// })(window.history);
//
// window.onpopstate = history.onreplacestate = function(e) {
//     var domain = getDomainName()
//     console.log(e.state.path)
//     Cookies.set('handl_url', e.state.path, {
//         expires: parseInt(handl_utm_cookie_duration[0]),
//         domain: domain,
//         sameSite:'None',
//         secure: true }
//     );
// }

var qvars = getUrlVars()
var domain = getDomainName()

jQuery(function($) {
    if (handl_utm_cookie_duration[1] === '1' &&
        typeof(TVE) === "undefined" //thrive editor fix
    ){
        RunHandL()
    }
});


function RunHandL(){
    window.utms_js = {}
    window.gaNames = []

    //simulate referrer locally
    // Object.defineProperty(document, "referrer", {get : function(){ return "https://google.com"; }});

    SetRefLink('handlID', Math.floor(Math.random() * Date.now()), false, 0)

    GAClientID()
    setHandLParams()

    // console.log("after handl params")

    RunFieldFiller()

    jQuery.each(handl_utm_predefined, function( i,v ) {
        let cookie_name = v.name;
        let value = v.value

        if ( value.startsWith('[') && value.endsWith(']') ){ //it's a shortcode!
            value = v.value.replace(/^\[|\]$/g,'');
        }else{ //it is a hardcoded default value
            value = cookie_name
        }

        if ( ['_ga','gaclientid'].indexOf(value) > -1 ){
            gaNames.push(cookie_name)
        }

        if (cookie_name != ''){
            var cookie_field = GetQVars(value,qvars)

            if (cookie_field == ''){
                cookie_field = Cookies.get(value)
            }

            if ( cookie_field != '' && cookie_field != 'PANTHEON_STRIPPED' ){
                utms_js[cookie_name] = cookie_field
                SetRefLink(cookie_name, cookie_field, true, 0)
            }
        }
    })

    populateLinks()

    jQuery(document).on( 'nfFormReady', function() {
        form.fields.map(item => {
            if (item.default) {
                var matches = String(item.default).match(/^{(\w+):(\w+)/)
                if (matches && matches.length == 3) {
                    let key = matches[1]
                    let value = matches[2]

                    if (key === 'handl') {
                        jQuery('#nf-field-' + item.id).val(Cookies.get(value))
                    }
                }
            }
        })

        //hide hidden fields
        // jQuery('.nf-field-container.hidden-container').map( (i, item) => jQuery(item).parents().eq(4).hide())
    })
}

function RunFieldFiller(){

    Cookies.set('user_agent', navigator.userAgent, {domain: domain})

    jQuery.each(handl_utm_all_params, function( i,vi ) {
        var cookie_field = GetQVars(vi,qvars)

        var cookie_params = [vi]
        if ( handl_utm_first_touch_params.includes(vi) )
            cookie_params.push('first_'+vi)

        cookie_params.forEach( (v, ind) => {

            if ( cookie_field != '' && cookie_field != 'PANTHEON_STRIPPED' ){
                if ( v.startsWith('first_') && Cookies.get(v) != undefined ){
                            //do not update the first attributes...
                }else{
                    Cookies.set(v, cookie_field, {
                    expires: parseInt(handl_utm_cookie_duration[0]),
                    domain: domain,
                    sameSite:'None',
                    secure: true });
                }
            }

            var curval = decodeURI(Cookies.get(v))
            curval = curval != 'undefined' ? curval : ''
            if (curval != 'undefined') {
                utms_js[v] = curval
                curval = decodeURIComponent(curval)
                // curval = curval.replace(/[%]/g,' ')
                if (v == 'username') {
                    //Maybe this should apply to all... We'll see...
                    curval = curval.replace(/\+/g, ' ')
                }

                jQuery('input[name=\"'+v+'\"]').val(curval)
                jQuery('input#'+v).val(curval)
                jQuery('input.'+v).val(curval)
                jQuery('input#form-field-'+v).val(curval)

                //for nested input fix
                jQuery('#'+v).find('input').val(curval)
                jQuery('.'+v).find('input').val(curval)

                jQuery("[data-original_id='"+v+"']").val(curval)
                jQuery("[data-name='"+v+"']").val(curval)

                //wildcard selector
                jQuery("[name*="+v+"]").val(curval)
                jQuery("[id*="+v+"]").val(curval)
                jQuery("[class*="+v+"]").val(curval)

                jQuery("[class*="+v+"_out]").html(curval)
                jQuery("[id*="+v+"_out]").html(curval)

            }
        })
    });
}

function populateLinks(){
    jQuery('.utm-out-js, .utm-out-js a').each(function(){
        var merged_raw = jQuery.extend( {}, utms_js )
        var merged = Object.keys(merged_raw)
            .filter(key => handl_utm_append_params.includes(key))
            .reduce((obj, key) => {
                if (merged_raw[key]) {
                    obj[key] = decodeURIComponent(merged_raw[key]);
                }
                return obj;
            }, {});
        merged = jQuery.extend(merged, getSearchParams(this.href))
        var current_page = window.location.href.split('?')[0]
        if (
            this.href !== undefined &&
            this.href !== "" &&
            !this.href.match("#") &&
            !this.href.startsWith("tel:") &&
            !this.href.startsWith("mailto:") &&
            !this.href.startsWith("sms:") &&
            !jQuery(this).attr('onclick') &&
            this.href.match(new RegExp("^"+current_page+"$")) == null
        ){
            var href = this.href.split("?")[0];
            if ( !jQuery.isEmptyObject(merged) )
                this.href = href+"?"+decodeURIComponent(jQuery.param(merged))
        }
    })  ;

    // console.log(handl_utm)
    // console.log(utms_js)

    jQuery('.utm-out, .utm-out a').each(function(){
        var merged_raw = jQuery.extend( {}, handl_utm, utms_js )
        var merged = Object.keys(merged_raw)
            .filter(key => handl_utm_append_params.includes(key))
            .reduce((obj, key) => {
                if (merged_raw[key]) {
                    obj[key] = decodeURIComponent(merged_raw[key]);
                }
                return obj
            }, {});
        merged = jQuery.extend(merged, getSearchParams(this.href))
        var current_page = window.location.href.split('?')[0]
        if (
            this.href !== undefined &&
            this.href !== "" &&
            //             !this.href.match("#") &&
            !this.href.startsWith("tel:") &&
            !this.href.startsWith("mailto:") &&
            !this.href.startsWith("sms:") &&
            // !jQuery(this).attr('onclick') &&
            this.href.match(new RegExp("^"+current_page+"$")) == null
        ){
            var href = this.href.split("?")[0];
            var parts = []
            if (this.href.match("#")){
                parts = this.href.split("#")
                href = parts[0]
            }

            // console.log(parts, href)
            if ( !jQuery.isEmptyObject(merged) && !this.href.startsWith(current_page) ){
                if (this.href.match("#") && parts.length == 2){
                    this.href = href+"?"+decodeURIComponent(jQuery.param(merged))+"#"+parts[1].split("?")[0]
                }else{
                    this.href = href+"?"+decodeURIComponent(jQuery.param(merged))
                }
            }
        }
    });

    jQuery('.utm-src').each(function(){
        var target_url
        if (this.src){
            target_url = this.src
        }else if (jQuery(this).data('url')){
            target_url = jQuery(this).data('url')
        }

        var merged_raw = jQuery.extend( {}, handl_utm, utms_js )
        var merged = Object.keys(merged_raw)
            .filter(key => handl_utm_append_params.includes(key))
            .reduce((obj, key) => {
                if (merged_raw[key]) {
                    obj[key] = decodeURIComponent(merged_raw[key]);
                }
                return obj;
            }, {});
        merged = jQuery.extend( merged, getSearchParams(target_url) )

        var src = target_url.split("?")[0];
        if ( !jQuery.isEmptyObject(merged) ){
            var final_target = src + "?" + decodeURIComponent(jQuery.param(merged))
            if (this.src) {
                this.src = final_target
            }else if (jQuery(this).data('url')){
                jQuery(this).data('url', final_target)
            }
        }
    });
}

function getSearchParams(url,k){
    var p={};
    var a = document.createElement('a');
    a.href = url;
    a.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
    return k?p[k]:p;
}

function GetQVars(v,qvars){
    if (qvars[v] != undefined) {
        return qvars[v]
    }
    return ''
}

function getUrlVars() {
    var vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function GAClientID(){
    if ( typeof(ga) == 'function' && typeof(ga.getAll) == 'function'){
        var trackers = ga.getAll();
        if (trackers.length > 0 ){
            //maybe we'll update this later, so we can loop all the tracking ids...
            var entries = Object.entries({"clientId":"gaclientid","referrer":"organic_source"})
            var domain = getDomainName()
            for (const [gaName, handlName] of entries) {
                var gaValue = trackers[0].get(gaName)
                if (gaValue !== undefined) {
                    if (gaName != 'clientId'){ // we should reset the array for anythng other than clientId
                        gaNames = []
                    }

                    gaNames.push(handlName)

                    for (const curName of gaNames){
                        // console.log(curName, gaValue)
                        utms_js[curName] = gaValue
                        Cookies.set(curName, gaValue, {
                            expires: parseInt(handl_utm_cookie_duration[0]),
                            domain: domain,
                            sameSite:'None',
                            secure: true }
                        );
                        // console.log(`Setting ${handlName} as ${gaValue}`)
                        jQuery('input[name=\"' + curName + '\"]').val(gaValue)
                        jQuery('input#' + curName).val(gaValue)
                        jQuery('input.' + curName).val(gaValue)
                    }

                    // so we can include gaclientid to the links incase needed
                    populateLinks()
                }
            }
        }
    }else{
        setTimeout(GAClientID,500);
    }
}

function getDomainName(){
    var name="HandLtestDomainName"
    var value="HandLtestDomainValue"
    var host=location.host
    var domain;

    if (host.split('.').length === 1){
        domain = '';
    }else{
        var domainParts = host.split('.');
        domainParts.shift();
        domain = '.'+domainParts.join('.');
        Cookies.set(name, value, {domain: domain})
        if (Cookies.get(name) == null || Cookies.get(name) != value)
        {
            domain = '.'+host;
        }
    }

    return domain;
}

function setHandLParams(){
    // console.log("set handl params")

    SetRefLink('handl_url_base', document.location.origin + document.location.pathname, true, 0)
    SetRefLink('handl_url', document.location.href, true, 0)
    SetRefLink('handl_ref', document.referrer, true, 0)
    SetRefLink('handl_ref_domain', document.referrer == '' ? '' : this.get_url_domain(document.referrer), true, 0)
    SetRefLink('handl_landing_page', document.location.href, false, 0)
    SetRefLink('handl_original_ref', document.referrer, false, 0)

    let original_ref =  Cookies.get('handl_ref_domain')
    let this_domain = document.location.host
    let source = "Other";
    if (original_ref == '') {
        source = "Direct";
    }else if ( original_ref.match(/google/i) !== null ){
        source = "Google";
    }else if ( original_ref.match(/yahoo/i) !== null ){
        source = "Yahoo";
    }else if ( original_ref.match(/bing/i) !== null ){
        source = "Bing";
    }else if ( original_ref.match(/instagram/i) !== null ){
        source = "Instagram";
    }else if ( original_ref.match(/facebook/i) !== null || original_ref.match(/fb\.com$/i) !== null ){
        source = "Facebook";
    }else if ( original_ref.match(/twitter/i) !== null || original_ref.match(/t\.co$/i) !== null ){
        source = "Twitter";
    }else if ( original_ref.match(/snapchat/i) !== null ){
        source = "Snapchat";
    }else if ( original_ref.match(/youtube/i) !== null ){
        source = "YouTube";
    }else if ( original_ref.match(/pinterest/i) !== null ){
        source = "Pinterest";
    }else if ( original_ref.match(/linkedin/i) !== null ){
        source = "LinkedIn";
    }else if ( original_ref.match(/tumblr/i) !== null ){
        source = "Tumblr";
    }else if ( original_ref.match(/duckduckgo/i) !== null ){
        source = "Duckduckgo";
    }else if (this_domain == original_ref){
        source = "Internal";
    }

    if(
        ( !Cookies.get('organic_source_str') || Cookies.get('organic_source_str') == '' ) ||
        ( Cookies.get('organic_source_str') && Cookies.get('organic_source_str') != '' && ['Internal','Direct'].indexOf(source) == -1 )
    ){
        SetRefLink('organic_source', document.referrer, true, 0)
        SetRefLink('organic_source_str', source, true, 0)
    }


    var qvars = getUrlVars()
    let traffic_source = 'Other'
    if (
        Cookies.get('fbclid') != undefined ||
        Cookies.get('gclid') != undefined ||
        Cookies.get('msclkid') != undefined ||
        Object.keys(qvars).indexOf('fbclid') != -1 ||
        Object.keys(qvars).indexOf('gclid') != -1 ||
        Object.keys(qvars).indexOf('msclkid') != -1
    ){
        traffic_source = 'Paid'
    }else if ( ['Google','Bing','Yahoo','Duckduckgo'].indexOf(Cookies.get('organic_source_str')) > -1 ){
        traffic_source = 'Organic'
    }else if ( ['Facebook','Twitter','Instagram','Snapchat','YouTube','Pinterest','LinkedIn','Tumblr'].indexOf(Cookies.get('organic_source_str')) > -1 ){
        traffic_source = 'Social'
    }else if ( ['Internal','Direct'].indexOf(Cookies.get('organic_source_str')) > -1 ){
        traffic_source = 'Direct'
    }else if ( Cookies.get('organic_source_str') && ['Internal'].indexOf(Cookies.get('organic_source_str')) == -1 ){
        traffic_source = 'Referral'
    }

    SetRefLink('traffic_source', traffic_source, true, 0)
    SetRefLink('first_traffic_source', traffic_source, false, 0)

}

function SetRefLink(field, value, overwrite, count){
    // console.log(`Trying ${count} for ${field} ---> ${value}`)
    var domain = getDomainName()

    if (Cookies.get(field) != value) {
        if (Cookies.get(field) !== undefined && Cookies.get(field) != "" && !overwrite ){
            //bail...
            // console.log(`No need to update... ${field}`)
        }else if (Cookies.get(field) === undefined || Cookies.get(field) == '' || overwrite) {
            if (count == undefined) {
                count = 0
            }

            // utms_js[field] = value //not sure if we really need it here
            // console.log(`Setting cookies for ${field} as ${value}`)
            Cookies.set(field, value, {
                    expires: parseInt(handl_utm_cookie_duration[0]),
                    domain: domain,
                    sameSite: 'None',
                    secure: true
                }
            );

        } else {
            count = count + 1
            if (count < 3) {
                setTimeout(function () {
                    SetRefLink(field, value, overwrite, count)
                }, 500)
            }
        }
    }
}

function get_url_domain(url) {
    let a      = document.createElement('a');
    a.href = url;
    return a.hostname;
}

//This is for OptinMonster events: https://optinmonster.com/docs/optinmonster-javascript-events-api/
document.addEventListener('om.Campaign.afterShow',function(){
    RunFieldFiller()
})

window.addEventListener('CookiebotOnAccept', function (e) {
    if (Cookiebot.consent.marketing)
    {
        RunHandL()
    }
}, false);

document.addEventListener("cmplz_enable_category", function(consentData) {
    var category = consentData.detail.category;
    if (category==='marketing'){
        RunHandL()
    }
});


function afterBookingSelectDateAndTime(a){
    Object.keys(a.bookings[0].customFields).forEach(function(key) {
        let label = a.bookings[0].customFields[key].label
        if (Cookies.get(label)){
            a.bookings[0].customFields[key].value = Cookies.get(label)
        }
    });
}

window.ameliaActions = {
    beforeBooking: function(x,y,a){
        afterBookingSelectDateAndTime(a)
        x()
    }
}

/*
FormStack solution...
 */
function FF_AutoFiller(v){
    it = jQuery(v)

    for ( vv of ['placeholder', 'title'] ) {
        var ph = it.attr(vv)
        var n = handl_utm_all_params.findIndex( (p) => new RegExp(p,'i').test(ph) )
        if (n > -1){
            var cval = Cookies.get(handl_utm_all_params[n])
            it.val(cval).attr('value',cval)
        }
    }
}
setTimeout(function () {
    jQuery('.fsCell.fsHidden>input').map( (i, v) => { FF_AutoFiller(v)  })
}, 3000)

jQuery(document).on('wsf-rendered', function(event, form_object, form_id, instance_id) {
    RunFieldFiller()
});

// function FF_OnAfterRender(){
//     console.log("Form rendered successfully!");
//     // var vars = ["Lead.UTM_Campaign__c","Lead.UTM_Medium__c","Lead.UTM_Source__c","Lead.UTM_Term__c","Lead.UTM_Content__c","Lead.GCLID__c","Lead.FBclid__c"]
//     // for (v of vars){
//     //     escape_v = v.replace(/\./g, '\\.')
//     //     jQuery("#"+escape_v).val(Cookies.get(v));
//     // }
//     return true;
// }

// wp.hooks.addFilter('jet.fb.inputs','jet-plugins',function(x,y,z,t,i){console.log(x,y,z,t,i)})
// wp.hooks.addFilter('jet.fb.macro.field.value','jet-plugins',function(x,y,z,t,i){console.log(x,y,z,t,i)})
// wp.hooks.addAction('jet.fb.observe.after','jet-plugins',function(x,y,z,t,i){console.log(x,y,z,t,i)})

//This is to support Jet Form Builder
jQuery(document).on('jet-form-builder/after-init',function(e){
    RunFieldFiller()
})

setTimeout(() => { //jotform iframe embed support
    if (typeof(window.utms_js) == 'object' && typeof(iframeParams) == 'object'){
        var iframeParams = [];
        iframeParams.push( jQuery.param(window.utms_js) )
        var src = ifr.src;
        ifr.src = src + "?" + iframeParams.join('&');
    }
}, "500");