function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

function bluring() {
	$('.modal-bg').addClass('show');
	$('body .divshadow').removeClass('animated').removeClass('bounceOut').removeClass('bounceIn').addClass('animated bounceIn');
}
function closedivshadow(){
	$('body .divshadow').addClass('divshadow').addClass('animated bounceOut');
	setTimeout(function() {
		$('body .divshadow').empty();
		$('body .divshadow').removeClass('show');
		$('.modal-bg').removeClass('show');
		$('body .divshadow').removeClass().addClass('divshadow');
	},700)
}
function closeshadow(){
	$('.modal-bg').removeClass('show');
}
function blurg() {
	if ($("body").hasClass("bluring")) {
		$('body').removeClass('bluring');
		$('.modal-bg').removeClass('show').removeClass('opacity');
	} else {
		$('body').addClass('bluring');
		$('.modal-bg').addClass('show').addClass('opacity');
	}
}
function getajaxcategorypage(href) {
    $.ajax({
        url: 'index.php?',
		type:'get',
		data: href,
		success: function(msg){
			$('.categorycarousel').empty();
			$('.categorycarousel').append(msg);
		}
    });
}
function getajaxcallbacking(href) {
    $.ajax({
        url: 'index.php?',
		type:'get',
		data: href,
		success: function(msg){
			setTimeout(function() {
			$('body .divshadow').empty();
			$('body .divshadow').append(msg);
			
			if ($('body .divshadow').hasClass("show")) {
				$('body .divshadow').removeClass('show');
			} else {
				$('body .divshadow').addClass('show');
				$('.modal-bg').addClass('show');
			}
			bluring();
			},700)
		}
    });
}
function getajax(href, shadow) {
    $.ajax({
        url: 'index.php?',
		type:'get',
		data: href,
		success: function(msg){
			setTimeout(function() {
			$('body .divshadow').empty();
			$('body .divshadow').append(msg);
			if (shadow == 'on') {
				if ($('body .divshadow').hasClass("show")) {
					$('body .divshadow').removeClass('show');
				} else {
					$('body .divshadow').addClass('show');
					$('.modal-bg').addClass('show');
				}
				bluring();
			}
			},700)
		}
    });
}
function ajaxpopup(href) {
    $.ajax({
        url: href,
		type:'get',
		success: function(msg){
			setTimeout(function() {
			$('body .divshadow').empty();
			var itog_msg = '<div class="option-div"><div class="close"><i class="fa fa-times"></i></div>' + msg + '</div>';
			$('body .divshadow').append(itog_msg);
			if ($('body .divshadow').hasClass("show")) {
				$('body .divshadow').removeClass('show');
			} else {
				$('body .divshadow').addClass('show');
				$('.modal-bg').addClass('show');
			}
			bluring();
			},700)
		}
    });
}
function ajaxmoremodule(href, iden) {
    $.ajax({
        url: 'index.php?route=extension/module/divshadow',
		type:'get',
		data: href,
		beforeSend: function() {
			$('.product-thumb' + iden).addClass('load');
		},
		success: function(msg){
			setTimeout(function() {
			$('body .divshadow').empty();
			$('body .divshadow').append(msg);
			if ($('body .divshadow').hasClass("show")) {
				$('body .divshadow').removeClass('show');
			} else {
				$('body .divshadow').addClass('show');
				$('.modal-bg').addClass('show');
			}
			bluring();
			$('.product-thumb' + iden).removeClass('load');
			},700)
		}
    });
}

function schetInner(clas) {
	clastemp = clas.replace(' >', '').replace(' >', '').replace(' >', '');
	var blockwidth = $(clas).parent().innerWidth();
	var innwidth = 0;
	var schet = 0;
	$(clastemp).each(function(){
		innwidth += $(this).width();
		if (innwidth > blockwidth) {
			schet += 1;
		}
	});
	
	var indexLast = $(clastemp).length - schet;
	
	
	var array_temp = [];
	
	$(clastemp).each(function(i){
		array_temp[i] = $(this).html();
	});
    var width_result = 0;
	array_temp.forEach(function(item, i) {
		width_result = $(clastemp + '[data-id=\'' + i + '\']').width() + width_result;
		if (width_result > blockwidth) {
			$(clastemp + '.more_articles ul').append($(clastemp + '[data-id=\'' + i + '\']'));
		} else {
			$(clastemp + '.more_articles').before($(clastemp + '.more_articles ul li[data-id=\'' + i + '\']'));
		}
		
	});
	if ($('.more_articles .dropdown-menu').text() === '') {
		$('.more_articles .fa').hide();
	} else {
		$('.more_articles .fa').show();
	}
}

