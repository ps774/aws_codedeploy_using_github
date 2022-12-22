var cart_id_set = false;
var last_cart_id = '';
var active_account = false;
var form_fields = '';
var page_view = false;
var cartupdated = false;
function load_others() {

	get_ip();
	get_events();
	get_form_fields();
	refresh_cart()
	// let buttons = document.getElementsByTagName("button")
	// for (let i = 0; i < buttons.length; i++) {
	// 	buttons[i].addEventListener("click",	refresh_cart)
	// }
	let inputs = document.getElementsByTagName("input")
	for (let i = 0; i < inputs.length; i++) {
		inputs[i].addEventListener("focusout", set_form_fields);
	}
}
var retry_cart = 0
if (typeof meta != 'undefined' && meta ) { 
	window.addEventListener("click", () => {
		if (retry_cart < 500 && cartupdated == false) {
			retry_cart = 0 
		} else if (cartupdated == false){
			retry_cart = 0
			meta_refresh_cart()
		}
	})

}
function refresh_cart(){
	// if (typeof getCookie('t1') == "undefined" || getCookie('t1') == "" || !getCookie('t1')){
	// 	if (typeof t1_token != "undefined" && t1_token != "" && t1_token){
	// 		ping("init", "token", t1_token)
	// 	}
	// }
	if (getCookie('cart') && getCookie('client_ip_address') && getCookie('client_user_agent') && getCookie('client_user_agent_full') && getCookie('t1') && active_account != false && cartupdated == false){
		cartupdated = true
		update_cart(getCookie('cart'), getCookie('client_ip_address'), getCookie('client_user_agent'), getCookie('client_user_agent_full'), getCookie('t1'))
	} else if (retry_cart < 6 && cartupdated == false && active_account != false){
		setTimeout(function(){
			retry_cart++
			refresh_cart()
		}, 500)
	} else if ( typeof meta != 'undefined' && meta && cartupdated == false && active_account != false) {
		retry_cart = 0
		meta_refresh_cart()
	} else {

	}
}
function meta_refresh_cart(){
	if (getCookie('cart') && getCookie('client_ip_address') && getCookie('client_user_agent') && getCookie('client_user_agent_full') && getCookie('t1') && active_account != false && cartupdated == false){
		cartupdated = true
		update_cart()
	} else if (retry_cart < 500 && cartupdated == false){
		setTimeout(function(){
			retry_cart++
			meta_refresh_cart()
		}, 700)
	} else {
	}
}
function set_form_fields(){
	setTimeout(function() {
		try {
			if (form_fields != undefined && form_fields != '' && form_fields){
				for (const key in form_fields){
					if(document.querySelector(form_fields[key]["field_selector"])){
						var v = document.querySelector(form_fields[key]["field_selector"]).value;
						// hash as an MD5, store MD5 in a cookie
						if( typeof v != 'undefined' && v != ''){
							var sha256_v = sha256(v);
							setCookie(form_fields[key].field_type, sha256_v, 604800000);
							//ping session with each data point
							session_sync(form_fields[key].field_type, sha256_v);
							//if phone + email are captured, ping capi with a AddPaymentInfo event
							var em = getCookie('em');
							var ph = getCookie('ph');
							var capi = getCookie('capi');
							if( typeof em != 'undefined' && em != '' && typeof ph != 'undefined' && ph != '' && typeof capi == 'undefined'){
								ping_capi("AddPaymentInfo", "0.00");
								setCookie('capi', 'sent', 604800000);
							}
						}
					}
				}
			}
		} catch (error) {
			
		}
	}, 300);
}


function update_cart(cart, client_ip_address, client_user_agent, client_user_agent_full, t1){
	var args = {};
	args.mode = 'cart_sync';
	args.sh_c = cart;
	args.t1 = t1;
	args.ip = client_ip_address;
	args.session_id = getCookie('pop6SID');
	args.account_id = getCookie('pop6AID');
	args.user_agent = client_user_agent;
	args.user_agent_full = client_user_agent_full;
	
	if (typeof(getCookie('fbp')) != "undefined" && getCookie('fbp') != '' && getCookie('fbp')){
		args.fbp = getCookie('fbp');
	} else if (typeof(getCookie('_fbp')) != "undefined" && getCookie('_fbp') != '' && getCookie('_fbp')){
		args.fbp = getCookie('_fbp');
	} else {
		args.fbp = undefined;
	}
	
	if (typeof(getCookie('fbc')) != "undefined" && getCookie('fbc') != '' && getCookie('fbc')){
		args.fbc = getCookie('fbc');
	} else if (typeof(getCookie('_fbc')) != "undefined" && getCookie('_fbc') != '' && getCookie('_fbc')){
		args.fbc = getCookie('_fbc');
	} else {
		args.fbc = undefined;
	}
	
	if(active_account){	
		var xmlHttp
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlHttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}					
		xmlHttp.onreadystatechange = function() {
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
				let data = JSON.parse(xmlHttp.response);
				if (data.response === "cart id sync success"){
				} else {
					retry_cart = 0
					cartupdated = false;
					refresh_cart()
				}
			}
		}
	
		let argString = new URLSearchParams(Object.entries(args)).toString()
		xmlHttp.open("POST", ping_url, true); 
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		xmlHttp.send(argString);
	}
				
}

function session_sync(the_key, the_value){
	var args = {};
	args.mode = 'session_sync';
	// if (typeof getCookie('t1') == "undefined" || getCookie('t1') == "" || !getCookie('t1')){
	// 	if (typeof t1_token != "undefined" && t1_token != "" && t1_token){
	// 		ping("init", "token", t1_token)
	// 	}
	// }
	args.t1 = getCookie('t1');
	args.account_id = getCookie('pop6AID');
	args.session_id = getCookie('pop6SID');
	args.passed_key = the_key;
	args.passed_value = the_value;
	if(active_account){	
		var xmlHttp
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlHttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}					
		xmlHttp.onreadystatechange = function() {
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
				let data = JSON.parse(xmlHttp.response);
			}
		}
	
		let argString = new URLSearchParams(Object.entries(args)).toString()
		xmlHttp.open("POST", ping_url, true); 
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		xmlHttp.send(argString);
	}
}

