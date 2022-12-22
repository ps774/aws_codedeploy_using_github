function pop6_init_01(){

	if( typeof a10x_dl != 'undefined' ){
		
		a10x_dl.client_user_agent_full = window.clientInformation.userAgent;
		a10x_dl.client_user_agent = window.clientInformation.userAgent.replace(/[^A-Za-z]/g, "");
		a10x_dl.client_ip_address = p6_get_cookie('gtm_p6_ip');
		
		if( typeof a10x_dl.client_ip_address == "undefined"){
			
			if (window.XMLHttpRequest) {
				xmlHttp=new XMLHttpRequest();
			}
			else {
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlHttp.onreadystatechange = function() {
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
					let data = JSON.parse(xmlHttp.response);
					a10x_dl.client_ip_address = data.IPv4;
					p6_set_cookie('gtm_p6_ip', data.IPv4, 86400000); //1d
					pop6_init_02();
				}
			}
		
			xmlHttp.open("POST", 'https://geolocation-db.com/json/', true);
			xmlHttp.send(); 

		} else {
			pop6_init_02();
		}
		
	} else {
		console.log(red_dot + pop6 + ' Error: 101');
	} 
	
}

function pop6_init_02(){
	if( typeof a10x_dl != 'undefined' ){	
		//first check if popsixle session cookie is set, if so, run init_03, if not, continue
		a10x_dl.s_id = p6_get_cookie('gtm_p6_s_id'); //2678400000 = one month of milliseconds
		if( typeof a10x_dl.s_id == 'undefined' ){
			var xmlHttp
			if (window.XMLHttpRequest) {
				xmlHttp=new XMLHttpRequest();
			}
			else {
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}					
			xmlHttp.onreadystatechange = function() {
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
					let data = JSON.parse(xmlHttp.response);
					process_session(data, '02');
				}
			}
			let argString = new URLSearchParams(Object.entries(a10x_dl)).toString();
			xmlHttp.open("POST", ping_base + 'pop6_init_02_02.php', true); 
			xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
			xmlHttp.send(argString);
		} else {
			var xmlHttp
			if (window.XMLHttpRequest) {
				xmlHttp=new XMLHttpRequest();
			}
			else {
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}					
			xmlHttp.onreadystatechange = function() {
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
					let data = JSON.parse(xmlHttp.response);
					process_session(data, '01');
				}
			}
			let argString = new URLSearchParams(Object.entries(a10x_dl)).toString();
			xmlHttp.open("POST", ping_base + 'pop6_init_02_01.php', true); 
			xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
			xmlHttp.send(argString);
		}
	} else {
		console.log(red_dot + pop6 + ' Error: 102');
	}	
}

function pop6_init_03(){

	if( typeof a10x_dl != 'undefined' ){
		//prior to any checks, store the currenct fbc for version control
		a10x_dl.fbc_last = a10x_dl.fbc;
		var fbc_clean = '';
		
		//check if there's an existing fbc in the session/dataLayer
		if (typeof a10x_dl.fbc != "undefined" && a10x_dl.fbc ){
			fbc_clean = a10x_dl.fbc.split('_aem_');
			fbc_clean = fbc_clean[0];
			var fbc_chunks = fbc_clean.split('.');
			//check its age, discard if too old
			var fbc_time = parseInt( fbc_chunks[2] );
			if( Date.now() - fbc_time > 604800000 ){ //1w
				a10x_dl.fbc = '';
			}
		}
		
		if (typeof window != "undefined"){
			if(typeof window.location != "undefined"){
				if(typeof window.location.search != "undefined"){
					const queryString = window.location.search
					const urlParams = new URLSearchParams(queryString);
					
					
					var fb_c_p = p6_get_cookie('_fbc');
					var fb_c_c = p6_get_cookie('gtm_p6_fb_c_c');
					
					//first look in the url for the click id
					if (typeof urlParams.get('fbclid') != "undefined" && urlParams.get('fbclid')){
						var fbc_val = `fb.1.${Date.now()}.${urlParams.get('fbclid')}`;
						fbc_clean = fbc_val.split('_aem_');
						fbc_val = fbc_clean[0];
						
						p6_set_cookie('gtm_p6_fb_c_c', fbc_val, { secure: true, expires: 8035200000 }); //3m
						a10x_dl.fbc = fbc_val;
						
					//then look for the our previous cookie
					} else if( typeof fb_c_c != 'undefined' && fb_c_c ){
						a10x_dl.fbc = fb_c_c;
					
					//then look for the facebook pixel/cookie
					} else if( typeof fb_c_p != 'undefined' && fb_c_p ){
						fbc_clean = fb_c_p.split('_aem_');
						fb_c_p = fbc_clean[0];
						p6_set_cookie('gtm_p6_fb_c_p', fb_c_p, { secure: true, expires: 8035200000 }); //3m
						a10x_dl.fbc = fb_c_p;
					}
					
					if( a10x_dl.fbc != a10x_dl.fbc_last ){
						session_sync(a10x_dl.s_id, 'fbc', a10x_dl.fbc);
					}
				}
			}
		}
	} else {
		console.log(red_dot + pop6 + ' Error: 103');
	}
	pop6_init_04();
}