function updateCart() {
	if ($('button #cart-total').text() == '0') {
		$('#top-links .fa-shopping-cart').after('<span id="cart-total" class="visible-xs-inline-block">&nbsp;&nbsp;(' + $('button #cart-total').text() + ')</span>');
	} else {
		$('#top-links .fa-shopping-cart').after('<span id="cart-total" class="visible-xs-inline-block text-danger">&nbsp;&nbsp;(' + $('button #cart-total').text() + ')</span>');
	}
}
$(document).ready(function() {
	function AllHeight(clas) {
		itog_height = 0;
		$(clas).each(function() {
			if (itog_height < $(this).outerHeight()) {
				itog_height = $(this).outerHeight();
			}
		});
		$(clas).css('height', itog_height);
	}
	setTimeout(function() {
		AllHeight('.list-category ul li');
	}, 0)
	$('.navbar_category .dropdown-menu .tab-content .list-unstyled').css('min-width', ($('#content').width() + 25)/3);
	schetInner('.bg-header .articles > ul > li');
	$(window).resize(function() {
		$('.navbar_category .dropdown-menu .tab-content .list-unstyled').css('min-width', ($('#content').width() + 25)/3);
		schetInner('.bg-header .articles > ul > li');
	});
	if ($('.product-layout').hasClass('product-list')) {
		$('.product-thumb .rating').addClass('trapezium10');
	}

	updateCart();
	
	$('.stiker-module-popular, .stiker-module-new, .stiker-module-special').wrapInner('<span class="trapezium10"></span>');
	$('.stiker-popular-product, .stiker-new-product, .stiker-special-product').wrapInner('<span class="trapezium10"></span>');

	$('.category > .nav').hover(function() {
		if ($(window).width() > 767) {
			bluring();
		}
		
	},
	function() {
		closeshadow();
	}
	);
	$('.product-thumb').hover(function() {
		if ($(this).parent().hasClass('product-list') || $(this).parent().hasClass('product-price')) {}  else {
			var _this = $(this).find('.chevron');
			_this.removeClass('bounceOut');
			_this.addClass('animated');
			_this.addClass('bounceIn');
		}
    }, function() {
		if ($(this).parent().hasClass('product-list') || $(this).parent().hasClass('product-price')) {}  else {
			var _this = $(this).find('.chevron');
			_this.removeClass('bounceIn');
			_this.addClass('bounceOut');
		}
    });
	$('.category.column_position > ul > li > ul > li').hover(function() {
		
		var width_1 = $(this).parent().parent().find('.child_1').outerWidth();
		var width_2 = $(this).find('.child_2').outerWidth();
		var widthobsh = parseInt(width_1) + parseInt(width_2) + 10;
		
		$(this).parent().parent().find('.child_1').css("width", widthobsh + "px");
        
        var height_1 = $(this).parent().parent().find('.child_1').outerHeight() + 10;
        
        $(this).find('.child_2').css("bottom", "auto");
        
        var height_2 = $(this).find('.child_2').outerHeight() + 10;
		
		if (height_2 > height_1) {
		    
			$(this).parent().parent().find('.child_1').css("height", height_2 + "px");
            
		} else {
		    $(this).find('.child_2').css("bottom", "10px");
		}
		
	},
	function() {
		
		var width_1 = $(this).parent().parent().find('.child_1').outerWidth();
		var width_2 = $(this).find('.child_2').outerWidth();
		var widthobsh = parseInt(width_1) - parseInt(width_2) - 10;
		
		$(this).parent().parent().find('.child_1').css("width", widthobsh + "px");
		
	}
	);
	
});
$(document).ready(function() {
	
	$('#column-left').css('margin-top', $('.navbar-category-collapse > .list-group.category ul.nav').height());
	
	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();
		
		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});
		
	// Currency
	$('#form-currency .currency-select').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').val($(this).attr('name'));

		$('#form-currency').submit();
	});

	// Language
	$('#form-language .language-select').on('click', function(e) {
		e.preventDefault();

		$('#form-language input[name=\'code\']').val($(this).attr('name'));

		$('#form-language').submit();
	});
	
	$('a.scrolling').click(function () {
        $('body,html').animate({scrollTop: 0}, 500);
        return false;
    });
	
	
	$('body').on('click', '#search ul li', function () {
		$('#search .select-wrap button.search span').text($(this).text());
		$('#search input[name=\'category_id_header\']').val($(this).attr('data-id'));
		$('#search .select-wrap button span').css('width', 'auto');
		$('#search .select-wrap button span + span').remove();
	});
	

	$('body').on('click', '#top_banner .close_banner', function () {
		getajaxcookie('topbanner');
	});
	$('body').on('click', '.subscribe .close', function () {
		getajaxcookie('bannerpopup');
	});
	function getajaxcookie(perem) {
		$.ajax({
			url: 'index.php?route=common/cookie/' + perem,
			type:'get',
			success: function(){
				if (perem == 'topbanner') {$('#top_banner').remove();}
				if (perem == 'bannerpopup') {$('.option-div.subscribe').remove();}
			}
		});
	}
	
	setTimeout(function(){
		$("#cart.btn-group.btn-block button img").addClass("visible animated zoomIn");
	}, 500);
	setTimeout(function(){
		$(".bg-header #logo img").addClass("visible animated zoomIn");
	}, 100);
	
	/* Search header */
	$('.header_block #search input[name=\'search\']').parent().find('span.input-group-btn button').on('click', function() {
		url = $('base').attr('href') + 'index.php?route=product/search';

		var search = $('.header_block #search input[name=\'search\']').val();

		if (search) {
			url += '&search=' + encodeURIComponent(search);
		}
		
		var category_id = $('.header_block #search .select-wrap input[name=\'category_id_header\']').prop('value');

		if (category_id > 0) {
			url += '&category_id=' + encodeURIComponent(category_id) + '&sub_category=true';
		}

		location = url;
	});
	
	$('.header_block #search input[name=\'search\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('.header_block #search input[name=\'search\']').parent().find('.input-group-btn button').trigger('click');
		}
		
		var category_id = $('.header_block #search .select-wrap input[name=\'category_id_header\']').prop('value');

		if (category_id > 0) {
			url += '&category_id=' + encodeURIComponent(category_id) + '&sub_category=true';
		}
		
	});

	$(document).keydown(function(e) {
		if (e.keyCode == 27) {
			$('body .divshadow .option-div').find('.close').trigger('click');
		}
	});
	
	$(document).mouseup(function (e) {
		if ($('body').find('.divshadow .option-div').html() != null && $('body').find('.divshadow .option-div').has(e.target).length === 0) {
			$('body .divshadow .option-div').find('.close').trigger('click');
		}
	});
	
	// Menu
	$('#menu .dropdown-menu').each(function() {
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();

		var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

		if (i > 0) {
			$(this).css('margin-left', '0px');
		}
	});
	
	function clearStyleBootstrap() {
		$('#content .product-layout .product-thumb .image').removeClass('col-lg-1').removeClass('col-sm-2').removeClass('col-xs-3');
		$('#content .product-layout .product-thumb .caption').removeClass('col-lg-11').removeClass('col-sm-10').removeClass('col-xs-9');
		$('#content .product-layout .product-thumb .caption h4').removeAttr('class');
		$('#content .product-layout .product-thumb .price-cart').removeClass('col-lg-5').removeClass('col-sm-6').removeClass('col-xs-12');
		if ($('.product-thumb > div').hasClass('row')) {
			$('.product-thumb > div').contents().unwrap();
		}
		if ($('.product-thumb > .caption > div').hasClass('row')) {
			$('.product-thumb > .caption > div').contents().unwrap();
		}
		if ($('.list-product').removeClass('col-xs-12'));
	}
	
	function clearActiveView(clas) {
		$('#list-view').removeClass('active-view');
		$('#grid-view').removeClass('active-view');
		$('#price-view').removeClass('active-view');
		$(clas).addClass('active-view');
	}
	// Product List
	$('#list-view').click(function() {
		$('#content .product-layout > .clearfix').remove();

		$('#content .product-layout').each(function() {
			if ($(this).hasClass('no_image')) {
				$(this).attr('class', 'product-layout product-list col-xs-12 no_image');
			} else {
				$(this).attr('class', 'product-layout product-list col-xs-12');
			}
		});
		
		$('.product-list .price-cart .button-group').each(function() {
			var htm = $(this).parent().parent().parent().find('.image .chevron').removeClass('animated').removeClass('bounceOut');
			$(this).prepend(htm);
		});
		
		$('.product-list .caption > h4').each(function() {
			$(this).after($(this).find('.price_block'));
		});
		
		clearStyleBootstrap();
		
		$('#content .product-layout .product-thumb .caption > p, #content .product-layout .product-thumb .caption > .rating').show();
		
		$('.product-list .caption .rating').each(function() {
			$(this).after($(this).parent().find('.price-cart .cheapering').removeClass('btn-gray'));
		});
		
		if ($('.list-product').hasClass('no_image')) {
			$('.list-product').find('.product-layout').addClass('no_image');
		}

		localStorage.setItem('display', 'list');
		
		clearActiveView(this);
	});
	
	// Product Grid
	$('#grid-view').click(function() {
		$('#content .product-layout > .clearfix').remove();

		// What a shame bootstrap does not take into account dynamically loaded columns
		cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-layout').each(function() {
				if ($(this).hasClass('no_image')) {
					$(this).attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12 no_image');
				} else {
					$(this).attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
				}
			});
		} else if (cols == 1) {
			$('#content .product-layout').each(function() {
				if ($(this).hasClass('no_image')) {
					$(this).attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12 no_image');
				} else {
					$(this).attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
				}
			});
		} else {
			$('#content .product-layout').each(function() {
				if ($(this).hasClass('no_image')) {
					$(this).attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12 no_image');
				} else {
					$(this).attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
				}
			});
		}
		
		clearStyleBootstrap();
		
		$('.product-grid .product-thumb .image > a').each(function() {
			var htm2 = $(this).parent().parent().find('.caption .price-cart .chevron').addClass('animated').addClass('bounceOut');
			$(this).after(htm2);
		});
		
		$('#content .product-layout .product-thumb .caption > p, #content .product-layout .product-thumb .caption > .rating').show();
		
		$('.product-grid .caption .rating').each(function() {
			$(this).after($(this).parent().find('.price-cart .cheapering').removeClass('btn-gray'));
		});
		$('.product-grid .product-thumb .image .chevron a').each(function() {
			$(this).removeClass('btn-border');
		});
		
		if ($('.list-product').hasClass('no_image')) {
			$('.list-product').find('.product-layout').addClass('no_image');
		}

		localStorage.setItem('display', 'grid');
		 
		clearActiveView(this);
	});
	
	// Product Price
	$('#price-view').click(function() {
		$('#content .product-layout > .clearfix').remove();

		$('#content .product-layout').each(function() {
			if ($(this).hasClass('no_image')) {
				$(this).attr('class', 'product-layout product-price col-xs-12 no_image');
			} else {
				$(this).attr('class', 'product-layout product-price col-xs-12');
			}
		});
		
		if ($('.product-price .caption > div').hasClass('row')) {} else {
			$('.product-price .caption').wrapInner('<div class="row"></div>');
		}
		
		if ($('.product-price .product-thumb > div').hasClass('row')) {} else {
			$('.product-price .product-thumb').wrapInner('<div class="row"></div>');
		}
		
		$('#content .product-layout .product-thumb .image').addClass('col-lg-1 col-sm-2 col-xs-3');
		$('#content .product-layout .product-thumb .caption').addClass('col-lg-11 col-sm-10 col-xs-9');
		
		$('#content .product-layout .product-thumb .caption .row > p, #content .product-layout .product-thumb .caption .row > .rating').hide();
		
		$('#content .product-layout .product-thumb .caption .row > h4').addClass('col-lg-7 col-sm-6 col-xs-12');
		
		
		$('#content .product-layout .product-thumb .caption .row > .price-cart').addClass('col-lg-5 col-sm-6 col-xs-12').removeClass('width');
		
		$('.product-price .product-thumb .image > .chevron').each(function() {
			$(this).removeClass('animated').removeClass('bounceOut');
			$(this).find('a').addClass('btn btn-border btn-gray');
			$(this).parent().parent().parent().find('.caption .price-cart .button-group').prepend($(this));
		});
		$('#content .product-layout .product-thumb .caption .row > a.cheapering').each(function() {
			$(this).addClass('btn');
			$(this).parent().parent().parent().find('.caption .price-cart p.price').before($(this));
		});
		
		$('.product-price .caption .row > .price_block').each(function() {
			$(this).parent().parent().find('h4').append($(this));
		});

		localStorage.setItem('display', 'price');
		
		clearActiveView(this);
		
		if ($('.list-product').hasClass('col-xs-12')) {} else {
			$('.list-product').addClass('col-xs-12')
		}
	});

	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
	} else if (localStorage.getItem('display') == 'price') {
		$('#price-view').trigger('click');
	} else {
		$('#grid-view').trigger('click');
	}
	
	$('.divshadow .close').click(function() {
		closedivshadow();
	});
	
	// Dooblicad
	var divcart = $('.header_block').find('#cart').html();
	if (divcart != null) {
		var parentcart = '<div id="cart" class="btn-group btn-block">' + divcart + '</div>';
		$('.tabletting').append(parentcart);
	}
	
	var element = $(".top_line"), display;
	$(window).scroll(function () {
		display = $(this).scrollTop() >= 200;
		if (display) {element.addClass('show');} else {element.removeClass('show');}
		
	});
	
	$(".top_line").prepend('<div class="col-sm-9"><div class="row"><div class="col-xm-1 col-xs-1 hidden-sm hidden-md hidden-lg"><div class="row">' + $('.menu_tablet').html() + '</div></div><div class="top_search col-xm-2 visible-xm hidden-xs hidden-sm hidden-md hidden-lg"><i class="fa fa-search"></i></div><div class="col-lg-12 col-sm-12 col-xs-11 col-xm-8"><div class="row"><div class="col-lg-8 col-md-7 col-sm-6 col-xs-5 row-xs-left col-xm-1 hidden-xm">' + $('#search').parent().html() + '</div><div class="col-lg-3 col-md-4 col-sm-5 col-xs-6 col-xm-10"><div class="phone-header">' + $('.phone-header').html() + '</div></div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xm-1">' + $('#cart').parent().html() + '</div></div></div></div></div>');
	$(".top_line").prepend('<div class="col-sm-3 hidden-xs"><div class="navbar_category">' + $('.navbar_category').html() + '</div></div>');
	
	$('.top_line .navbar_category > button').attr('data-target', '.navbar-category-collapse-top');
	$('.top_line .navbar_category > button + .category > div').removeClass('navbar-category-collapse').addClass('navbar-category-collapse-top');
	
	
	$('.top_search').append($('.top_line #search').parent().html());
	
	$('.top_line .top_search .fa-search').click(function() {
		$('.top_line .top_search').toggleClass('slid');
	});
	
	$('.top_line #search .select-wrap').addClass('hidden-xs');
	
	$(".top_line").wrapInner('<div class="row"></div>').wrapInner('<div class="container"></div>');

	if ($('#column-left').length === 0) {
		$('.navbar-category-collapse').addClass('collapse-hidden');
	} else {
		$('#column-left').css('margin-top', $('.navbar-category-collapse').height());
	}
	
	$('.product ul.breadcrumb').append('<li class="printing pull-right" onClick="print();"><i class="fa fa-print" aria-hidden="true"></i> Распечатать</li>');
	
	var divsearch = $('.header_block').find('#search').html();
	if (divsearch != null) {
		var parentsearch = '<div class="row"><div id="search" class="input-group form-control col-sm-8">' + divsearch + '</div></div>';
		$('#search_tablet').append(parentsearch);
	}
	var divfootersearch = $('.header_block').find('#search').html();
	if (divfootersearch != null) {
		var footersearch = '<div id="search" class="input-group btn-border">' + divfootersearch + '</div>';
		$('#search_footer').append(footersearch);
	}
	$("#search_tablet #search > input.form-control").each(function(){
		$(this).attr('name', $(this).attr('name') + '_tablet');
    });
	$("#search_footer #search > input.form-control").each(function(){
		$(this).attr('name', $(this).attr('name') + '_footer');
    });
	
	/* Search tablet */
	$('#search_tablet #search input[name=\'search_tablet\']').parent().find('span.input-group-btn button').on('click', function() {
		url = $('base').attr('href') + 'index.php?route=product/search';

		var search_tablet = $('#search input[name=\'search_tablet\']').val();

		if (search_tablet) {
			url += '&search=' + encodeURIComponent(search_tablet);
		}
		
		var category_id = $('#search_tablet .select-wrap input[name=\'category_id_header\']').prop('value');

		if (category_id > 0) {
			url += '&category_id=' + encodeURIComponent(category_id) + '&sub_category=true';
		}

		location = url;
	});
	

	$('#search_tablet #search input[name=\'search_tablet\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('#search_tablet #search input[name=\'search_tablet\']').parent().find('span.input-group-btn button').trigger('click');
		}
		
		var category_id = $('#search .select-wrap input[name=\'category_id_header\']').prop('value');

		if (category_id > 0) {
			url += '&category_id=' + encodeURIComponent(category_id) + '&sub_category=true';
		}
		
	});
	
	$(".top_line #search > input.form-control").each(function(){
		$(this).attr('name', $(this).attr('name') + '_top');
    });
	
	$('.top_line #search').find('input[name=\'search_top\']').keyup(function(ev){
		var search_top;
		search_top = this.value;
		search_top = this.value;
		setTimeout(function() {
			$('.top_line #search input[name=\'search_top\']').val(search_top);
		},0)
		
	})
	
	/* Search header Top */
	$('.top_line #search input[name=\'search_top\']').parent().find('span.input-group-btn button').on('click', function() {
		url = $('base').attr('href') + 'index.php?route=product/search';
		
		var search_top = $('#search input[name=\'search_top\']').val();

		if (search_top) {
			url += '&search=' + encodeURIComponent(search_top);
		}
		
		var category_id = $('.top_line #search .select-wrap input[name=\'category_id_header\']').prop('value');

		if (category_id > 0) {
			url += '&category_id=' + encodeURIComponent(category_id) + '&sub_category=true';
		}

		location = url;
	});
	
	$('.top_line #search input[name=\'search_top\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('.top_line #search input[name=\'search_top\']').parent().find('span.input-group-btn button').trigger('click');
		}
		
		var category_id = $('.top_line #search .select-wrap input[name=\'category_id_header\']').prop('value');

		if (category_id > 0) {
			url += '&category_id=' + encodeURIComponent(category_id) + '&sub_category=true';
		}
		
	});

	
	$('.navbar-category-collapse-top .dropdown-menu .nav-tabs li a').each(function(){
		$(this).attr('href', $(this).attr('href') + 'top');
	});
	
	$('.navbar-category-collapse-top .dropdown-menu .clearfix + .tab-content > div').each(function(){
		$(this).attr('id', $(this).attr('id') + 'top');
	});
		
	setTimeout(function() {
		$('.navbar-category-collapse-top .dropdown-menu ul.nav-tabs li a').attr('data-toggle','tab');
	}, 1000);
});

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cartopenstore/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				$('#cart-total').html(json['total']);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > .dropdown-menu').load('index.php?route=common/cart/info .dropdown-menu');
				}
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert').remove();
				$('.message-success, .warning, .attention, .information, .error').remove();

				if (json['success']) {

					$('#message-success').after('<div class="message-success" style="display: none;">' + json['success'] + '</div>');

					$('.message-success').fadeIn(500).delay(4000).fadeOut(1000);
				}
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert').remove();
				$('.message-success, .warning, .attention, .information, .error').remove();

				if (json['success']) {

					$('#message-success').after('<div class="message-success" style="display: none;">' + json['success'] + '</div>');

					$('.message-success').fadeIn(500).delay(4000).fadeOut(1000);
				}
			}
		});
	},
	'remove': function() {

	}
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();
	
			$.extend(this, option);
	
			$(this).attr('autocomplete', 'off');
			
			// Focus
			$(this).on('focus', function() {
				this.request();
			});
			
			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);				
			});
			
			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}				
			});
			
			// Click
			this.click = function(event) {
				event.preventDefault();
	
				value = $(event.target).parent().attr('data-value');
	
				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}
			
			// Show
			this.show = function() {
				var pos = $(this).position();
	
				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});
	
				$(this).siblings('ul.dropdown-menu').show();
			}
			
			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}		
			
			// Request
			this.request = function() {
				clearTimeout(this.timer);
		
				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}
			
			// Response
			this.response = function(json) {
				html = '';
	
				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}
	
					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}
	
					// Get all the ones with a categories
					var category = new Array();
	
					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}
	
							category[json[i]['category']]['item'].push(json[i]);
						}
					}
	
					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';
	
						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}
	
				if (html) {
					this.show();
				} else {
					this.hide();
				}
	
				$(this).siblings('ul.dropdown-menu').html(html);
			}
			
			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));	
			
		});
	}
})(window.jQuery);