function close_msg(){
	var elem = document.getElementById('pop6-msg');
    return elem.parentNode.removeChild(elem);
}

function ping_capi(event_type = 0, event_value = 0, tag){
	var args = {};
	args.mode = 'ping_capi';
	// if (typeof getCookie('t1') == "undefined" || getCookie('t1') == "" || !getCookie('t1')){
	// 	if (typeof t1_token != "undefined" && t1_token != "" && t1_token){
	// 		ping("init", "token", t1_token)
	// 	}
	// }
	args.account_mode = getCookie('account_mode');
	args.t1 = getCookie('t1');
	args.t2 = getCookie('t2');
	args.t3 = getCookie('t3');
	args.t4 = getCookie('t4');
	
	if (typeof(getCookie('fbp')) != "undefined" && getCookie('fbp') != '' && getCookie('fbp')){
		args.fbp = getCookie('fbp');
	} else if (typeof(getCookie('_fbp')) != "undefined" && getCookie('_fbp') != '' && getCookie('_fbp')){
		args.fbp = getCookie('_fbp');
	} else {
		args.fbp = undefined;
	}
	
	if (typeof(getCookie('fbc')) != "undefined" && getCookie('fbc') != '' && getCookie('fbc')){
		args.fbc = getCookie('fbc');
	} else if (typeof(getCookie('_fbc')) != "undefined" && getCookie('_fbc') != '' && getCookie('_fbc')){
		args.fbc = getCookie('_fbc');
	} else {
		args.fbc = undefined;
	}
	
	
	args.em = getCookie('em');
	args.ph = getCookie('ph');
	args.fn = getCookie('fn');
	args.ln = getCookie('ln');
	args.shop = getCookie('shop');
	args.client_ip_address = getCookie('client_ip_address');
	args.client_user_agent = getCookie('client_user_agent_full')
	args.account_id = getCookie('pop6AID');
	args.session_id = getCookie('pop6SID');
	// args.client_user_agent = getCookie('client_user_agent').replace(/[^A-Za-z]/g, "");
	args.event_type = event_type;
	args.event_value = event_value;
	
	if( typeof meta != 'undefined' && meta){
		if( typeof meta.product != 'undefined' && meta.product){
			if( typeof meta.product.id != 'undefined' && meta.product.id){
				args.content_ids = [];
				args.content_ids.push(meta.product.id);
				//upgrade page view to content view
				if( args.event_type == 'PageView' ){
					args.event_type = 'ViewContent';
					event_type = args.event_type;
				}
			}
			if( typeof meta.product.variants != 'undefined' && meta.product.variants){
				var i = 0;
				if( meta.product.variants.length > 1 ){
					
					if( typeof document.querySelector('select[name="id"]') != 'undefined' && document.querySelector('select[name="id"]')){
						if( typeof document.querySelector('select[name="id"]').value != 'undefined' && document.querySelector('select[name="id"]').value){
							var selected_id = document.querySelector('select[name="id"]').value;
					
							meta.product.variants.forEach((the_data, index) => {
								if(  the_data.id == selected_id ){
									i = index;
								}
							})
						}
					}
					
					if( typeof document.querySelector('input[name="id"]') != 'undefined' && document.querySelector('input[name="id"]') ){
						if( typeof document.querySelector('input[name="id"]').value != 'undefined' && document.querySelector('input[name="id"]').value){
							var selected_id = document.querySelector('input[name="id"]').value;
					
							meta.product.variants.forEach((the_data, index) => {
								if(  the_data.id == selected_id ){
									i = index;
								}
							})
						}
					}
					
					
				}
				if( meta.product.variants[i].name != 'undefined' && meta.product.variants[i].name ){
					args.content_name = meta.product.variants[i].name;
				}
				
				if( meta.product.variants[i].price != 'undefined' && meta.product.variants[i].price ){
					
					if( event_type == 'AddToCart' || event_type == 'Purchase' || event_type == 'InitiateCheckout' || event_type == 'ViewContent'){
						args.event_value = meta.product.variants[i].price / 100.00;
						args.event_value = (Math.round(args.event_value * 100) / 100).toFixed(2);
						event_value = args.event_value;
						
					}
				}
			}
		}
		
		//check for Shopify.currency.active
		if( typeof Shopify != 'undefined' ){
			if( typeof Shopify.currency != 'undefined' ){
				if( typeof Shopify.currency.active != 'undefined' ){
					args.currency = Shopify.currency.active;
				}
			}
		}
	} else {
		if( typeof dataLayer != 'undefined' ){
			dataLayer.forEach(function(the_val, the_key){
				if( typeof the_val == 'object' ){
					if ('ecommerce' in the_val){
						if( typeof the_val.ecommerce != 'undefined' && the_val.ecommerce){
							if( typeof the_val.ecommerce.detail != 'undefined' ){
								if( typeof the_val.ecommerce.detail.products != 'undefined' ){
									if( typeof the_val.ecommerce.detail.products[0] != 'undefined' ){
										if( the_val.ecommerce.detail.products.length == 1 ){
											if( args.event_type == 'PageView' ){
												args.event_type = 'ViewContent';
												event_type = args.event_type;
											}
											if( typeof the_val.ecommerce.detail.products[0].name != 'undefined' ){	
												args.content_name = the_val.ecommerce.detail.products[0].name;
											}
											if( typeof the_val.ecommerce.detail.products[0].product_id != 'undefined' ){	
												args.content_ids = [];
												args.content_ids.push(the_val.ecommerce.detail.products[0].product_id);
											}
											if( typeof the_val.ecommerce.detail.products[0].price != 'undefined' ){	
												args.event_value = the_val.ecommerce.detail.products[0].price;
												event_value = args.event_value;
											}
										}
									}
								}
							}
						}
					}
				}
			});
		}
	}
	
	

	//test for woocommerce
	
	
	
	
	
	if( typeof document.getElementsByClassName('woocommerce')[0] != 'undefined'){		
		if( typeof document.querySelectorAll('.order-total')[0] != 'undefined' ){
			var the_total = document.querySelectorAll('.order-total')[0].innerText.replace(/[^0-9.]/g, '');			
			if( typeof the_total != 'undefined' ){
				if( args.event_type == 'Purchase' || args.event_type == 'InitiateCheckout' || args.event_type == 'AddPaymentInfo'){
					args.event_value = the_total;
					event_value = args.event_value;
					setCookie("CartTotal", the_total, 604800000);
				}
			}
		} else if( typeof document.querySelectorAll('.woocommerce-Price-amount')[0] != 'undefined' ) {
			var the_price = document.querySelectorAll('.woocommerce-Price-amount')[0].innerText.replace(/[^0-9.]/g, '');
			if( typeof the_price != 'undefined' ){
				if( args.event_type == 'AddToCart' || args.event_type == 'ViewContent'){
					args.event_value = the_price;
					event_value = args.event_value;
				}
			}
		}		
	} 
	
	if( typeof google_tag_manager != 'undefined'){
		if( typeof google_tag_manager['GTM-PN6839'] != 'undefined' && google_tag_manager['GTM-PN6839'] ){
			if( typeof google_tag_manager['GTM-PN6839'].dataLayer != 'undefined' && google_tag_manager['GTM-PN6839'].dataLayer ){
				
				var fgx_cart_total = google_tag_manager['GTM-PN6839'].dataLayer.get('frgxOrderTotal');
				if( typeof fgx_cart_total != 'undefined' && fgx_cart_total){			
					setCookie("CartTotal", fgx_cart_total,  604800000 );
				}
				
				
				if(  args.event_type == "AddToCart" ){
					var event_id = google_tag_manager['GTM-PN6839'].dataLayer.get('frgxFbAtcId');
				} else {
					var event_id = google_tag_manager['GTM-PN6839'].dataLayer.get('frgxEventId');
				}
				args.event_id = event_id;
			}
		} else if( typeof google_tag_manager['GTM-N4M8HQ'] != 'undefined' && google_tag_manager['GTM-N4M8HQ'] ){
			if( typeof google_tag_manager['GTM-N4M8HQ'].dataLayer != 'undefined' && google_tag_manager['GTM-N4M8HQ'].dataLayer ){
				
				var fgx_cart_total = google_tag_manager['GTM-N4M8HQ'].dataLayer.get('pcomOrderTotal');
				if( typeof fgx_cart_total != 'undefined' && fgx_cart_total){			
					setCookie("CartTotal", fgx_cart_total,  604800000 );
				}
				
				
				if(  args.event_type == "AddToCart" ){
					var event_id = google_tag_manager['GTM-N4M8HQ'].dataLayer.get('pcomFbAtcId');
				} else {
					var event_id = google_tag_manager['GTM-N4M8HQ'].dataLayer.get('pcomEventId');
				}
				args.event_id = event_id;
			}
		} else if (typeof google_tag_manager['GTM-PS7SL57'] != 'undefined' && google_tag_manager['GTM-PS7SL57'] ){
			if(  args.event_type == "AddToCart" ){
				// console.log(tag.id.replace("lnkAddToCart", "lblPrice"))
				tag.id = tag.id.replace("lnkAddToCart", "lblPrice")
				let tag_replace_value = document.getElementById(tag.id).innerText
				tag_replace_value = parseInt(tag_replace_value.replace("$", ""))
				args.event_value = tag_replace_value
				event_value = tag_replace_value
			}
		}
	}
	
	if( args.event_type == "Purchase" ){
		var cart_total = getCookie("CartTotal");
		if( typeof cart_total != 'undefined' ){
			if( parseInt(cart_total) > 1 ){
				args.event_value = cart_total;
				event_value = args.event_value;
			}			
			
		}
	}
	
	if( typeof args.t1 == 'undefined' || typeof args.t2 == 'undefined' || typeof args.t3 == 'undefined' || typeof args.t4 == 'undefined' ){
		return false;
	} else {
		if(active_account){	
			var xmlHttp
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlHttp=new XMLHttpRequest();
			}
			else {// code for IE6, IE5
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}					
			xmlHttp.onreadystatechange = function() {
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
					let data = JSON.parse(xmlHttp.response);
					if(typeof data.response != 'undefined'){
						if(parseInt(event_value) > 0 ){
						} else {
							event_value = '0.00';
						}
						
						if(typeof data.response.fbtrace_id != 'undefined'){
							if( args.account_mode == 'debug' ){
								if(console.log == 'User denied the request for Geolocation.'){

								} else {
									console.log(green_dot + pop6+event_type+' event logged with value: '+event_value);
								}
							} else {
								if(console.log == 'User denied the request for Geolocation.'){

								} 
							}
						}
					}
				}
			}
			let argString = new URLSearchParams(Object.entries(args)).toString()
			xmlHttp.open("POST", ping_url, true); 
			xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
			xmlHttp.send(argString); 
		}
	}
}