function pop6_init_04(){

	if( typeof a10x_dl != 'undefined' ){
		//prior to any checks, store the currenct fbp for version control
		a10x_dl.fbp_last = a10x_dl.fbp;
		
		//check if there's an existing fbp in the session/dataLayer
		if (typeof a10x_dl.fbp != "undefined" && a10x_dl.fbp ){
			var fbp_chunks = a10x_dl.fbp.split('.');
			//check its age, discard if too old
			var fbp_time = parseInt( fbp_chunks[2] );
			if( Date.now() - fbp_time > 2678400000 ){ //1m
				a10x_dl.fbp = '';
			}
		}

		var fb_p_p = p6_get_cookie('_fbp');

		//first look for the facebook pixel/cookie
		if( typeof fb_p_p != 'undefined' && fb_p_p ){
			//p6_set_cookie('gtm_p6_fb_p_p', fb_p_p, { secure: true, expires: 2678400000 }); //1m
			a10x_dl.fbp = fb_p_p;
		} 
		
		if( a10x_dl.fbp != a10x_dl.fbp_last ){
			session_sync(a10x_dl.s_id, 'fbp', a10x_dl.fbp);
		}

	} else {
		console.log(red_dot + pop6 + ' Error: 103b');
	}
	
	pop6_init_05();
}

