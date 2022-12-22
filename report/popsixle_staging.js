
/*
var meta = {"product":{"id":4681855533135,"gid":"gid:\/\/shopify\/Product\/4681855533135","vendor":"HEIRESS BEVERLY HILLS","type":"","variants":[{"id":40490343563461,"price":12800,"name":"Diamante strap dress - XXS","public_title":"XXS","sku":""},{"id":32337517740111,"price":12800,"name":"Diamante strap dress - XS","public_title":"XS","sku":""},{"id":32337517772879,"price":12800,"name":"Diamante strap dress - S","public_title":"S","sku":""},{"id":32337517805647,"price":12800,"name":"Diamante strap dress - M","public_title":"M","sku":""},{"id":32337517838415,"price":12800,"name":"Diamante strap dress - L","public_title":"L","sku":""},{"id":40490353131717,"price":12800,"name":"Diamante strap dress - XL","public_title":"XL","sku":""},{"id":40490359259333,"price":12800,"name":"Diamante strap dress - XXL","public_title":"XXL","sku":""}]},"page":{"pageType":"product","resourceType":"product","resourceId":4681855533135},"evids":{"pv":"Page View","vprd":"Viewed Product","ps":"Performed Search"}};

*/
document.addEventListener("DOMContentLoaded", function() {
	
	


	
	
	get_events();
	
	/*

	$('#ViewContent').click(function(){
		ping_capi('ViewContent');
	});
	
	$('#Lead').click(function(){
		ping_capi('Lead', 50);
	});
	
	$('#InitiateCheckout').click(function(){
		ping_capi('InitiateCheckout', 5);
	});
	
	$('#Purchase').click(function(){
		ping_capi('Purchase', 100);
	});
	
	$('#CompleteRegistration').click(function(){
		ping_capi('CompleteRegistration', 200);
	});
	
	*/
	
	try {
	  //console.log(white_dot + pop6 + meta);
	} catch (e) {
	  //console.log(red_dot + pop6 + e);
	}
	
	/*
		
	get URL events from database
	if current URL matches, ping CAPI with event name and amount
		
	get click events from database
	create click event listeners with each element, event, and amount
		
	*/
	
	
	/*
		form for event setup
		
		selector: choose event type (either URL event or Click event)
		
		url event:
			form element 1: text field - enter text of URL contains string query
			form element 2: drop down selector: choose CAPI event type from
				PageView
				ViewContent
				AddToCart
				InitiateCheckout
				Purchase
				Lead
				CompleteRegistration
			form element 3: text field - enter USD value of event (Not required)
			
		click event:
			form element 1: radio selector - choose either "id" or "class" selector
			form element 2: first append '#" or "." based on radio selection. text field - enter click element id/class name
			form element 3: drop down selector: choose CAPI event type from
				PageView
				ViewContent
				AddToCart
				InitiateCheckout
				Purchase
				Lead
				CompleteRegistration
			form element 4: text field - enter USD value of event (Not required, unless purchase event)
			
			
		database structure:
		- new table for events
			id
			account_token
			event_type (url vs click)
			selector_query
			event_type
			event_val
			created
			updated
	/*
		
	
	
	/*
	heiress
	<button
		type="submit"
		name="add"
		data-add-to-cart
		class="add-to-cart"
	>
		<span
			data-add-to-cart-text
		>
		Add to Cart
		</span>
	</button>
		
	//button-click
	--> button text
	--> button classes
	--> button id
	--> button data parameters
	--> form elements	
	
	
	fb standard events:
	
	-> ViewContent
	-> AddToCart
	-> InititateCheckout
		
		<span data-checkout-subtotal-price-target="6800" >
		<span data-checkout-total-shipping-target="0" >
		<span data-checkout-total-taxes-target="0" >
		<span data-checkout-payment-due-target="6800" >
	-> Purchase
		-> value
		-> currency
	-> Lead
	-> CompleteRegistration
	
	
	*/
	
	
	/*
		
	page view on all page loads - URL, URL groupings, Meta parameters
	view content - if meta is available - singular product is found - pass all content details (page URL contains "collections")
		value
		currency
		content_ids
		content_type
		content_name
		content_category	
		
	add to cart (page URL contains "cart")
		same params as view conent with "num_items"
		
	checkout 
		shows total cart value
		
	purchase (page URL contains "order")
	
	for value - add option for manual vs dynamic load
	for events - add button text
	
	
	heiress
	 - view content on product pages
	 - add to cart
	 - initiate checkout
	 - purchase
	*/
	
});