$(window).bind('resize', function(){

// Hide mobile menu etc on window resize
 if ($(window).width() > 768) {
       $('.mobile_menu_wrapper, #cart .contentwrapper, .top_header_drop_down').hide();
  }
});

$(document).ready(function() {
	$('.more_description').click(function() {
		$('.show_description').slideToggle(0);
		$('.hide_description').slideToggle(0);
		$('.razver').slideToggle(0);
		$('.svernut').slideToggle(0);
	}); 
			
	
// Move breadcrumb to header //
			$('.breadcrumb').appendTo($('.breadcrumb_wrapper'));
			$('.breadcrumb_wrapper').has('.breadcrumb').addClass('has_breadcrumb');
	

     
// Mobile main navigation  //
		
			$('.mobile_menu_trigger').click(function() {
  			$('.mobile_menu_wrapper').slideToggle(500, "easeInCubic")
        	});           

            $('.mobile_menu li').bind().click(function(e) {
			$(this).toggleClass("open").find('>ul').stop(true, true).slideToggle(500)
            .end().siblings().find('>ul').slideUp().parent().removeClass("open");
            e.stopPropagation();
			});
			
			$('#search .select-wrap ul li').click(function() {
				$(this).parent().parent().parent().find('input[type=\'text\']').focus();
			});
			
			$('.mobile_menu li a').click(function(e) {
            e.stopPropagation();
            });
			
			

// Mobile main navigation  //
		
$('.contacts-phone').click(function() {
	$('.contacts-phone-more').slideToggle(0)
	$('.arrow-contacts-phone-more').slideToggle(0)
});  
			
// Slideshow and carousel arrows  //
			$(".slide_arrow_prev, .slide_arrow_next").hide();
			$(".product_wrapper, .r_slideshow-wrapper").hover(function() {
			$(this).find(".slide_arrow_prev, .slide_arrow_next").stop(true, true).fadeIn(200)
			});

			$(".product_wrapper, .r_slideshow-wrapper").mouseleave(function() {
			$(this).find(".slide_arrow_prev, .slide_arrow_next").stop(true, true).fadeOut(200)
			});

$('.product-thumb.transition .divshadow').each(function() {
	var productthumb = $(this).parent().offset();

	var i = $('body').outerWidth() - (productthumb.left + $(this).parent().outerWidth() + $(this).outerWidth());
	
	if (i < 0) {
		$(this).parent().addClass('boundary');
	} else {
		$(this).parent().removeClass('boundary');
	}
});
	

});

