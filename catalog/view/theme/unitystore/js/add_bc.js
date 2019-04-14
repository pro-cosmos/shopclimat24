function add_bc_featured(product_id) {
	data = $('#option1_'+product_id+' input[type=\'radio\']:checked, #option1_'+product_id+' input[type=\'checkbox\']:checked, #option1_'+product_id+' select');
	$.ajax({
		url: 'index.php?route=checkout/cartopenstore/add',
		type: 'post',
		data: data.serialize() + '&product_id=' + product_id,
		dataType: 'json',
		beforeSend: function() {
			$('#cart > button').button('loading');
		},
		complete: function() {
			$('#cart > button').button('reset');
		},
		success: function(json) {
			$('.message-success, .success, .warning, .attention, information, .error').remove();
			if (json['error']) {
				if (json['error']['warning']) {
					$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/unitystore/image/close.png" alt="" class="close" /></div>');
				
					$('.warning').fadeIn('slow');
				}
				for (i in json['error']) {
					$('#option1-' + i).after('<span class="error">' + json['error'][i] + '</span>');
				}
			}
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option1-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			}		
			if (json['success']) {
				$('#message-success').after('<div class="message-success" style="display: none;">' + json['success'] + '</div>');
				setTimeout(function () {$('#cart > button').html('<span id="cart-total">' + json['total'] + '</span>');}, 100);
				$('.message-success').fadeIn(500).delay(4000).fadeOut(1000);
				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}	
		}
	});
}
function add_bc_bestseller(product_id) {
	data = $('#option2_'+product_id+' input[type=\'radio\']:checked, #option2_'+product_id+' input[type=\'checkbox\']:checked, #option2_'+product_id+' select');
	$.ajax({
		url: 'index.php?route=checkout/cartopenstore/add',
		type: 'post',
		data: data.serialize() + '&product_id=' + product_id,
		dataType: 'json',
		beforeSend: function() {
			$('#cart > button').button('loading');
		},
		complete: function() {
			$('#cart > button').button('reset');
		},
		success: function(json) {
			$('.message-success, .success, .warning, .attention, information, .error').remove();
			if (json['error']) {
				if (json['error']['warning']) {
					$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/unitystore/image/close.png" alt="" class="close" /></div>');
				
					$('.warning').fadeIn('slow');
				}
				for (i in json['error']) {
					$('#option2-' + i).after('<span class="error">' + json['error'][i] + '</span>');
				}
			}
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option2-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			}		
			if (json['success']) {
				$('#message-success').after('<div class="message-success" style="display: none;">' + json['success'] + '</div>');
				setTimeout(function () {$('#cart > button').html('<span id="cart-total">' + json['total'] + '</span>');}, 100);
				$('.message-success').fadeIn(500).delay(4000).fadeOut(1000);
				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}	
		}
	});
}
function add_bc_latest(product_id) {
	data = $('#option3_'+product_id+' input[type=\'radio\']:checked, #option3_'+product_id+' input[type=\'checkbox\']:checked, #option3_'+product_id+' select');
	$.ajax({
		url: 'index.php?route=checkout/cartopenstore/add',
		type: 'post',
		data: data.serialize() + '&product_id=' + product_id,
		dataType: 'json',
		beforeSend: function() {
			$('#cart > button').button('loading');
		},
		complete: function() {
			$('#cart > button').button('reset');
		},
		success: function(json) {
			$('.message-success, .success, .warning, .attention, information, .error').remove();
			if (json['error']) {
				if (json['error']['warning']) {
					$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/unitystore/image/close.png" alt="" class="close" /></div>');
				
					$('.warning').fadeIn('slow');
				}
				for (i in json['error']) {
					$('#option3-' + i).after('<span class="error">' + json['error'][i] + '</span>');
				}
			}
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option3-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			}		
			if (json['success']) {
				$('#message-success').after('<div class="message-success" style="display: none;">' + json['success'] + '</div>');
				setTimeout(function () {$('#cart > button').html('<span id="cart-total">' + json['total'] + '</span>');}, 100);
				$('.message-success').fadeIn(500).delay(4000).fadeOut(1000);
				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}	
		}
	});
}
function add_bc_special(product_id) {
	data = $('#option4_'+product_id+' input[type=\'radio\']:checked, #option4_'+product_id+' input[type=\'checkbox\']:checked, #option4_'+product_id+' select');
	$.ajax({
		url: 'index.php?route=checkout/cartopenstore/add',
		type: 'post',
		data: data.serialize() + '&product_id=' + product_id,
		dataType: 'json',
		beforeSend: function() {
			$('#cart > button').button('loading');
		},
		complete: function() {
			$('#cart > button').button('reset');
		},
		success: function(json) {
			$('.message-success, .success, .warning, .attention, information, .error').remove();
			if (json['error']) {
				if (json['error']['warning']) {
					$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/unitystore/image/close.png" alt="" class="close" /></div>');
				
					$('.warning').fadeIn('slow');
				}
				for (i in json['error']) {
					$('#option4-' + i).after('<span class="error">' + json['error'][i] + '</span>');
				}
			}
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option4-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			}		
			if (json['success']) {
				$('#message-success').after('<div class="message-success" style="display: none;">' + json['success'] + '</div>');
				setTimeout(function () {$('#cart > button').html('<span id="cart-total">' + json['total'] + '</span>');}, 100);
				$('.message-success').fadeIn(500).delay(4000).fadeOut(1000);
				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}	
		}
	});
}
function add_bc_list(product_id) {
	data = $('#option10_'+product_id+' input[type=\'radio\']:checked, #option10_'+product_id+' input[type=\'checkbox\']:checked, #option10_'+product_id+' select');
	$.ajax({
		url: 'index.php?route=checkout/cartopenstore/add',
		type: 'post',
		data: data.serialize() + '&product_id=' + product_id,
		dataType: 'json',
		beforeSend: function() {
			$('#cart > button').button('loading');
		},
		complete: function() {
			$('#cart > button').button('reset');
		},
		success: function(json) {
			$('.message-success, .success, .warning, .attention, information, .error').remove();
			if (json['error']) {
				if (json['error']['warning']) {
					$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/unitystore/image/close.png" alt="" class="close" /></div>');
				
					$('.warning').fadeIn('slow');
				}
				for (i in json['error']) {
					$('#option10-' + i).after('<span class="error">' + json['error'][i] + '</span>');
				}
			}
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option10-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			}		
			if (json['success']) {
				$('#message-success').after('<div class="message-success" style="display: none;">' + json['success'] + '</div>');
				setTimeout(function () {$('#cart > button').html('<span id="cart-total">' + json['total'] + '</span>');}, 100);
				$('.message-success').fadeIn(500).delay(4000).fadeOut(1000);
				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}	
		}
	});
}