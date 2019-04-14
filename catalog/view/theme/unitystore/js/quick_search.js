function doquick_search( ev, keywords, category_id, cl) {
	if( ev.keyCode == 38 || ev.keyCode == 40 ) {
		return false;
	}
	$('#quick_search_results').remove();
	updown = -1;

	if( keywords == '' || keywords.length < 1 ) {
		return false;
	}
	var parent_category_id;
	if (category_id) {
		parent_category_id = '&category_id=' + category_id;
	} else {
		parent_category_id = '';
	}
	
	$.ajax({url: $('base').attr('href') + 'index.php?route=product/ajaxsearch/ajax' + parent_category_id + '&keyword=' + keywords, dataType: 'json', success: function(result) {
		if( result.length > 0 ) {
				var html;
				html = '';
				html += '<ul id="quick_search_results">';
				for( var i in result ) {
					html += '<li>';
					html += 	'<a href="' + result[i].href + '">';
					html += 		'<img src="' + result[i].thumb + '" alt="" title="" />';
					html += 		'<span>' + result[i].name + '</span>';
					if (result[i].sku) {
						html += 		'<span class="pull-right" style="margin-top: 10px; color: #aaaaaa;">' + result[i].sku + '</span>';
					}
					html += 	'</a>';
					html += '</li>';
				}
				html += '</ul>';
				if( $('#quick_search_results').length > 0 ) {
					$('#quick_search_results').remove();
				}
				$(cl + ' #search').append(html);
		}
	}});

	return true;
}
function upDownEvent(ev, clas) {
	var elem = document.getElementById('quick_search_results');
	var fkey = $(clas + ' #search').find('[name=search]').first();

	if( elem ) {
		var length = elem.childNodes.length - 1;

		if( updown != -1 && typeof(elem.childNodes[updown]) != 'undefined' ) {
			$(elem.childNodes[updown]).removeClass('lisves');
		}

		if( ev.keyCode == 38 ) {
			updown = ( updown > 0 ) ? --updown : updown;
		}
		else if( ev.keyCode == 40 ) {
			updown = ( updown < length ) ? ++updown : updown;
		}

		if( updown >= 0 && updown <= length ) {
			$(elem.childNodes[updown]).addClass('lisves');

			var text = elem.childNodes[updown].childNodes[0].text;
			if( typeof(text) == 'undefined' ) {
				text = elem.childNodes[updown].childNodes[0].innerText;
			}

			$(clas + ' #search').find('[name=search]').first().val( new String(text).replace(/(\s\(.*?\))$/, '') );
		}
	}

	return false;
}

var updown = -1;
function func(clas1) {
	$(clas1).find('#search input[type=\'text\']').keyup(function(ev){
		var category_id = $(clas1 + ' #search .select-wrap input[name=\'category_id_header\']').val();
		doquick_search(ev, this.value, category_id, clas1);
	}).focus(function(ev){
		var category_id = $(clas1 + ' #search .select-wrap input[name=\'category_id_header\']').val();
		doquick_search(ev, this.value, category_id, clas1);
	}).keydown(function(ev){
		upDownEvent(ev, clas1);
	}).blur(function(){
		window.setTimeout("$('#quick_search_results').remove();updown=0;", 500);
	});
	$(document).bind('keydown', function(ev) {
		try {
			if( ev.keyCode == 13 && $('.lisves').length > 0 ) {
				document.location.href = $('.lisves').find('a').first().attr('href');
			}
		}
		catch(e) {}
	});
}
$(document).ready(function(){
	func('.header_block');
	func('.top_line');
	func('.search_tablet');
});