function close_msg(){
	var elem = document.getElementById('pop6-msg');
    return elem.parentNode.removeChild(elem);
}

function ping_capi(event_type = 0, event_value = 0){
	var args = {};
	args.mode = 'ping_capi';
	args.t1 = Cookies.get('t1');
	args.t2 = Cookies.get('t2');
	args.t3 = Cookies.get('t3');
	args.t4 = Cookies.get('t4');
	args.fbp = Cookies.get('_fbp');
	args.fbc = Cookies.get('_fbc');
	args.em = Cookies.get('em');
	args.ph = Cookies.get('ph');
	args.fn = Cookies.get('fn');
	args.ln = Cookies.get('ln');
	args.event_type = event_type;
	args.event_value = event_value;
	
	if( typeof meta != 'undefined' && meta){
		if( typeof meta.product != 'undefined' && meta.product){
			if( typeof meta.product.id != 'undefined' && meta.product.id){
				args.content_ids = [];
				args.content_ids.push(meta.product.id);
				//upgrade page view to content view
				if( args.event_type == 'PageView' ){
					args.event_type = 'ContentView';
				}
			}
			if( typeof meta.product.variants != 'undefined' && meta.product.variants){
				var i = 0;
				if( meta.product.variants.length > 1 ){
					var selected_id = document.querySelector('input[name="id"]').val();
					
					meta.product.variants.forEach((the_data, index) => {
						if(  the_data.id == selected_id ){
							i = index;
						}
					})
					
				}
				if( meta.product.variants[i].name != 'undefined' && meta.product.variants[i].name ){
					args.content_name = meta.product.variants[i].name;
				}
				
				if( meta.product.variants[i].price != 'undefined' && meta.product.variants[i].price ){
					
					if( event_type == 'AddToCart' || event_type == 'Purchase' || event_type == 'InitiateCheckout'){
						args.event_value = meta.product.variants[i].price / 100.00;
						args.event_value = (Math.round(args.event_value * 100) / 100).toFixed(2);
					}
				}
			}
		}
	}
	
	if( typeof args.t1 == 'undefined' || typeof args.t2 == 'undefined' || typeof args.t3 == 'undefined' || typeof args.t4 == 'undefined' ){
		return false;
	} else {
		
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
							console.log(green_dot + pop6+event_type+' event logged with value: '+event_value);
						} else {
							console.log(green_dot + pop6+' . ');
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

function get_events(){
	var args = {};
	args.mode = 'get_events';
	args.t1 = Cookies.get('t1');
	
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
			if( typeof data.events != 'undefined' && data.events ){
				if( Cookies.get('account_mode') == 'debug' ){
					console.log(green_dot + pop6+'Events loaded...');
				}
				data.events.forEach((the_data, index) => {
					if( the_data.event_trigger == 'click' ){
						document.querySelector(the_data.selector_query).addEventListener("click", function(){
							ping_capi(the_data.event_type, the_data.event_value);
						});	
					}
					if( the_data.event_trigger == 'url' ){
						var page = window.location.pathname;
						if( page.includes( the_data.selector_query ) ){
							ping_capi(the_data.event_type, the_data.event_value);
						} else {
							ping_capi('PageView', '');
						}
					} else {
						ping_capi('PageView', '');
					}
				})
			} else {
				ping_capi('PageView', '');
			}
		}
	}

	let argString = new URLSearchParams(Object.entries(args)).toString()
	xmlHttp.open("POST", ping_url, true); 
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
	xmlHttp.send(argString);
}