/*

on event, capture data and store it for future payload
 - hash as an MD5, store MD5 in a cookie
 - when sending a payload, check to see what's avaialble, and include what's there
	
*/

function get_form_fields(){
	//1. load the configured fields from the database
	var args = {};
	args.mode = 'get_form_fields';
	// if (typeof getCookie('t1') == "undefined" || getCookie('t1') == "" || !getCookie('t1')){
	// 	if (typeof t1_token != "undefined" && t1_token != "" && t1_token){
	// 		ping("init", "token", t1_token)
	// 	}
	// }
	args.t1 = getCookie('t1');
	args.account_id = getCookie('pop6AID');
	args.session_id = getCookie('pop6SID');

	if(active_account){	
		var xmlHttp
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlHttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}					
		xmlHttp.onreadystatechange = function() {
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
				let data = JSON.parse(xmlHttp.response);
				if( typeof data.form_fields != 'undefined' && data.form_fields ){
					form_fields = data.form_fields;
					if( getCookie('account_mode') == 'debug' ){
						if(console.log == 'User denied the request for Geolocation.'){

						} else {
							console.log(green_dot + pop6+'Form Fields loaded...');
						}
					}
						data.form_fields.forEach((the_data, index) => {
							if(document.querySelector(the_data.field_selector)){
								document.querySelector(the_data.field_selector).addEventListener("focusout", function(){
									var v = document.querySelector(the_data.field_selector).value;
									// hash as an MD5, store MD5 in a cookie
									if( typeof v != 'undefined' && v != ''){
										var sha256_v = sha256(v);
										setCookie(the_data.field_type, sha256_v, 604800000);
										//ping session with each data point
										session_sync(the_data.field_type, sha256_v);
										//if phone + email are captured, ping capi with a AddPaymentInfo event
										var em = getCookie('em');
										var ph = getCookie('ph');
										var capi = getCookie('capi');
									if( typeof em != 'undefined' && em != '' && typeof ph != 'undefined' && ph != '' && typeof capi == 'undefined'){
												 ping_capi("AddPaymentInfo", "0.00");
												 setCookie('capi', 'sent',  3000 );
											}
									}
								});	
	
							} else if (the_data.field_selector[0] != "#" && the_data.field_selector[0] != "." && document.querySelector(`input[name='${the_data.field_selector}']`)) {
								document.querySelector(`input[name='${the_data.field_selector}']`).addEventListener("focusout", function(){
									var v = document.querySelector(`input[name='${the_data.field_selector}']`).value;
									// hash as an MD5, store MD5 in a cookie
									if( typeof v != 'undefined' && v != ''){
										var sha256_v = sha256(v);
										setCookie(the_data.field_type, sha256_v, 604800000);
										//ping session with each data point
										session_sync(the_data.field_type, sha256_v);
										//if phone + email are captured, ping capi with a AddPaymentInfo event
										var em = getCookie('em');
										var ph = getCookie('ph');
										var capi = getCookie('capi');
									if( typeof em != 'undefined' && em != '' && typeof ph != 'undefined' && ph != '' && typeof capi == 'undefined'){
												 ping_capi("AddPaymentInfo", "0.00");
												 setCookie('capi', 'sent', 3000 );
											}
									}
								});	
							}
						})
				}
			}
		}
	
		let argString = new URLSearchParams(Object.entries(args)).toString()
		xmlHttp.open("POST", ping_url, true); 
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		xmlHttp.send(argString); 
	}
}


