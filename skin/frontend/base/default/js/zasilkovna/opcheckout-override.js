Review.prototype.save = function(){
	if (checkout.loadWaiting!=false) return;
	checkout.setLoadWaiting('review');
	var params = Form.serialize(payment.form);
	if (this.agreementsForm) {
		params += '&'+Form.serialize(this.agreementsForm);
	}

	params.save = true;

	// ON REVIEW SAVE APPEND PACKETERY DATA
	let idElement = document.getElementById('packeta-branch-id');
	let nameElement = document.getElementById('packeta-branch-name');
	params += (idElement) ? "&packetaId="+idElement.value : "";
	params += (nameElement) ? "&packetaName="+nameElement.value : "";
	// ON REVIEW SAVE APPEND PACKETERY DATA

	new Ajax.Request(
		this.saveUrl,
		{
			method:'post',
			parameters:params,
			onComplete: this.onComplete,
			onSuccess: this.onSave,
			onFailure: checkout.ajaxFailure.bind(checkout)
		}
	);
}

ShippingMethod.prototype.validate = function(){

	var methods = document.getElementsByName('shipping_method');
	if (methods.length==0) {
		alert(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.').stripTags());
		return false;
	}

	if(!this.validator.validate()) {
		return false;
	}

	for (var i=0; i<methods.length; i++) {
		if (methods[i].checked) {
			if (methods[i].value == 'zasilkovna_zasilkovna') {
				var branchId = document.getElementById('packeta-branch-id');
				if(!branchId || !branchId.value)
				{
					alert(Translator.translate('Please choose the pick-up point.'));
					return false;
				}
			}
			return true;
		}
	}
	alert(Translator.translate('Please choose the shipment method.'));
	return false;
}

/**
 * Callback po zavreni widgetu zasilkovny
 * @param {name, id, ..} point 
 */
function showSelectedPickupPoint(point)
{
	var pickedDeliveryPlace = document.getElementById('picked-delivery-place');
	var packetaBranchId = document.getElementById('packeta-branch-id');
	var packetaBranchName = document.getElementById('packeta-branch-name');
	
	if(packetaBranchId && packetaBranchName && pickedDeliveryPlace)
	{
		packetaBranchId.value = null;
		packetaBranchName.value = null;
		pickedDeliveryPlace.innerText = "";
		
		if(point)
		{
			pickedDeliveryPlace.innerText = (point ? point.name : "");
			packetaBranchId.value = point.id;
			packetaBranchName.value = point.name;
			var inputMethod = document.querySelectorAll('input[value="zasilkovna_zasilkovna"]');
			
			if(inputMethod.length == 1)
			{
				inputMethod[0].checked = true;	
			}
		}
	}
};