function pop6_init_05(reinit = false){
	if( typeof a10x_dl != 'undefined' ){
		
		get_metadata( a10x_dl.metadata_mode );
		
		var xmlHttp
		if (window.XMLHttpRequest) {
			xmlHttp=new XMLHttpRequest();
		}
		else {
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}	
		xmlHttp.onreadystatechange = function() {
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
				let data = JSON.parse(xmlHttp.response);
				if( typeof data.events != 'undefined' && data.events ){
					
					data.events.forEach((the_data, index) => {
						if(typeof the_data.currency != 'undefined'){
							var currency = the_data.currency;
						} else {
							var currency = 'USD';
						}
						if( the_data.event_trigger == 'click' ){
							if (the_data.selector_query.includes("=")){
								let multi_tag_data = document.querySelectorAll(the_data.selector_query)
								multi_tag_data.forEach(tag => {
									tag.addEventListener("click", () => {
										
										if( parseInt( a10x_dl.event_value ) < 1 &&  parseInt( a10x_dl.content_value ) > 1 ){
											a10x_dl.event_value = a10x_dl.content_value;
										}
										if( typeof a10x_dl.event_value != 'undefined' ){
											if( parseInt( a10x_dl.event_value ) > 0 &&  parseInt( the_data.event_value ) == 0 ){
												custom_get_value(the_data.event_type, a10x_dl.event_value, tag);
											} else {
												custom_get_value(the_data.event_type, the_data.event_value, tag);
											}
										} else {
											custom_get_value(the_data.event_type, the_data.event_value, tag);
										}

									});	
								})

							} else if(document.querySelector(the_data.selector_query)){
								document.querySelector(the_data.selector_query).addEventListener("click", function(){
									
									if( parseInt( a10x_dl.event_value ) < 1 &&  parseInt( a10x_dl.content_value ) > 1 ){
										a10x_dl.event_value = a10x_dl.content_value;
									}
									
									if( typeof a10x_dl.event_value != 'undefined' ){
																				
										if( parseInt( a10x_dl.event_value ) > 0 &&  parseInt( the_data.event_value ) == 0 ){
											ping_capi(the_data.event_type, a10x_dl.event_value);
										} else {
											ping_capi(the_data.event_type, the_data.event_value);
										}
									} else {
										ping_capi(the_data.event_type, the_data.event_value);
									}
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
				if (reinit == true){
					setTimeout(() => {
						if( typeof data.form_fields != 'undefined' && data.form_fields ){
							form_fields = data.form_fields;
							form_fields2 = data.form_fields;
							data.form_fields.forEach((the_data, index) => {
								if (the_data.field_selector[0] != "#" && the_data.field_selector[0] != "." && !the_data.field_selector.includes("=") && document.querySelector(`input[name='${the_data.field_selector}']`) ) {
									document.querySelector(`input[name='${the_data.field_selector}']`).addEventListener("focusout", function(){
										form_fields2.forEach((the_data2, index2) => {
											if(the_data2.field_selector[0] != "#" && the_data2.field_selector[0] != "." && !the_data2.field_selector.includes("=") && typeof document.querySelector(`input[name='${the_data2.field_selector}']`) != 'undefined'  && document.querySelector(`input[name='${the_data2.field_selector}']`)){
												if( typeof document.querySelector(`input[name='${the_data2.field_selector}']`).value != 'undefined' && document.querySelector(`input[name='${the_data2.field_selector}']`).value){
													var v = document.querySelector(`input[name='${the_data2.field_selector}']`).value;
													if( typeof v != 'undefined' && v != ''){
														var sha256_v = sha256(v);
														//p6_set_cookie('gtm_p6_'+the_data2.field_type, sha256_v, 5356800000);//2m
														if( a10x_dl[the_data2.field_type] !=  sha256_v ){
															a10x_dl[the_data2.field_type] = sha256_v;
															session_sync( a10x_dl.s_id, the_data2.field_type, sha256_v);
														}
													}
												}
											}
										})							
									});	
								} else if (the_data.field_selector[0] != "#" && the_data.field_selector[0] != "." && the_data.field_selector.includes("=")) {
									if (document.querySelector(the_data.field_selector) && typeof document.querySelector(the_data.field_selector) != 'undefined'){
										document.querySelector(the_data.field_selector).addEventListener("focusout", function(){
											form_fields2.forEach((the_data2, index2) => {
												if( typeof document.querySelector(the_data2.field_selector) != 'undefined'  && document.querySelector(the_data2.field_selector)){
													if( typeof document.querySelector(the_data2.field_selector).value != 'undefined' && document.querySelector(the_data2.field_selector).value){
														var v = document.querySelector(the_data2.field_selector).value;
														if( typeof v != 'undefined' && v != ''){
															var sha256_v = sha256(v);
															//p6_set_cookie('gtm_p6_'+the_data2.field_type, sha256_v, 5356800000);//2m
															if( a10x_dl[the_data2.field_type] !=  sha256_v ){
																a10x_dl[the_data2.field_type] = sha256_v;
																session_sync( a10x_dl.s_id, the_data2.field_type, sha256_v);
															}
														}
													}
												}
											})							
										});	
									}
								} else if (document.querySelector(the_data.field_selector) && typeof document.querySelector(the_data.field_selector) != 'undefined' && !the_data.field_selector.includes("=")){
									document.querySelector(the_data.field_selector).addEventListener("focusout", function(){
										form_fields2.forEach((the_data2, index2) => {
											if( typeof document.querySelector(the_data2.field_selector) != 'undefined'  && document.querySelector(the_data2.field_selector)){
												if( typeof document.querySelector(the_data2.field_selector).value != 'undefined' && document.querySelector(the_data2.field_selector).value){
													var v = document.querySelector(the_data2.field_selector).value;
													if( typeof v != 'undefined' && v != ''){
														var sha256_v = sha256(v);
														//p6_set_cookie('gtm_p6_'+the_data2.field_type, sha256_v, 5356800000);//2m
														if( a10x_dl[the_data2.field_type] !=  sha256_v ){
															a10x_dl[the_data2.field_type] = sha256_v;
															session_sync( a10x_dl.s_id, the_data2.field_type, sha256_v);
														}
													}
												}
											}
										})
									});	
								}
							})
						}	
					}, 1000);
				} else {

					if( typeof data.form_fields != 'undefined' && data.form_fields ){
						form_fields = data.form_fields;
						form_fields2 = data.form_fields;
						data.form_fields.forEach((the_data, index) => {
							if (the_data.field_selector[0] != "#" && the_data.field_selector[0] != "." && !the_data.field_selector.includes("=") && document.querySelector(`input[name='${the_data.field_selector}']`) ) {
								document.querySelector(`input[name='${the_data.field_selector}']`).addEventListener("focusout", function(){
									form_fields2.forEach((the_data2, index2) => {
										if(the_data2.field_selector[0] != "#" && the_data2.field_selector[0] != "." && !the_data2.field_selector.includes("=") && typeof document.querySelector(`input[name='${the_data2.field_selector}']`) != 'undefined'  && document.querySelector(`input[name='${the_data2.field_selector}']`)){
											if( typeof document.querySelector(`input[name='${the_data2.field_selector}']`).value != 'undefined' && document.querySelector(`input[name='${the_data2.field_selector}']`).value){
												var v = document.querySelector(`input[name='${the_data2.field_selector}']`).value;
												if( typeof v != 'undefined' && v != ''){
													var sha256_v = sha256(v);
													//p6_set_cookie('gtm_p6_'+the_data2.field_type, sha256_v, 5356800000);//2m
													if( a10x_dl[the_data2.field_type] !=  sha256_v ){
														a10x_dl[the_data2.field_type] = sha256_v;
														session_sync( a10x_dl.s_id, the_data2.field_type, sha256_v);
													}
												}
											}
										}
									})							
								});	
								
							} else if (the_data.field_selector[0] != "#" && the_data.field_selector[0] != "." && the_data.field_selector.includes("=")) {
								if (document.querySelector(the_data.field_selector) && typeof document.querySelector(the_data.field_selector) != 'undefined'){
									document.querySelector(the_data.field_selector).addEventListener("focusout", function(){
										form_fields2.forEach((the_data2, index2) => {
											if( typeof document.querySelector(the_data2.field_selector) != 'undefined'  && document.querySelector(the_data2.field_selector)){
												if( typeof document.querySelector(the_data2.field_selector).value != 'undefined' && document.querySelector(the_data2.field_selector).value){
													var v = document.querySelector(the_data2.field_selector).value;
													if( typeof v != 'undefined' && v != ''){
														var sha256_v = sha256(v);
														//p6_set_cookie('gtm_p6_'+the_data2.field_type, sha256_v, 5356800000);//2m
														if( a10x_dl[the_data2.field_type] !=  sha256_v ){
															a10x_dl[the_data2.field_type] = sha256_v;
															session_sync( a10x_dl.s_id, the_data2.field_type, sha256_v);
														}
													}
												}
											}
										})							
									});	
								}
							} else if (document.querySelector(the_data.field_selector) && typeof document.querySelector(the_data.field_selector) != 'undefined' && !the_data.field_selector.includes("=")){
								document.querySelector(the_data.field_selector).addEventListener("focusout", function(){
									form_fields2.forEach((the_data2, index2) => {
										if( typeof document.querySelector(the_data2.field_selector) != 'undefined'  && document.querySelector(the_data2.field_selector)){
											if( typeof document.querySelector(the_data2.field_selector).value != 'undefined' && document.querySelector(the_data2.field_selector).value){
												var v = document.querySelector(the_data2.field_selector).value;
												if( typeof v != 'undefined' && v != ''){
													var sha256_v = sha256(v);
													//p6_set_cookie('gtm_p6_'+the_data2.field_type, sha256_v, 5356800000);//2m
													if( a10x_dl[the_data2.field_type] !=  sha256_v ){
														a10x_dl[the_data2.field_type] = sha256_v;
														session_sync( a10x_dl.s_id, the_data2.field_type, sha256_v);
													}
												}
											}
										}
									})
								});	
							}
						})
					}				
				}
			}
		}
		
		var args = {};
		args.t1 = a10x_dl.t1;
		let argString = new URLSearchParams(Object.entries(args)).toString()
		xmlHttp.open("POST", ping_base + 'pop6_init_05.php', true); 
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		xmlHttp.send(argString); 
		
	} else {
		console.log(red_dot + pop6 + ' Error: 104');
	}
	
	if( reinit ){
		
	} else {
		pop6_init_06();
	}
}

var run_loop = true;

function pop6_init_06(){
	if( typeof a10x_dl != 'undefined' ){
		//is it shopify?
		if (typeof meta != 'undefined' && meta ) { 
			sync_cart();
			window.addEventListener("mousedown", () => {
				if( run_loop ){
					sync_cart();
				}
			})
			window.addEventListener("touchstart", () => {
				if( run_loop ){
					sync_cart();
				}
			})
		} 
	} else {
		console.log(red_dot + pop6 + ' Error: 106');
	
	}
	
	ping_capi( 'PageView', '0.00' );
}

function process_event( event_type, event_value = '', currency = '' ){
	
	var custom_data = {};
	
	//if( event_type == 'ViewContent' || event_type == 'AddToCart' || event_type == 'Purchase' ){
	//	custom_data = get_metadata( a10x_dl.metadata_mode );
	//	a10x_dl.custom_data = custom_data;
	//}
	
	//payload( event_type, event_value, currency, custom_data );	

}
var last_url = window.location.href;
var datalayer_arg_num = ""
var custom_event_fire = false
function get_metadata(mode = 'meta'){
	if (  mode.includes('headless') ){	

		window.addEventListener("mousedown", function(){
			var url = window.location.href;
			if( url != last_url ){
				last_url = url
				pop6_init_05()
			}
		});
		window.addEventListener("touchstart", function(){
			var url = window.location.href;
			if( url != last_url ){
				last_url = url
				pop6_init_05()
			}
		});
	}
	if ( mode.includes('bestfriends') ){
		if( typeof google_tag_manager != 'undefined'){
			if( typeof google_tag_manager['GTM-PQNJ8LJ'] != 'undefined' && google_tag_manager['GTM-PQNJ8LJ'] ){
				if( typeof google_tag_manager['GTM-PQNJ8LJ'].dataLayer != 'undefined' && google_tag_manager['GTM-PQNJ8LJ'].dataLayer ){
					
					var bestfriends_event_id = google_tag_manager['GTM-PQNJ8LJ'].dataLayer.get('orderID');
					if( typeof bestfriends_event_id != 'undefined' && bestfriends_event_id){			
						a10x_dl.event_id = bestfriends_event_id
					} else {
						bestfriends_event_id = google_tag_manager['GTM-PQNJ8LJ'].dataLayer.get('external_id');
						if( typeof bestfriends_event_id != 'undefined' && bestfriends_event_id){			
							a10x_dl.event_id = bestfriends_event_id
						}
					}
				}
			}
		}
	}
	if( mode.includes('gtm') ){
		if( typeof dataLayer != 'undefined' ){
			dataLayer.push({'event':'pop6-base-code-loaded'});
		}
	}
	if (mode.includes('olsen')){ 
		if( typeof document.getElementById('subtotal') != 'undefined' && document.getElementById('subtotal') && document.getElementById('subtotal') != "null" ){
				var the_price = document.getElementById('subtotal').innerText.replace(/[^0-9.\,]/g, '');
				a10x_dl.event_value = parseFloat(the_price).toFixed(2)
				a10x_dl.content_value = a10x_dl.event_value;
		}
		if (document.querySelectorAll('.woocommerce-Price-amount').length > 0) {
			if( typeof document.querySelectorAll('.woocommerce-Price-amount')[0] != 'undefined' && document.querySelectorAll('.woocommerce-Price-amount')[0] && document.querySelectorAll('.woocommerce-Price-amount')[0] != "null" ){
				var the_price = document.querySelectorAll('.woocommerce-Price-amount')[document.querySelectorAll('.woocommerce-Price-amount').length - 1].innerText.replace(/[^0-9.\,]/g, '');
				a10x_dl.event_value = parseFloat(the_price).toFixed(2)
				a10x_dl.content_value = a10x_dl.event_value;
				payload( 'InitiateCheckout', a10x_dl );
			}
		}

	}
	if( mode == 'meta' ){
		
		//check for Shopify.currency.active
		if( typeof Shopify != 'undefined' ){
			if( typeof Shopify.currency != 'undefined' ){
				if( typeof Shopify.currency.active != 'undefined' ){
					a10x_dl.currency = Shopify.currency.active;
				}
			}
		}
		
		if( typeof meta != 'undefined' && meta){
			if( typeof meta.product != 'undefined' && meta.product){
				if( typeof meta.product.id != 'undefined' && meta.product.id){
					a10x_dl.content_ids = [];
					a10x_dl.content_ids.push(meta.product.id);

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
						a10x_dl.content_name = meta.product.variants[i].name;
					}
					
					if( meta.product.variants[i].price != 'undefined' && meta.product.variants[i].price ){
						
						
						a10x_dl.event_value = meta.product.variants[i].price / 100.00;
						a10x_dl.event_value = (Math.round(a10x_dl.event_value * 100) / 100).toFixed(2);
						a10x_dl.content_value = a10x_dl.event_value;
							
					}
				}
				payload( 'ViewContent', a10x_dl );
			}
		}
		
		
		
	} else if ( mode.includes('woocommerce') ){
		
		if( typeof document.getElementsByClassName('woocommerce')[0] != 'undefined'){		
			if( typeof document.getElementsByClassName('woocommerce-Price-currencySymbol')[0] != 'undefined'){
				var woo_currency_symbol = document.getElementsByClassName('woocommerce-Price-currencySymbol')[0].innerText;
				if( woo_currency_symbol == '£' || woo_currency_symbol == 'GBP' || woo_currency_symbol == '&pound;' || woo_currency_symbol == 'U+000A3' || woo_currency_symbol == '&#xa3;' || woo_currency_symbol == '&#163;' ){
					a10x_dl.currency = 'GBP';
				} else if( woo_currency_symbol == '$' || woo_currency_symbol == 'USD' || woo_currency_symbol == '&dollar;' || woo_currency_symbol == 'U+00024' || woo_currency_symbol == '&#x24;' || woo_currency_symbol == '&#36;' ){
					a10x_dl.currency = 'USD';
				} else if( woo_currency_symbol == 'CAD$' || woo_currency_symbol == 'CAN$' || woo_currency_symbol == 'Can$' || woo_currency_symbol == 'C$' || woo_currency_symbol == 'CAD' ){
					a10x_dl.currency = 'CAD';
				} else if( woo_currency_symbol == 'NZ$' || woo_currency_symbol == 'NZD' ){
					a10x_dl.currency = 'NZD';
				} else if( woo_currency_symbol == 'kr' || woo_currency_symbol == 'SEK' ){
					a10x_dl.currency = 'SEK';
				} else if( woo_currency_symbol == 'Kr.' || woo_currency_symbol == 'DKK' ){
					a10x_dl.currency = 'DKK';
				} else if( woo_currency_symbol == '₩' || woo_currency_symbol == 'KRW' ){
					a10x_dl.currency = 'KRW';
				} else if( woo_currency_symbol == 'ج.س.' || woo_currency_symbol == 'SDG' ){
					a10x_dl.currency = 'SDG';
				} else if( woo_currency_symbol == 'MAD' || woo_currency_symbol == 'DH' ){
					a10x_dl.currency = 'MAD';
				} else if( woo_currency_symbol == 'zł' || woo_currency_symbol == 'PLN' ){
					a10x_dl.currency = 'PLN';
				} else if( woo_currency_symbol == 'CHF' || woo_currency_symbol == 'CHf' || woo_currency_symbol == 'Fr.' || woo_currency_symbol == 'SFr.' ){
					a10x_dl.currency = 'CHF';
				} else if( woo_currency_symbol == 'د.إ' || woo_currency_symbol == 'AED$' || woo_currency_symbol == 'AED' || woo_currency_symbol == 'ar' ){
					a10x_dl.currency = 'AED';
				} else if( woo_currency_symbol == 'A$' || woo_currency_symbol == 'AUS$' || woo_currency_symbol == 'AUS' || woo_currency_symbol == 'AU$' ){
					a10x_dl.currency = 'AUS';
				} else if( woo_currency_symbol == '€' || woo_currency_symbol == 'EUR' || woo_currency_symbol == '&euro;' || woo_currency_symbol == 'U+020AC' || woo_currency_symbol == '&#x20AC;' || woo_currency_symbol == '&#8364;' ){
					a10x_dl.currency = 'EUR';
				} else if( woo_currency_symbol == '¥' || woo_currency_symbol == 'YEN' || woo_currency_symbol == '&yen;' || woo_currency_symbol == 'U+000A5' || woo_currency_symbol == '&#xa5;' || woo_currency_symbol == '&#165;' || woo_currency_symbol == 'JPY' ){
					a10x_dl.currency = 'JPY';
				} else if( woo_currency_symbol == '₱'|| woo_currency_symbol == 'MXN'  || woo_currency_symbol == 'U+020B1' || woo_currency_symbol == '&#x20B1;' || woo_currency_symbol == '&#8369;' ){
					a10x_dl.currency = 'MXN';
				} else if( woo_currency_symbol == '元' || woo_currency_symbol == 'HK$' || woo_currency_symbol == 'HKD'){
					a10x_dl.currency = 'HKD';
				} else if( woo_currency_symbol == '₹' || woo_currency_symbol == 'INR' || woo_currency_symbol == 'U+020B9' || woo_currency_symbol == '&#x20B9;' || woo_currency_symbol == '&#8377;' ){
					a10x_dl.currency = 'INR';
				} 
			}
			
			if( typeof document.querySelectorAll('.order-total')[0] != 'undefined' ){
				var the_total_parent = document.getElementsByClassName('order-total')[0];
				var the_total = the_total_parent.querySelectorAll('.woocommerce-Price-amount')[0].innerText.replace(/[^0-9.]/g, '');			
				if( typeof the_total != 'undefined' ){
					a10x_dl.event_value = the_total;
					a10x_dl.content_value = a10x_dl.event_value;
					p6_set_cookie("gtm_p6_cart_total", the_total, 604800000);
				}
			} else if( typeof document.querySelectorAll('.woocommerce-Price-amount')[0] != 'undefined' ) {
				var the_price = document.querySelectorAll('.woocommerce-Price-amount')[0].innerText.replace(/[^0-9.]/g, '');
				if( typeof the_price != 'undefined' ){
					a10x_dl.event_value = the_price;
					a10x_dl.content_value = a10x_dl.event_value;
					payload( 'ViewContent', a10x_dl );
				}
			}		
		} 
	} else if ( mode.includes('fgx')){
		if( typeof google_tag_manager != 'undefined'){
			if( typeof google_tag_manager['GTM-PN6839'] != 'undefined' && google_tag_manager['GTM-PN6839'] ){
				if( typeof google_tag_manager['GTM-PN6839'].dataLayer != 'undefined' && google_tag_manager['GTM-PN6839'].dataLayer ){
					
					var fgx_cart_total = google_tag_manager['GTM-PN6839'].dataLayer.get('frgxOrderTotal');
					if( typeof fgx_cart_total != 'undefined' && fgx_cart_total){			
						p6_set_cookie("gtm_p6_cart_total", fgx_cart_total,  604800000 );
					}
				}
			} else if( typeof google_tag_manager['GTM-N4M8HQ'] != 'undefined' && google_tag_manager['GTM-N4M8HQ'] ){
				if( typeof google_tag_manager['GTM-N4M8HQ'].dataLayer != 'undefined' && google_tag_manager['GTM-N4M8HQ'].dataLayer ){
					
					var fgx_cart_total = google_tag_manager['GTM-N4M8HQ'].dataLayer.get('pcomOrderTotal');
					if( typeof fgx_cart_total != 'undefined' && fgx_cart_total){			
						p6_set_cookie("gtm_p6_cart_total", fgx_cart_total,  604800000 );
					}
				}
			} 
		}
	} else if ( mode == 'camp'){
		if( typeof dataLayer != 'undefined' ){
			dataLayer.forEach(function(the_val, the_key){
				if( typeof the_val == 'object' ){
					if ('ecommerce' in the_val){
						if( typeof the_val.ecommerce != 'undefined' && the_val.ecommerce){
							if( typeof the_val.ecommerce.detail != 'undefined' ){
								if( typeof the_val.ecommerce.detail.products != 'undefined' ){
									if( typeof the_val.ecommerce.detail.products[0] != 'undefined' ){
										if( the_val.ecommerce.detail.products.length == 1 ){
											
											if( typeof the_val.ecommerce.detail.products[0].name != 'undefined' ){	
												a10x_dl.content_name = the_val.ecommerce.detail.products[0].name;
											}
											if( typeof the_val.ecommerce.detail.products[0].product_id != 'undefined' ){	
												a10x_dl.content_ids = [];
												a10x_dl.content_ids.push(the_val.ecommerce.detail.products[0].product_id);
											}
											if( typeof the_val.ecommerce.detail.products[0].price != 'undefined' ){	
												a10x_dl.content_value = the_val.ecommerce.detail.products[0].price;
												a10x_dl.event_value = the_val.ecommerce.detail.products[0].price;
											}
											payload( 'ViewContent', a10x_dl );
										}
									}
								}
							}
						}
					}
				}
			});
		}
	} else if ( mode == 'pela'){
		if( typeof dataLayer != 'undefined' ){
			dataLayer.forEach(function(the_val, the_key){
				if( typeof the_val == 'object' ){
					if ('ecommerce' in the_val){
						if( typeof the_val.ecommerce != 'undefined' && the_val.ecommerce){
							if( typeof the_val.ecommerce.detail != 'undefined' && the_val.event == 'productDetailView'){
								if( typeof the_val.ecommerce.detail.products != 'undefined' ){
									if( typeof the_val.ecommerce.detail.products[0] != 'undefined' ){
										if( the_val.ecommerce.detail.products.length == 1 ){
											
											if( typeof the_val.ecommerce.detail.products[0].name != 'undefined' ){	
												a10x_dl.content_name = the_val.ecommerce.detail.products[0].name;
											}
											if( typeof the_val.ecommerce.detail.products[0].product_id != 'undefined' ){	
												a10x_dl.content_ids = [];
												a10x_dl.content_ids.push(the_val.ecommerce.detail.products[0].product_id);
											}
											if( typeof the_val.ecommerce.detail.products[0].price != 'undefined' ){	
												a10x_dl.content_value = the_val.ecommerce.detail.products[0].price;
												a10x_dl.event_value = the_val.ecommerce.detail.products[0].price;
											}
											payload( 'ViewContent', a10x_dl );
										}
									}
								}
							}
						}
					}
				}
			});
		}
	} else if ( mode.includes('tourparavel') ){
		if( typeof a10x_dl != 'undefined' ){
			if (run_loop == true ){
				var cart_id = p6_get_cookie('cart');
				if( typeof cart_id != 'undefined' && cart_id ){
					run_loop = false;
					if( cart_id != a10x_dl.sh_cart_id ){
						session_sync(a10x_dl.s_id, 'sh_cart_id', cart_id);
						a10x_dl.sh_cart_id = cart_id;
						p6_set_cookie('gtm_p6_sh_cart_id', cart_id, 86400000);//1d
					}	
				}			
			}
			window.addEventListener("mousedown", () => {
				if( run_loop ){
					var cart_id = p6_get_cookie('cart');
					if( typeof cart_id != 'undefined' && cart_id ){
						run_loop = false;
						if( cart_id != a10x_dl.sh_cart_id ){
							session_sync(a10x_dl.s_id, 'sh_cart_id', cart_id);
							a10x_dl.sh_cart_id = cart_id;
							p6_set_cookie('gtm_p6_sh_cart_id', cart_id, 86400000);//1d
						}	
					}			
				}
			})
			window.addEventListener("touchstart", () => {
				if( run_loop ){
					var cart_id = p6_get_cookie('cart');
					if( typeof cart_id != 'undefined' && cart_id ){
						run_loop = false;
						if( cart_id != a10x_dl.sh_cart_id ){
							session_sync(a10x_dl.s_id, 'sh_cart_id', cart_id);
							a10x_dl.sh_cart_id = cart_id;
							p6_set_cookie('gtm_p6_sh_cart_id', cart_id, 86400000);//1d
						}	
					}
				}
			})
		}
	} else if ( mode == 'tru_mpg' ){
		if( typeof google_tag_manager != 'undefined'){
			if (typeof google_tag_manager['GTM-PS7SL57'] != 'undefined' && google_tag_manager['GTM-PS7SL57'] ){
				if(  a10x_dl.event_type == "AddToCart" ){
					tag.id = tag.id.replace("lnkAddToCart", "lblPrice")
					let tag_replace_value = document.getElementById(tag.id).innerText
					tag_replace_value = parseInt(tag_replace_value.replace("$", ""))
					a10x_dl.event_value = tag_replace_value
					event_value = tag_replace_value
				}
			}
		}
	} else if(  mode.includes('melon') ){
		
		var page = window.location.pathname;
			
		if( typeof document.getElementById('currency_switcher')[0].value != 'undefined'){	
			a10x_dl.currency = document.getElementById('currency_switcher')[0].value;
		}
		
		if( typeof document.getElementsByClassName('woocommerce-Price-amount')[0] != 'undefined'){	
			var price_element = document.getElementsByClassName('woocommerce-Price-amount')[0];
            if( typeof price_element.dataset != 'undefined' ){
                if( typeof price_element.dataset.price != 'undefined' ){
                    a10x_dl.event_value = parseFloat( price_element.dataset.price ).toFixed(2);
                    a10x_dl.content_value = a10x_dl.event_value;
                }
            }
		}
        

		if( typeof dataLayer != 'undefined' ){
			dataLayer.forEach(function(the_val, the_key){
				if( typeof the_val == 'object' ){
                    for (const the_key2 in the_val) {
                       if(typeof the_val[the_key2].ecomm_prodid != 'undefined'){

                           if( typeof a10x_dl.content_ids != 'object' ){
                                a10x_dl.content_ids = [];
                           }
                           a10x_dl.content_ids.push(the_val[the_key2].ecomm_prodid);
                        }
                        if(typeof the_val[the_key2].page_title != 'undefined'){
                            a10x_dl.content_name = the_val[the_key2].page_title;
                        }
					}
                }
			});
		}
		
		if( typeof document.querySelectorAll('.order-total')[0] != 'undefined' ){
			var the_total_parent = document.getElementsByClassName('order-total')[0];
			var last_element_index = document.getElementsByClassName('order-total').length - 1;
			var the_total = the_total_parent.querySelectorAll('.woocommerce-Price-amount')[last_element_index];	
			if( typeof the_total.dataset != 'undefined' ){
                if( typeof the_total.dataset.price != 'undefined' ){
                    the_total = parseFloat( the_total.dataset.price ).toFixed(2);
                    a10x_dl.event_value = the_total;
                }
            }		
			if( typeof the_total != 'undefined' ){
				p6_set_cookie("gtm_p6_cart_total", the_total, 604800000);
				
				
				
				if( page.includes( 'cart' ) ){
					payload( 'AddToCart', a10x_dl );
				} else if( page.includes( 'checkout' ) ){
					payload( 'InitiateCheckout', a10x_dl );
				} else {
				}
			}
		} else if( typeof document.querySelectorAll('.woocommerce-Price-amount')[0] != 'undefined' ) {
			var the_price = document.querySelectorAll('.woocommerce-Price-amount')[0].innerText.replace(/[^0-9.]/g, '');
			if( typeof the_price != 'undefined' ){
				payload( 'ViewContent', a10x_dl );
			}
		}
		
		if( page.includes( 'order-received' ) ){
			a10x_dl.event_value = p6_get_cookie("gtm_p6_cart_total");
			payload( 'Purchase', a10x_dl );
		}
	} else if(  mode.includes('ruokaboksi') ){	
		a10x_dl.currency = "EUR"
		if( typeof document.getElementsByClassName('woocommerce-Price-amount')[0] != 'undefined'){	
							the_price =  document.querySelectorAll('.woocommerce-Price-amount')[0].innerText.replace(/[^0-9.\,]/g, '');
							the_price = the_price.split(',')
							if (the_price.length > 1){
								the_price = the_price[0]
							}
							a10x_dl.event_value = parseFloat(the_price).toFixed(2)
							a10x_dl.content_value = a10x_dl.event_value;
							p6_set_cookie("gtm_p6_cart_total", a10x_dl.event_value, 604800000);
		}
		if (typeof window.dataLayer != "undefined" && window.dataLayer){
			window.dataLayer.push(
				{'event': 'pop6_loaded'}
			);
		}
	} else if (mode.includes('color_camp')){
		if (custom_event_fire == false){
			window.addEventListener("click", function(){
				color_camp_add_to_cart()
			})
			custom_event_fire = true
		}
		if( typeof dataLayer != 'undefined' ){
			for (let i = dataLayer.length - 1 ; i >= 0; i--) {
				if(typeof dataLayer[i][1] != 'undefined'){ 
					if (dataLayer[i][1] == 'view_item'){
						if(typeof dataLayer[i][2] != 'undefined'){
							if(typeof dataLayer[i][2]['items'] != 'undefined'){
								if(typeof dataLayer[i][2]['items'][0] != 'undefined'){
									a10x_dl.content_name = dataLayer[i][2]['items'][0]['name'];
									a10x_dl.content_ids = [];
									a10x_dl.content_ids.push(dataLayer[i][2]['items'][0]['id']);
									a10x_dl.content_value = dataLayer[i][2]['items'][0]['price'];
									a10x_dl.event_value = dataLayer[i][2]['items'][0]['price'];
									payload( 'ViewContent', a10x_dl );

								}
							}
						}
						break
					}
				}
			}
		}
	}

}
function color_camp_add_to_cart () {
	if( typeof dataLayer != 'undefined' ){
		if ( dataLayer.length > 0){
			if(typeof dataLayer[dataLayer.length - 1][1] != 'undefined'){
				var ii = dataLayer.length - 1
				if (dataLayer[ii][1] == 'add_to_cart'){
						if (ii != datalayer_arg_num){
							a10x_dl.content_name = dataLayer[ii][2]['items'][0]['name'];
							a10x_dl.content_ids = [];
							a10x_dl.content_ids.push(dataLayer[ii][2]['items'][0]['id']);
							a10x_dl.content_value = dataLayer[ii][2]['items'][0]['price'];
							a10x_dl.event_value = dataLayer[ii][2]['items'][0]['price'];
							datalayer_arg_num = ii
							payload( 'AddToCart', a10x_dl );
						}
					}
			}
		}
	}
}
function payload( event_type, datalayer ){
		
	if( typeof datalayer != 'undefined' ){
		
				
		datalayer.event_type = event_type;
		//args.event_value = event_value;
		
		
		if( event_type == "Purchase" || event_type == "Purchasetest" ){
			var cart_total = p6_get_cookie("gtm_p6_cart_total");
			if( typeof cart_total != 'undefined' ){
				if( parseInt(cart_total) > 1 ){
					datalayer.event_value = cart_total;
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
				let data = JSON.parse(xmlHttp.response);
			}
		}
		let argString = new URLSearchParams(Object.entries(datalayer)).toString()
		xmlHttp.open("POST", ping_base + 'pop6_ping_capi.php', true); 
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		xmlHttp.send(argString); 
		
	} 	
}

function ping_capi( event_type, event_value, currency = '', custom_data = '' ){
		
	if( typeof a10x_dl != 'undefined' ){
		
		a10x_dl.event_type = event_type;
		a10x_dl.event_value = event_value;
		if( currency !== '' ){
			a10x_dl.currency = currency;
		}
		if( custom_data !=='' ){
			a10x_dl.custom_data = custom_data;
		}
		
		if( event_type == "Purchase" || event_type == "Purchasetest"){
			var cart_total = p6_get_cookie("gtm_p6_cart_total");
			if( typeof cart_total != 'undefined' ){
				if( parseInt(cart_total) > 1 ){
					a10x_dl.event_value = cart_total;
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
				let data = JSON.parse(xmlHttp.response);
			}
		}
		let argString = new URLSearchParams(Object.entries(a10x_dl)).toString()
		xmlHttp.open("POST", ping_base + 'pop6_ping_capi.php', true); 
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		xmlHttp.send(argString); 
		
	} 	
}

function session_sync(the_session_id, the_key, the_value){
	var args = {};
	args.session_id = the_session_id;
	args.passed_key = the_key;
	args.passed_value = the_value;
	
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
	xmlHttp.open("POST", ping_base + 'pop6_sync.php', true); 
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
	xmlHttp.send(argString);
}

function process_session(data, path){
	//grab session id and store in a cookie with 60 day expiration
	if( typeof data.session_id != 'undefined' && data.session_id){
		p6_set_cookie('gtm_p6_s_id', data.session_id, 8035200000); //3m
		a10x_dl.s_id = data.session_id;
	}
	
	
	
	//session_source
	if( typeof data.session_source != 'undefined' && data.session_source){
		var s_source = data.session_source	
		
		if( s_source == 'new' ){
			//if session source is new, just grab session id and create fields with null values
			a10x_dl.fn = null;
			a10x_dl.ln = null;
			a10x_dl.em = null;
			a10x_dl.ph = null;
			a10x_dl.fbp = null;
			a10x_dl.fbc = null;
			a10x_dl.sh_cust_id = null;
			a10x_dl.sh_cart_id = null;
			
		} else if( s_source == 'db' ){
			
			if( typeof data.session_data != 'undefined'){
				
				var loop_keys = [
					'fn', 'ln', 'em' ,'ph', 'fbp', 'fbc', 'sh_cust_id', 'sh_cart_id'
				];
				
				loop_keys.forEach( function(the_val, the_key){
					if( typeof data.session_data[the_val] != 'undefined' && data.session_data[the_val]){
						a10x_dl[the_val] = data.session_data[the_val];
						if( the_val == 'fbc'){
							p6_set_cookie('gtm_p6_fb_c_s', data.session_data[the_val], 8035200000); //3m
						} else if( the_val == 'fbp'){
							//p6_set_cookie('gtm_p6_fb_p_s', data.session_data[the_val], 2678400000); //1m
						} else {
							//p6_set_cookie('gtm_p6_' + the_val, data.session_data[the_val], 5356800000); //2m
						}
						
					} else {
						a10x_dl[the_val] = null;
					}
				});
			}
			
		} else {
			console.log('session source wasnt "new" or "db".');
		}

		pop6_init_03();
		
	} else {
		console.log('no session source passed.');
	}
}

function sync_cart(){
	if( typeof a10x_dl != 'undefined'){
		if (run_loop == true ){
			var cart_id = p6_get_cookie('cart');
			if( typeof cart_id != 'undefined' && cart_id ){
				run_loop = false;
				if( cart_id != a10x_dl.sh_cart_id ){
					session_sync(a10x_dl.s_id, 'sh_cart_id', cart_id);
					a10x_dl.sh_cart_id = cart_id;
					p6_set_cookie('gtm_p6_sh_cart_id', cart_id, 86400000);//1d
				}	
			}			
			setTimeout(() => {
				sync_cart()
			}, 1000);
		}
	} else {
		console.log(red_dot + pop6 + ' Error: 105');
	}
}


function custom_get_value(event_type, event_value, tag){
	
	if( a10x_dl.metadata_mode == 'tru_mpg' && event_type == 'AddToCart'){
		tag.id = tag.id.replace("lnkAddToCart", "lblPrice")
		let tag_replace_value = document.getElementById(tag.id).innerText
		tag_replace_value = parseInt(tag_replace_value.replace("$", ""))
		event_value = tag_replace_value
	}
	
	ping_capi( event_type,  event_value );
}

function get_ip(){
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlHttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			let data = JSON.parse(xmlHttp.response);
			return data.IPv4;
		}
	}

	xmlHttp.open("POST", 'https://geolocation-db.com/json/', true);
	xmlHttp.send(); 
}

function p6_get_cookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

function p6_set_cookie(name, value, milliseconds) {
	//60000 = one minute
	//3600000 = one hour
	//86400000 = one day
	//604800000 = one week
	//2678400000 = one month / 30 days
	//5356800000 = two months / 60 days
	//8035200000 = three months / 90 days
	
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

function objectMap(object, mapFn) {
  return Object.keys(object).reduce(function(result, key) {
    result[key] = mapFn(object[key])
    return result
  }, {})
}