// 155 function
function getURLVar(key) {
	var value = [];
	
	var query = String(document.location).split('?');
	
	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');
			
			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}
		
		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
} 
	function centering(diving){
		var wsize = windowWorkSize(),
		testElem = $(diving),
		testElemWid =  testElem.outerWidth(),
		testElemHei =  testElem.outerHeight();
				
		testElem.css('top', wsize[1]/2 - testElemHei/2 + (document.body.scrollTop || document.documentElement.scrollTop) + 'px');

		function windowWorkSize(){
		var wwSize = new Array();
			if (window.innerHeight !== undefined) {wwSize= [window.innerWidth,window.innerHeight]} else {
				wwSizeIE = (document.body.clientWidth) ? document.body : document.documentElement; 
				wwSize= [wwSizeIE.clientWidth, wwSizeIE.clientHeight];
			};
			return wwSize;
		};
	}

	function divcartul(){
		$('.divcart').addClass("show animated bounceIn divcart show animated bounceIn col-lg-offset-3 col-lg-6 col-sm-offset-2 col-sm-8 col-xs-offset-1 col-xs-10 col-xm-12 col-xm-offset-0");
		$('.divcart').empty();
		var divcart = $('body').find('#cart > .dropdown-menu > div').html();
		if (divcart != null) {
			var parentcart = '<div class="dropdown-menu pull-right"><div>' + divcart + '</div></div>';
			$('.divcart').append(parentcart);
		}
		setTimeout(function () {
			centering('.divcart');
		}, 0);
		$('.closecart').click(function() {
			deletedivcartul();
		});
		$(document).keydown(function(e) {
			var containerfind = $('body').find('.divcart.show').html();
			if (containerfind != null && e.keyCode === 27) {
				deletedivcartul();
			}
		});
		GenerationCountCart();
	}
	
	function deletedivcartul(){
		$('.divcart').removeClass("bounceIn").addClass("bounceOut");
		setTimeout(function () {
			
			$('.divcart').removeClass("show");
			$('.divcart').empty();
			$('.modal-bg').removeClass("show");
		}, 700);
		setTimeout(function () {
			$('.divcart').removeClass("animated").removeClass("bounceOut");
		}, 2000);
	}
	$(document).ready(function() {
		$('#cart > button').click(function() {
			divcartul();
			bluring();
		});
		
		// tooltips on hover
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body',trigger: 'hover'});

		// Makes tooltips work on ajax generated content
		$(document).ajaxStop(function() {
			$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
		});
		
		
		
	});
	
	function ViewedFooter() {
		$('.footer_fixed .bg-gray').slideToggle(300);
		$('.footer_viewed button .fa').slideToggle(0);
	}
	