function ping(mode = 'init', passed_key = 0, passed_value = 0){
	var args = {};
	args.mode = mode;
	args.passed_key = passed_key;
	args.passed_value = passed_value;
	
	var xmlHttp
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlHttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}					
	xmlHttp.onreadystatechange = function(data) {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			//may need to console this out
			// alert(xmlHttp.responseText);
			let data = JSON.parse(xmlHttp.response);
			if( typeof data.settings != 'undefined' && data.settings){
			
				Cookies.set('t1', data.settings.t1, { secure: true, expires: 86400 });
	
				//console.log(Cookies.get('t1'));
	
				
				if( typeof data.settings.t2 != 'undefined' && data.settings.t2){
					console.log(green_dot + pop6 + 'Settings loaded. Ready...');
					Cookies.set('t2', data.settings.t2, { secure: true, expires: 86400 });
					Cookies.set('t3', data.settings.t3, { secure: true, expires: 86400 });
					Cookies.set('t4', data.settings.t4, { secure: true, expires: 86400 });
				} else {
					
					console.log(red_dot + pop6 + 'Please complete setup at Popsixle.com');
	
				}
				
			} else {
				if( typeof data.expires != 'undefined' ){
					console.log(red_dot + pop6 + 'Token expired on: ' + data.expires);
				} else if( typeof data.error != 'undefined' ){
					console.log(red_dot + pop6 + data.error);
				} else {
					console.log(red_dot + pop6 + '(00) Unkown Error - Something went wrong.');
				}
			}
		}
	}

	let argString = new URLSearchParams(Object.entries(args)).toString()
	xmlHttp.open("POST", ping_url, true); 
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
	xmlHttp.send(argString); 
}

(function(d,f){"use strict";var h=function(d){if("object"!==typeof d.document)throw Error("Cookies.js requires a `window` with a `document` object");var b=function(a,e,c){return 1===arguments.length?b.get(a):b.set(a,e,c)};b._document=d.document;b._cacheKeyPrefix="cookey.";b._maxExpireDate=new Date("Fri, 31 Dec 9999 23:59:59 UTC");b.defaults={path:"/",secure:!1};b.get=function(a){b._cachedDocumentCookie!==b._document.cookie&&b._renewCache();a=b._cache[b._cacheKeyPrefix+a];return a===f?f:decodeURIComponent(a)};
b.set=function(a,e,c){c=b._getExtendedOptions(c);c.expires=b._getExpiresDate(e===f?-1:c.expires);b._document.cookie=b._generateCookieString(a,e,c);return b};b.expire=function(a,e){return b.set(a,f,e)};b._getExtendedOptions=function(a){return{path:a&&a.path||b.defaults.path,domain:a&&a.domain||b.defaults.domain,expires:a&&a.expires||b.defaults.expires,secure:a&&a.secure!==f?a.secure:b.defaults.secure}};b._isValidDate=function(a){return"[object Date]"===Object.prototype.toString.call(a)&&!isNaN(a.getTime())};
b._getExpiresDate=function(a,e){e=e||new Date;"number"===typeof a?a=Infinity===a?b._maxExpireDate:new Date(e.getTime()+1E3*a):"string"===typeof a&&(a=new Date(a));if(a&&!b._isValidDate(a))throw Error("`expires` parameter cannot be converted to a valid Date instance");return a};b._generateCookieString=function(a,b,c){a=a.replace(/[^#$&+\^`|]/g,encodeURIComponent);a=a.replace(/\(/g,"%28").replace(/\)/g,"%29");b=(b+"").replace(/[^!#$&-+\--:<-\[\]-~]/g,encodeURIComponent);c=c||{};a=a+"="+b+(c.path?";path="+
c.path:"");a+=c.domain?";domain="+c.domain:"";a+=c.expires?";expires="+c.expires.toUTCString():"";return a+=c.secure?";secure":""};b._getCacheFromString=function(a){var e={};a=a?a.split("; "):[];for(var c=0;c<a.length;c++){var d=b._getKeyValuePairFromCookieString(a[c]);e[b._cacheKeyPrefix+d.key]===f&&(e[b._cacheKeyPrefix+d.key]=d.value)}return e};b._getKeyValuePairFromCookieString=function(a){var b=a.indexOf("="),b=0>b?a.length:b,c=a.substr(0,b),d;try{d=decodeURIComponent(c)}catch(k){console&&"function"===
typeof console.error&&console.error('Could not decode cookie with key "'+c+'"',k)}return{key:d,value:a.substr(b+1)}};b._renewCache=function(){b._cache=b._getCacheFromString(b._document.cookie);b._cachedDocumentCookie=b._document.cookie};b._areEnabled=function(){var a="1"===b.set("cookies.js",1).get("cookies.js");b.expire("cookies.js");return a};b.enabled=b._areEnabled();return b},g=d&&"object"===typeof d.document?h(d):h;"function"===typeof define&&define.amd?define(function(){return g}):"object"===
typeof exports?("object"===typeof module&&"object"===typeof module.exports&&(exports=module.exports=g),exports.Cookies=g):d.Cookies=g})("undefined"===typeof window?this:window);