function get_events(){
	var args = {};
	args.mode = 'get_events';
	// if (typeof getCookie('t1') == "undefined" || getCookie('t1') == "" || !getCookie('t1')){
	// 	if (typeof t1_token != "undefined" && t1_token != "" && t1_token){
	// 		ping("init", "token", t1_token)
	// 	}
	// }
	args.t1 = getCookie('t1');
	args.account_id = getCookie('pop6AID');
	args.session_id = getCookie('pop6SID');

	var xmlHttp
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlHttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}	
	if(active_account){			
		xmlHttp.onreadystatechange = function() {
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
				if (page_view == false){
					ping_capi('PageView', '');
					page_view = true
				}
				let data = JSON.parse(xmlHttp.response);
				if( typeof data.events != 'undefined' && data.events ){
					if( getCookie('account_mode') == 'debug' ){
						if(console.log == 'User denied the request for Geolocation.'){

						} else {
							console.log(green_dot + pop6+'Events loaded...');
						}
					}
					data.events.forEach((the_data, index) => {
						if( the_data.event_trigger == 'click' ){
							if (the_data.selector_query.includes("=")){
								let multi_tag_data = document.querySelectorAll(the_data.selector_query)
								multi_tag_data.forEach(tag => {
									tag.addEventListener("click", () => {
										ping_capi(the_data.event_type, the_data.event_value, tag)
									});	

								})
								// for (const key in multi_tag_data) {
								// 	multi_tag_data[key].addEventListener("click", function(){
								// 		ping_capi(the_data.event_type, the_data.event_value);
								// 	});	
								// }
							} else if(document.querySelector(the_data.selector_query)){
									document.querySelector(the_data.selector_query).addEventListener("click", function(){
										ping_capi(the_data.event_type, the_data.event_value);
									});	
								}
						}
						if( the_data.event_trigger == 'url' ){
							var page = window.location.pathname;
							if( page.includes( the_data.selector_query ) ){
								ping_capi(the_data.event_type, the_data.event_value);
							} else if (the_data.selector_query.includes("**")){
								let cleanedSelectorQuery = the_data.selector_query.replace("**", "")
								if (page.endsWith(cleanedSelectorQuery)) {
									ping_capi(the_data.event_type, the_data.event_value);
								}
							}
						} else {
						}
					})
				} else {
				}
			}
		}
	
		let argString = new URLSearchParams(Object.entries(args)).toString()
		xmlHttp.open("POST", ping_url, true); 
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		xmlHttp.send(argString); 
	}
}

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