function GenerationCountCart() {
	$('.minus-cart').click(function () {
		var $input = $(this).parent().find('#cont-cart');
		var count = parseInt($input.val()) - 1;
		count = count < 1 ? 1 : count;
		$input.val(count);
		$input.change();
		cart.update($input.attr('data-cart-id'), count);
		return false;
	});
	$('.plus-cart').click(function () {
		var $input = $(this).parent().find('#cont-cart');
		var count = parseInt($input.val()) + 1;
		$input.val(count);
		$input.change();
		cart.update($input.attr('data-cart-id'), count);
		return false;
	});
	$('.quantity-input-cart #cont-cart').on('keydown', function(e) {
		if (e.keyCode == 13) {

			var count = parseInt($(this).val());
			
			if (count < 1) {
				count = 1;
			}
			cart.update($(this).attr('data-cart-id'), count);
		}
	});
}
	
// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		if ($('body').find('.divshadow.show').html() != null) {
			quantity = $('.divshadow.show').find('#cont').val();
		}
		data = $('#option1_'+product_id+' input[type=\'radio\']:checked, #option1_'+product_id+' input[type=\'checkbox\']:checked, #option1_'+product_id+' select');
		
		$.ajax({
			url: 'index.php?route=checkout/cartopenstore/add',
			type: 'post',
			data: data.serialize() + '&product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				$('.alert, .text-danger, .message-success, .error').remove();

				$('#cart > button').button('reset');
				
				var containerfind = $('body').find('.divshadow.show').html();
				if (containerfind == null) {
					if (json['redirect']) {
						$('.product-thumb' + product_id).addClass('load');
						setTimeout(function () {
							location = json['redirect'];
						}, 1000);
					}
				}
				
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
					setTimeout(function () {
						$('#cart > button').html('<i class="fa fa-shopping-cart" aria-hidden="true"><span id="cart-total">' + json['total'] + '</span></i>');
					}, 100);

					$('#cart > .dropdown-menu').load('index.php?route=common/cart/info .dropdown-menu');
					
					if (containerfind != null) {
						closedivshadow();
					}
					$('.product-thumb' + product_id).addClass('load');
					setTimeout(function () {
						divcartul();
						bluring();
					}, 800);
					setTimeout(function () {
						$('.product-thumb' + product_id).removeClass('load');
					}, 2000);
					updateCart();
				}
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cartopenstore/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<i class="fa fa-shopping-cart" aria-hidden="true"><span id="cart-total">' + json['total'] + '</span></i>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > .dropdown-menu').load('index.php?route=common/cart/info .dropdown-menu');
				}
				setTimeout(function () {
					divcartul();
					$('.divcart.show .dropdown-menu').removeClass('load');
				}, 800);
				updateCart();
			},
	        error: function(xhr, ajaxOptions, thrownError) {
	            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
	        }
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cartopenstore/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<i class="fa fa-shopping-cart" aria-hidden="true"><span id="cart-total">' + json['total'] + '</span></i>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > .dropdown-menu').load('index.php?route=common/cart/info .dropdown-menu');
				}
				setTimeout(function () {
					divcartul();
				}, 800);
				$('.divcart').addClass("show");
				updateCart();
			},
	        error: function(xhr, ajaxOptions, thrownError) {
	            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
	        }
		});
	}
}

