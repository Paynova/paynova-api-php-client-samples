var callbackCheckerInterval = false;
var orginalCallbacks = {"PAYMENT_1":"","PAYMENT_2":"","PAYMENT_3":"","SESSION_END":"","user_BACK":""}; 
var callbacks = orginalCallbacks;
var jsonCallbacks = orginalCallbacks;
var stopCheckForCallbacksWhenCallbacksNotEmpty = ["PAYMENT_1","SESSION_END","userBACK"];
var countingDown = false;
$( document ).ready(function() {
	//Triggers
	$("#createSimpleOrderButton").click(function(){
		var onr = $("#orderNumber").html();
		if(onr==""){
			showMessage("A merchant orderNumber is not set");
		}else {
			var $btn = $(this);
		    $btn.button('loading');
			createSimpleOrder.execute(onr,"createSimpleOrderResult",$btn);
		}
	});
	
	$("#initializePaymentButton").click(function(){
		var oid = $("#orderId").html();
		if(oid==""){
			showMessage("An order is not created, missing orderId");
		}else {
			var $btn = $(this);
		    $btn.button('loading');
		    initializePayment.execute(oid,"initalizePaymentResult",$btn);
		}
	});
	$("#finalizeAuthorizationButton").click(function(){
		var properties = {orderId:$("#orderId").html(),transactionId:$("#transactonId").html(),amount:$("#amount").html()}
		var message = "";
		$.each(properties,function(key,value){
			if(value=="") {
				message+=key+" have to be set\n";
			}
		});
		if(message!="") {
			alert(message+"Start by Create Order and Initialize Payment");
		}else{
			var $btn = $(this);
		    $btn.button('loading');
		    finalizeAuthorization.execute(properties,"finalizeAuthorizationResult",$btn);
		}
		
	});
	$("#refundPaymentButton").click(function(){
		var properties = {orderId:$("#orderId").html(),transactionId:$("#transactonId").html(),amount:$("#amount").html()}
		var message = "";
		$.each(properties,function(key,value){
			if(value=="") {
				message+=key+" have to be set\n";
			}
		});
		if(message!="") {
			alert(message+"Start by Create Order and Initialize Payment");
		}else{
			var $btn = $(this);
		    $btn.button('loading');
		    refundPayment.execute(properties,"refundPaymentResult",$btn);
		}
		
	});
	
	$("#annulAuthorizationButton").click(function(){
		var properties = {orderId:$("#orderId").html(),transactionId:$("#transactonId").html(),amount:$("#amount").html()}
		var message = "";
		$.each(properties,function(key,value){
			if(value=="") {
				message+=key+" have to be set\n";
			}
		});
		if(message!="") {
			alert(message+"Start by Create Order and Initialize Payment");
		}else{
			var $btn = $(this);
		    $btn.button('loading');
		    annulAuthorization.execute(properties,"annulAuthorizationResult",$btn);
		}
		
	});
	$("#getCustomerProfileButton").click(function(){
		var properties = {profileId:$("#profileId").html()};
		var message = "";
		$.each(properties,function(key,value){
			if(value=="") {
				message+=key+" have to be set\n";
			}
		});
		if(message!="") {
			alert(message+"Start by Create Order and Initialize Payment");
		}else{
			var $btn = $(this);
		    $btn.button('loading');
		    getCustomerProfile.execute(properties,"getCustomerProfileResult",$btn);
		}
		
	});
	$("#removeCustomerProfileCardButton").click(function(){
		var properties = {profileId:$("#profileId").html(),cardId:$("#cardId").html()};
		var message = "";
		$.each(properties,function(key,value){
			if(value=="") {
				message+=key+" have to be set\n";
			}
		});
		if(message!="") {
			alert(message+"Start by Get Customer Profile");
		}else{
			var $btn = $(this);
		    $btn.button('loading');
		    removeCustomerProfileCard.execute(properties,"removeCustomerProfileCardResult",$btn);
		}
		
	});
	$("#removeCustomerProfileButton").click(function(){
		var properties = {profileId:$("#profileId").html()};
		var message = "";
		$.each(properties,function(key,value){
			if(value=="") {
				message+=key+" have to be set\n";
			}
		});
		if(message!="") {
			alert(message+"Start by Get Customer Profile");
		}else{
			var $btn = $(this);
		    $btn.button('loading');
		    removeCustomerProfile.execute(properties,"removeCustomerProfileResult",$btn);
		}
		
	});
	
	
	$("#orderNumberGenerator").click(function(){
		$.ajax({
			url: "index.php?ajax=util",
			type:"POST",
			data: {"action":"random-order-number"},
			success:function(data) {
				jQuery("#orderNumber").html(data.orderNumber);
			}
		});
	});
	$("#clearAllProperties").click(function(){
		var $btn = $(this);
	    $btn.button('loading');
		clearAllProperties($btn);
	});
	
});
function clearAllProperties(firingButton) {
	reallyStopInterval();
	Util.loadContent("?ajax=util", {"action":"clear-variables"}, function(data){
		 $(firingButton).button('reset');
	    location.reload();
	});
}
function showMessage(message) {
	alert(message);
	//$("#basicModal").modal();
}
function stopInterval() {
	if(!countingDown) {
		countingDown = true;
		setTimeout(function(){
			reallyStopInterval();
			countingDown = false;
			initializePayment.loadPaymentSuccessProperties(1);
		},3000);
	}
}
function reallyStopInterval() {
	clearInterval(callbackCheckerInterval);
	callbackCheckerInterval = false;
}
function callbackCheckingCanStop() {
	var conditionsMet = 0;
	$.each(stopCheckForCallbacksWhenCallbacksNotEmpty, function(key,value){
		if(callbacks[value]!="") {
			conditionsMet++;
		}
	});
	return (conditionsMet == stopCheckForCallbacksWhenCallbacksNotEmpty.length);
}
var Util = {
		/*getPrettified: function(str) {
			return '<pre class="prettyprint lang-java">'+str+'</pre>';
			
		},*/
		display: function(str,element,doAppend) {
			if(doAppend){
				$("#"+element).hide().append(str).fadeIn(500);
			} else{	
				$("#"+element).hide().html(str).fadeIn(500);
			}
		},
		urlDecode: function(str) {
		   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
		},
		loadContent: function(url,json,callback) {
			var contents = "";
			$.ajax({
				type: "POST",
				url: url,
				data: json,
				headers: { 
			        Accept : "application/json"
			    },
				success:callback,
				error: function(jqXHR, textStatus, errorThrown ){
					alert(JSON.stringify(jqXHR));
					alert(textStatus);
					alert(errorThrown);
					reallyStopInterval();
				}
			});
			return contents;
		},
		jsonPrettify: function(json){
			if (typeof json != 'string') {
		         json = JSON.stringify(json, undefined, 2);
		    }
		    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
		        var cls = 'number';
		        if (/^"/.test(match)) {
		            if (/:$/.test(match)) {
		                cls = 'key';
		            } else {
		                cls = 'string';
		            }
		        } else if (/true|false/.test(match)) {
		            cls = 'boolean';
		        } else if (/null/.test(match)) {
		            cls = 'null';
		        }
		        return '<span class="' + cls + '">' + match + '</span>';
		    });
		}
		/*parseAjaxResponse: function(content) {
			return content.split("--SPLIT--");
		}*/
}
var createSimpleOrder = {
		execute: function(onr,targetElement,firingButton){
			var _this = this;
			Util.loadContent("?service=create-and-init",{"action":"create-order", "orderNumber":onr},function(data){
				_this.displayResult(data, targetElement,firingButton);
				
			});
		},
		displayResult: function(data,element,firingButton) {
			if(data.httpCode==502){
				showMessage(data.errorMessage);
			}else { 
				var statusString = Util.jsonPrettify(data.statusObject);
				var html = Util.urlDecode(data.html);
				html = html.replace(/status-here/,statusString);
				Util.display(html,element, false);
				Util.display(data.orderId,"orderId", false);
			}
			
			$(firingButton).button("reset");
			$(firingButton).hide();
		},
		
}
var initializePayment = {
		execute: function(oid,targetElement,firingButton){
			var _this = this;
			Util.loadContent("?service=create-and-init",{"action":"initialize-payment"},function(data){
				_this.displayResult(data, targetElement,firingButton);
				
			});
		},
		displayResult: function(data,element,firingButton) {
			var statusString = Util.jsonPrettify(data.statusObject);
			var html = Util.urlDecode(data.html);
			if(data.url!="") {
				html+=this.getPaymentIframHtml(data.url);
			}
			html = html.replace(/status-here/,statusString);
			Util.display(html,element, false);
			Util.display(data.sessionId,"sessionId", false);
			
			
			
			$(firingButton).button("reset");
			$(firingButton).hide();
		},
		getPaymentIframHtml: function(url){
			var html ='<a class="btn btn-sm btn-primary" role="button" href="javascript:void(0)" onclick="initializePayment.tooglePaymentWindow()">Toggle payment-iframe</a>';
			html+='<iframe src="'+url+'" width="100%" height="500px" id="paymentIframe">Content loading</iframe>';
			return html
		},
		tooglePaymentWindow: function() {
			
			if($("#paymentIframe").is(":visible")){
				$("#paymentIframe").hide();
				stopInterval();
				$(".callbackInfo").hide();
			}else{
				$("#paymentIframe").show();
				$(".callbackInfo").show();
				var sessionId = $("#sessionId").html();
				var tries = 0;
				callbackCheckerInterval = setInterval(function() {
					
					$("#callbackCheckCount").html(++tries);
					var json = {
						"action":"get-callback-info",
						"types":"",
						"sessionId":sessionId
					};
					var allCallbacksReceived = true;
					$.each( callbacks, function( key, value ) {
						if(value==""){
							if(json.types!="")json.types+=",";
							json.types+=key;
							allCallbacksReceived = false;
						}
					});
					if(allCallbacksReceived) {
						stopInterval();
					}
					Util.loadContent("?ajax=util",json,function(data){
						
						jQuery.each(data.html, function(key, value) {
							if(value!="" && key!="user_BACK") {
								callbacks[key]=Util.urlDecode(value);
								jsonCallbacks[key] = data["json"][key];
								$("#callbackResult").append(
										'<div class="variableBox">'+
											'<strong>'+key.replace(/_[1-3]/,"")+'</strong><br />'+
											Util.urlDecode(value)+
										'</div>'
								);
							}
						});
						if(callbackCheckingCanStop()) {
							stopInterval();
						}
					});
				}, 1000)
			}
			//Check if callbacks have been received
			
		},
		loadPaymentSuccessProperties: function(tryNum) {
			var _this = this;
			Util.loadContent("?ajax=util",{"action":"get-payment-success-properties","sessionId":$("#sessionId").html(),"tryNum":tryNum},function(data){
				if(data.status!="Authorized"){
					alert("The simulated transaction " + data.status+". As it should.");
				}else{
					$("#transactionId").html(data.transactionId);
					$("#amount").html(data.amount);
					if(data.hasOwnProperty("message")) {
						showMessage(data.message);
						if(parseInt(data.tryNum)<2){
							_this.loadPaymentSuccessProperties(2);
						}
					}
				}
			});
		}
}
var finalizeAuthorization = {
		execute: function(properties,targetElement,firingButton){
			var json={};
			$.extend(json, {"action":"finalize-authorization"}, properties);
			
			var _this = this;
			Util.loadContent("?service=finalize-authorization",json,function(data){
				_this.displayResult(data, targetElement,firingButton);
				
			});
		},
		displayResult: function(data,element,firingButton) {
			var statusString = Util.jsonPrettify(data.statusObject);
			var html = Util.urlDecode(data.html);
			html = html.replace(/status-here/,statusString);
			Util.display(html,element, false);
			$(firingButton).button("reset");
		}
}
var refundPayment = {
	execute: function(properties,targetElement,firingButton){
		var json={};
		$.extend(json, {"action":"refund-payment"}, properties);
		
		var _this = this;
		Util.loadContent("?service=refund-payment",json,function(data){
			_this.displayResult(data, targetElement,firingButton);
			
		});
	},
	displayResult: function(data,element,firingButton) {
		var statusString = Util.jsonPrettify(data.statusObject);
		var html = Util.urlDecode(data.html);
		html = html.replace(/status-here/,statusString);
		Util.display(html,element, false);
		$(firingButton).button("reset");
	}
}
var annulAuthorization = {
		execute: function(properties,targetElement,firingButton){
			var json={};
			$.extend(json, {"action":"annul-authorization"}, properties);
			
			var _this = this;
			Util.loadContent("?service=annul-authorization",json,function(data){
				_this.displayResult(data, targetElement,firingButton);
				
			});
		},
		displayResult: function(data,element,firingButton) {
			
			var html = Util.urlDecode(data.html);
			if(data.hasOwnProperty("statusObject")) {
				var statusString = Util.jsonPrettify(data.statusObject);
				html = html.replace(/status-here/,statusString);
			}
			Util.display(html,element, false);
			$(firingButton).button("reset");
		}
}
var getCustomerProfile = {
		execute: function(properties,targetElement,firingButton){
			var json={};
			$.extend(json, {"action":"get-customer-profile"}, properties);
			
			var _this = this;
			Util.loadContent("?service=get-customer-profile",json,function(data){
				_this.displayResult(data, targetElement,firingButton);
				
			});
		},
		displayResult: function(data,element,firingButton) {
			var statusString = Util.jsonPrettify(data.statusObject);
			var html = Util.urlDecode(data.html);
			html = html.replace(/status-here/,statusString);
			Util.display(html,element, false);
			if(data.hasOwnProperty("cardId")) {
				$("#cardId").html(data.cardId);
			}else{
				alert("No card existed in the profile. Create Order and Initialize Payment and store a card");
			}
			$(firingButton).button("reset");
		}
}
var removeCustomerProfileCard = {
		execute: function(properties,targetElement,firingButton){
			var json={};
			$.extend(json, {"action":"remove-customer-profile-card"}, properties);
			
			var _this = this;
			Util.loadContent("?service=remove-customer-profile-card",json,function(data){
				_this.displayResult(data, targetElement,firingButton);
				
			});
		},
		displayResult: function(data,element,firingButton) {
			if(data.hasOwnProperty("removeCard") && data.removeCard=="1") {
				$("#cardId").html("");
			}
			var html = Util.urlDecode(data.html);
			if(data.hasOwnProperty("statusObject")) {
				var statusString = Util.jsonPrettify(data.statusObject);
				html = html.replace(/status-here/,statusString);
			}
			Util.display(html,element, false);
			$(firingButton).button("reset");
		}
}
var removeCustomerProfile = {
		execute: function(properties,targetElement,firingButton){
			var json={};
			$.extend(json, {"action":"remove-customer-profile"}, properties);
			var _this = this;
			Util.loadContent("?service=remove-customer-profile-card",json,function(data){
				_this.displayResult(data, targetElement,firingButton);
				
			});
		},
		displayResult: function(data,element,firingButton) {
			var html = Util.urlDecode(data.html);
			if(data.hasOwnProperty("statusObject")) {
				var statusString = Util.jsonPrettify(data.statusObject);
				html = html.replace(/status-here/,statusString);
			}
			Util.display(html,element, false);
			$(firingButton).button("reset");
			if(data.hasOwnProperty("removeProfile") && data.removeProfile=="1") {
				alert("Profile removed from API server but will remain in the Properties-box");
			}
		}
}