function setCookie(name, value, milliseconds) {
    var expires;
    if (milliseconds) {
        var date = new Date();
        date.setTime(date.getTime() + milliseconds);
        expires = "; expires=" + date.toGMTString();
    }
    else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

// var retry_ip = false
function get_ip(){
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlHttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	if(active_account){					
		xmlHttp.onreadystatechange = function() {
			
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
				//may need to console this out
				// alert(xmlHttp.responseText);
				let data = JSON.parse(xmlHttp.response);
				// if (typeof data.IPv4 == "undefined" && retry_ip == false || !data.IPv4 && retry_ip == false || data.IPv4 == "" && retry_ip == false){
				// 	retry_ip = true
				// 	get_ip()
				// }
				setCookie('client_ip_address', data.IPv4, 604800000);
				if( getCookie('account_mode') == 'debug' ){
					if(console.log == 'User denied the request for Geolocation.'){

					} else {
						console.log(green_dot + pop6 + 'Client IP recorded...');
					}
				}
			}
		}

		xmlHttp.open("POST", 'https://geolocation-db.com/json/', true);
		xmlHttp.send(); 
	}
}

function ping(mode = 'init', passed_key = 0, passed_value = 0){

// if (mode != 'init' && mode != 're_init'){
// 		if (typeof getCookie('t1') == "undefined" || getCookie('t1') == "" || !getCookie('t1')){
// 			if (typeof t1_token != "undefined" && t1_token != "" && t1_token){
// 				ping("re_init", "token", t1_token)
// 			}
// 		}
// 	}
	var args = {};
	args.mode = mode;
	args.passed_key = passed_key;
	args.passed_value = passed_value;
	args.fbp = getCookie('_fbp');
	
	if( typeof getCookie('fbc') != 'undefined' && getCookie('fbc')){
		args.fbc = getCookie('fbc');
		session_sync('fbc', args.fbc);
	} else {
		args.fbc = getCookie('_fbc');
	}
	
	args.sh_c = getCookie('cart');
	
	if(mode == 'init' || mode == 're_init'){
		if (typeof window != "undefined"){
			if(typeof window.location != "undefined"){
				if(typeof window.location.search != "undefined"){
					const queryString = window.location.search
					const urlParams = new URLSearchParams(queryString);
					if (typeof urlParams.get('fbclid') != "undefined" && urlParams.get('fbclid')){
						var fbc_val = `fb.1.${Date.now()}.${urlParams.get('fbclid')}`;
						setCookie('fbc', fbc_val, { secure: true, expires: 604800000 });
						args.fbc = fbc_val;
						session_sync('fbc', fbc_val);
					}
				}
			}
		}
	}
	
	var xmlHttp
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlHttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}					
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			//may need to console this out
			// alert(xmlHttp.responseText);
			let data = JSON.parse(xmlHttp.response);
			if( typeof data.settings != 'undefined' && data.settings){
				active_account = true;
				setCookie('t1', data.settings.t1, 604800000);
				setCookie('pop6AID', data.settings.account_id, 604800000);
				setCookie('pop6SID', data.settings.session_id, 604800000);
				setCookie('client_user_agent_full', window.clientInformation.userAgent, 604800000);
				setCookie('client_user_agent', window.clientInformation.userAgent.replace(/[^A-Za-z]/g, ""), 604800000);
				if( getCookie('account_mode') == 'debug' ){
					if(console.log == 'User denied the request for Geolocation.'){

					} else {
						console.log(green_dot + pop6 + 'User Agent recorded...');
					}
				}	
				
				
				if( typeof data.pl != 'undefined' && data.pl ){
					var session_str = ' ';
					if( typeof data.pl.em != 'undefined' && data.pl.em){
						setCookie('em', data.pl.em, 604800000);
						session_str += 'em ';			
					}
					if( typeof data.pl.ph != 'undefined' && data.pl.ph){
						setCookie('ph', data.pl.ph, 604800000);
						session_str += 'ph ';			
					}
					if( typeof data.pl.fn != 'undefined' && data.pl.fn){
						setCookie('fn', data.pl.fn, 604800000);
						session_str += 'fn ';			
					}
					if( typeof data.pl.ln != 'undefined' && data.pl.ln){
						setCookie('ln', data.pl.ln, 604800000);
						session_str += 'ln ';			
					}
					if( typeof data.pl.fbp != 'undefined' && data.pl.fbp){
						setCookie('fbp', data.pl.fbp, 604800000);
						session_str += 'fbp ';			
					}
					if( typeof data.pl.fbc != 'undefined' && data.pl.fbc){
						setCookie('fbc', data.pl.fbc, 604800000);
						session_str += 'fbc ';			
					}
					if( typeof data.pl.shop != 'undefined' && data.pl.shop){
						setCookie('shop', data.pl.shop, 604800000);
						session_str += 'shop ';			
					}
					if( getCookie('account_mode') == 'debug' ){
						if(console.log == 'User denied the request for Geolocation.'){

						} else {
							console.log(green_dot + pop6 + '['+session_str+'] loaded from matched session.');
						}
					}
				}
				
				if( typeof data.settings.t2 != 'undefined' ){
					
					setCookie('t2', data.settings.t2, 604800000);
					setCookie('t3', data.settings.t3, 604800000);
					setCookie('t4', data.settings.t4, 604800000);
					setCookie('account_mode', data.settings.account_mode, 604800000);
					
					if( getCookie('account_mode') == 'debug' ){
						if(console.log == 'User denied the request for Geolocation.'){

						} else {
							console.log(green_dot + pop6 + 'Settings loaded. Ready...');
						}
						
					}
					// if (mode != 're_init'){
						load_others();
					// }
				} else {
					if(console.log == 'User denied the request for Geolocation.'){

					} else {
						console.log(red_dot + pop6 + 'Please complete setup at Popsixle.com');
					}
				}
				
			} else {
				active_account = false;
				if( typeof data.expires != 'undefined' && data.expires ){
					if(console.log == 'User denied the request for Geolocation.'){

					} else {
						console.log(red_dot + pop6 + 'Token expired on: ' + data.expires);
					}
				} else if( typeof data.error != 'undefined' ){
					if(console.log == 'User denied the request for Geolocation.'){

					} else {
						console.log(red_dot + pop6 + data.error);
					}
				} else {
					if(console.log == 'User denied the request for Geolocation.'){

					} else {
						console.log(red_dot + pop6 + '(00) Unkown Error - Something went wrong.');
					}
				}
			}
			
		}
	}
	args.sh_c = getCookie('cart');
	
	let argString = new URLSearchParams(Object.entries(args)).toString();
	xmlHttp.open("POST", ping_url, true); 
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
	xmlHttp.send(argString); 
}