$(document).ready(function() {
	$('a.add_review_child').click(function() {
		$('#tab-review').slideToggle(200);
	});
});
function add_bc_carous(product_id, quantity) {
	data = $('#option5_'+product_id+' input[type=\'text\'], #option5_'+product_id+' input[type=\'radio\']:checked, #option4_'+product_id+' input[type=\'checkbox\']:checked, #option5_'+product_id+' select, #option5_'+product_id+' textarea');
	quantity = $('.itemfeatured-' + product_id).val();
	$.ajax({
			url: 'index.php?route=checkout/cartopenstore/add',
			type: 'post',
			data: data.serialize() + '&product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function(){
			},
			success: function(json) {
				$('.message-success, .warning, .attention, information, .text-danger').remove();
				if (json['error']) {
					if (json['error']['option']) {
						for (i in json['error']['option']) {
							$('#option5-' + i).after('<span class="text-danger">' + json['error']['option'][i] + '</span>');
						}
					}
				} 
				if (json['success']) {
					$('#message-success').after('<div class="message-success" style="display: none;">' + json['success'] + '</div>');
					$('.message-success').fadeIn(500).delay(4000).fadeOut(1000);
					$('.top_line #cart-total').html(json['total']);
					$('.container #cart-total').html(json['total']);
					$('.top_line #cart > .dropdown-menu > ul').load('index.php?route=common/cart/info ul li');
					$('.container #cart > .dropdown-menu > ul').load('index.php?route=common/cart/info ul li');
					
				}	
			}
		});
}