//md5
!function(){"use strict";var ERROR="input is invalid type",WINDOW="object"==typeof window,root=WINDOW?window:{};root.JS_SHA256_NO_WINDOW&&(WINDOW=!1);var WEB_WORKER=!WINDOW&&"object"==typeof self,NODE_JS=!root.JS_SHA256_NO_NODE_JS&&"object"==typeof process&&process.versions&&process.versions.node;NODE_JS?root=global:WEB_WORKER&&(root=self);var COMMON_JS=!root.JS_SHA256_NO_COMMON_JS&&"object"==typeof module&&module.exports,AMD="function"==typeof define&&define.amd,ARRAY_BUFFER=!root.JS_SHA256_NO_ARRAY_BUFFER&&"undefined"!=typeof ArrayBuffer,HEX_CHARS="0123456789abcdef".split(""),EXTRA=[-2147483648,8388608,32768,128],SHIFT=[24,16,8,0],K=[1116352408,1899447441,3049323471,3921009573,961987163,1508970993,2453635748,2870763221,3624381080,310598401,607225278,1426881987,1925078388,2162078206,2614888103,3248222580,3835390401,4022224774,264347078,604807628,770255983,1249150122,1555081692,1996064986,2554220882,2821834349,2952996808,3210313671,3336571891,3584528711,113926993,338241895,666307205,773529912,1294757372,1396182291,1695183700,1986661051,2177026350,2456956037,2730485921,2820302411,3259730800,3345764771,3516065817,3600352804,4094571909,275423344,430227734,506948616,659060556,883997877,958139571,1322822218,1537002063,1747873779,1955562222,2024104815,2227730452,2361852424,2428436474,2756734187,3204031479,3329325298],OUTPUT_TYPES=["hex","array","digest","arrayBuffer"],blocks=[];!root.JS_SHA256_NO_NODE_JS&&Array.isArray||(Array.isArray=function(t){return"[object Array]"===Object.prototype.toString.call(t)}),!ARRAY_BUFFER||!root.JS_SHA256_NO_ARRAY_BUFFER_IS_VIEW&&ArrayBuffer.isView||(ArrayBuffer.isView=function(t){return"object"==typeof t&&t.buffer&&t.buffer.constructor===ArrayBuffer});var createOutputMethod=function(t,h){return function(r){return new Sha256(h,!0).update(r)[t]()}},createMethod=function(t){var h=createOutputMethod("hex",t);NODE_JS&&(h=nodeWrap(h,t)),h.create=function(){return new Sha256(t)},h.update=function(t){return h.create().update(t)};for(var r=0;r<OUTPUT_TYPES.length;++r){var e=OUTPUT_TYPES[r];h[e]=createOutputMethod(e,t)}return h},nodeWrap=function(method,is224){var crypto=eval("require('crypto')"),Buffer=eval("require('buffer').Buffer"),algorithm=is224?"sha224":"sha256",nodeMethod=function(t){if("string"==typeof t)return crypto.createHash(algorithm).update(t,"utf8").digest("hex");if(null==t)throw new Error(ERROR);return t.constructor===ArrayBuffer&&(t=new Uint8Array(t)),Array.isArray(t)||ArrayBuffer.isView(t)||t.constructor===Buffer?crypto.createHash(algorithm).update(new Buffer(t)).digest("hex"):method(t)};return nodeMethod},createHmacOutputMethod=function(t,h){return function(r,e){return new HmacSha256(r,h,!0).update(e)[t]()}},createHmacMethod=function(t){var h=createHmacOutputMethod("hex",t);h.create=function(h){return new HmacSha256(h,t)},h.update=function(t,r){return h.create(t).update(r)};for(var r=0;r<OUTPUT_TYPES.length;++r){var e=OUTPUT_TYPES[r];h[e]=createHmacOutputMethod(e,t)}return h};function Sha256(t,h){h?(blocks[0]=blocks[16]=blocks[1]=blocks[2]=blocks[3]=blocks[4]=blocks[5]=blocks[6]=blocks[7]=blocks[8]=blocks[9]=blocks[10]=blocks[11]=blocks[12]=blocks[13]=blocks[14]=blocks[15]=0,this.blocks=blocks):this.blocks=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],t?(this.h0=3238371032,this.h1=914150663,this.h2=812702999,this.h3=4144912697,this.h4=4290775857,this.h5=1750603025,this.h6=1694076839,this.h7=3204075428):(this.h0=1779033703,this.h1=3144134277,this.h2=1013904242,this.h3=2773480762,this.h4=1359893119,this.h5=2600822924,this.h6=528734635,this.h7=1541459225),this.block=this.start=this.bytes=this.hBytes=0,this.finalized=this.hashed=!1,this.first=!0,this.is224=t}function HmacSha256(t,h,r){var e,s=typeof t;if("string"===s){var i,o=[],a=t.length,H=0;for(e=0;e<a;++e)(i=t.charCodeAt(e))<128?o[H++]=i:i<2048?(o[H++]=192|i>>6,o[H++]=128|63&i):i<55296||i>=57344?(o[H++]=224|i>>12,o[H++]=128|i>>6&63,o[H++]=128|63&i):(i=65536+((1023&i)<<10|1023&t.charCodeAt(++e)),o[H++]=240|i>>18,o[H++]=128|i>>12&63,o[H++]=128|i>>6&63,o[H++]=128|63&i);t=o}else{if("object"!==s)throw new Error(ERROR);if(null===t)throw new Error(ERROR);if(ARRAY_BUFFER&&t.constructor===ArrayBuffer)t=new Uint8Array(t);else if(!(Array.isArray(t)||ARRAY_BUFFER&&ArrayBuffer.isView(t)))throw new Error(ERROR)}t.length>64&&(t=new Sha256(h,!0).update(t).array());var n=[],S=[];for(e=0;e<64;++e){var c=t[e]||0;n[e]=92^c,S[e]=54^c}Sha256.call(this,h,r),this.update(S),this.oKeyPad=n,this.inner=!0,this.sharedMemory=r}Sha256.prototype.update=function(t){if(!this.finalized){var h,r=typeof t;if("string"!==r){if("object"!==r)throw new Error(ERROR);if(null===t)throw new Error(ERROR);if(ARRAY_BUFFER&&t.constructor===ArrayBuffer)t=new Uint8Array(t);else if(!(Array.isArray(t)||ARRAY_BUFFER&&ArrayBuffer.isView(t)))throw new Error(ERROR);h=!0}for(var e,s,i=0,o=t.length,a=this.blocks;i<o;){if(this.hashed&&(this.hashed=!1,a[0]=this.block,a[16]=a[1]=a[2]=a[3]=a[4]=a[5]=a[6]=a[7]=a[8]=a[9]=a[10]=a[11]=a[12]=a[13]=a[14]=a[15]=0),h)for(s=this.start;i<o&&s<64;++i)a[s>>2]|=t[i]<<SHIFT[3&s++];else for(s=this.start;i<o&&s<64;++i)(e=t.charCodeAt(i))<128?a[s>>2]|=e<<SHIFT[3&s++]:e<2048?(a[s>>2]|=(192|e>>6)<<SHIFT[3&s++],a[s>>2]|=(128|63&e)<<SHIFT[3&s++]):e<55296||e>=57344?(a[s>>2]|=(224|e>>12)<<SHIFT[3&s++],a[s>>2]|=(128|e>>6&63)<<SHIFT[3&s++],a[s>>2]|=(128|63&e)<<SHIFT[3&s++]):(e=65536+((1023&e)<<10|1023&t.charCodeAt(++i)),a[s>>2]|=(240|e>>18)<<SHIFT[3&s++],a[s>>2]|=(128|e>>12&63)<<SHIFT[3&s++],a[s>>2]|=(128|e>>6&63)<<SHIFT[3&s++],a[s>>2]|=(128|63&e)<<SHIFT[3&s++]);this.lastByteIndex=s,this.bytes+=s-this.start,s>=64?(this.block=a[16],this.start=s-64,this.hash(),this.hashed=!0):this.start=s}return this.bytes>4294967295&&(this.hBytes+=this.bytes/4294967296<<0,this.bytes=this.bytes%4294967296),this}},Sha256.prototype.finalize=function(){if(!this.finalized){this.finalized=!0;var t=this.blocks,h=this.lastByteIndex;t[16]=this.block,t[h>>2]|=EXTRA[3&h],this.block=t[16],h>=56&&(this.hashed||this.hash(),t[0]=this.block,t[16]=t[1]=t[2]=t[3]=t[4]=t[5]=t[6]=t[7]=t[8]=t[9]=t[10]=t[11]=t[12]=t[13]=t[14]=t[15]=0),t[14]=this.hBytes<<3|this.bytes>>>29,t[15]=this.bytes<<3,this.hash()}},Sha256.prototype.hash=function(){var t,h,r,e,s,i,o,a,H,n=this.h0,S=this.h1,c=this.h2,f=this.h3,A=this.h4,R=this.h5,u=this.h6,_=this.h7,E=this.blocks;for(t=16;t<64;++t)h=((s=E[t-15])>>>7|s<<25)^(s>>>18|s<<14)^s>>>3,r=((s=E[t-2])>>>17|s<<15)^(s>>>19|s<<13)^s>>>10,E[t]=E[t-16]+h+E[t-7]+r<<0;for(H=S&c,t=0;t<64;t+=4)this.first?(this.is224?(i=300032,_=(s=E[0]-1413257819)-150054599<<0,f=s+24177077<<0):(i=704751109,_=(s=E[0]-210244248)-1521486534<<0,f=s+143694565<<0),this.first=!1):(h=(n>>>2|n<<30)^(n>>>13|n<<19)^(n>>>22|n<<10),e=(i=n&S)^n&c^H,_=f+(s=_+(r=(A>>>6|A<<26)^(A>>>11|A<<21)^(A>>>25|A<<7))+(A&R^~A&u)+K[t]+E[t])<<0,f=s+(h+e)<<0),h=(f>>>2|f<<30)^(f>>>13|f<<19)^(f>>>22|f<<10),e=(o=f&n)^f&S^i,u=c+(s=u+(r=(_>>>6|_<<26)^(_>>>11|_<<21)^(_>>>25|_<<7))+(_&A^~_&R)+K[t+1]+E[t+1])<<0,h=((c=s+(h+e)<<0)>>>2|c<<30)^(c>>>13|c<<19)^(c>>>22|c<<10),e=(a=c&f)^c&n^o,R=S+(s=R+(r=(u>>>6|u<<26)^(u>>>11|u<<21)^(u>>>25|u<<7))+(u&_^~u&A)+K[t+2]+E[t+2])<<0,h=((S=s+(h+e)<<0)>>>2|S<<30)^(S>>>13|S<<19)^(S>>>22|S<<10),e=(H=S&c)^S&f^a,A=n+(s=A+(r=(R>>>6|R<<26)^(R>>>11|R<<21)^(R>>>25|R<<7))+(R&u^~R&_)+K[t+3]+E[t+3])<<0,n=s+(h+e)<<0;this.h0=this.h0+n<<0,this.h1=this.h1+S<<0,this.h2=this.h2+c<<0,this.h3=this.h3+f<<0,this.h4=this.h4+A<<0,this.h5=this.h5+R<<0,this.h6=this.h6+u<<0,this.h7=this.h7+_<<0},Sha256.prototype.hex=function(){this.finalize();var t=this.h0,h=this.h1,r=this.h2,e=this.h3,s=this.h4,i=this.h5,o=this.h6,a=this.h7,H=HEX_CHARS[t>>28&15]+HEX_CHARS[t>>24&15]+HEX_CHARS[t>>20&15]+HEX_CHARS[t>>16&15]+HEX_CHARS[t>>12&15]+HEX_CHARS[t>>8&15]+HEX_CHARS[t>>4&15]+HEX_CHARS[15&t]+HEX_CHARS[h>>28&15]+HEX_CHARS[h>>24&15]+HEX_CHARS[h>>20&15]+HEX_CHARS[h>>16&15]+HEX_CHARS[h>>12&15]+HEX_CHARS[h>>8&15]+HEX_CHARS[h>>4&15]+HEX_CHARS[15&h]+HEX_CHARS[r>>28&15]+HEX_CHARS[r>>24&15]+HEX_CHARS[r>>20&15]+HEX_CHARS[r>>16&15]+HEX_CHARS[r>>12&15]+HEX_CHARS[r>>8&15]+HEX_CHARS[r>>4&15]+HEX_CHARS[15&r]+HEX_CHARS[e>>28&15]+HEX_CHARS[e>>24&15]+HEX_CHARS[e>>20&15]+HEX_CHARS[e>>16&15]+HEX_CHARS[e>>12&15]+HEX_CHARS[e>>8&15]+HEX_CHARS[e>>4&15]+HEX_CHARS[15&e]+HEX_CHARS[s>>28&15]+HEX_CHARS[s>>24&15]+HEX_CHARS[s>>20&15]+HEX_CHARS[s>>16&15]+HEX_CHARS[s>>12&15]+HEX_CHARS[s>>8&15]+HEX_CHARS[s>>4&15]+HEX_CHARS[15&s]+HEX_CHARS[i>>28&15]+HEX_CHARS[i>>24&15]+HEX_CHARS[i>>20&15]+HEX_CHARS[i>>16&15]+HEX_CHARS[i>>12&15]+HEX_CHARS[i>>8&15]+HEX_CHARS[i>>4&15]+HEX_CHARS[15&i]+HEX_CHARS[o>>28&15]+HEX_CHARS[o>>24&15]+HEX_CHARS[o>>20&15]+HEX_CHARS[o>>16&15]+HEX_CHARS[o>>12&15]+HEX_CHARS[o>>8&15]+HEX_CHARS[o>>4&15]+HEX_CHARS[15&o];return this.is224||(H+=HEX_CHARS[a>>28&15]+HEX_CHARS[a>>24&15]+HEX_CHARS[a>>20&15]+HEX_CHARS[a>>16&15]+HEX_CHARS[a>>12&15]+HEX_CHARS[a>>8&15]+HEX_CHARS[a>>4&15]+HEX_CHARS[15&a]),H},Sha256.prototype.toString=Sha256.prototype.hex,Sha256.prototype.digest=function(){this.finalize();var t=this.h0,h=this.h1,r=this.h2,e=this.h3,s=this.h4,i=this.h5,o=this.h6,a=this.h7,H=[t>>24&255,t>>16&255,t>>8&255,255&t,h>>24&255,h>>16&255,h>>8&255,255&h,r>>24&255,r>>16&255,r>>8&255,255&r,e>>24&255,e>>16&255,e>>8&255,255&e,s>>24&255,s>>16&255,s>>8&255,255&s,i>>24&255,i>>16&255,i>>8&255,255&i,o>>24&255,o>>16&255,o>>8&255,255&o];return this.is224||H.push(a>>24&255,a>>16&255,a>>8&255,255&a),H},Sha256.prototype.array=Sha256.prototype.digest,Sha256.prototype.arrayBuffer=function(){this.finalize();var t=new ArrayBuffer(this.is224?28:32),h=new DataView(t);return h.setUint32(0,this.h0),h.setUint32(4,this.h1),h.setUint32(8,this.h2),h.setUint32(12,this.h3),h.setUint32(16,this.h4),h.setUint32(20,this.h5),h.setUint32(24,this.h6),this.is224||h.setUint32(28,this.h7),t},HmacSha256.prototype=new Sha256,HmacSha256.prototype.finalize=function(){if(Sha256.prototype.finalize.call(this),this.inner){this.inner=!1;var t=this.array();Sha256.call(this,this.is224,this.sharedMemory),this.update(this.oKeyPad),this.update(t),Sha256.prototype.finalize.call(this)}};var exports=createMethod();exports.sha256=exports,exports.sha224=createMethod(!0),exports.sha256.hmac=createHmacMethod(),exports.sha224.hmac=createHmacMethod(!0),COMMON_JS?module.exports=exports:(root.sha256=exports.sha256,root.sha224=exports.sha224,AMD&&define(function(){return exports}))}();