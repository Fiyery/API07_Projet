$(function(){
	setTimeout('load_ajax_navigation()', 1);
});

function load_ajax_navigation() {
	var ajax = {};
	ajax.content_selector = '#MainBody .content';
	ajax.localhost_dir = 'TLH-Technologies/';
	
	ajax.get_root = function(){
		if (!ajax.root) {
			if (window.location.hostname == '127.0.0.1' || window.location.hostname == 'localhost') {
				ajax.root = window.location.origin + '/' + ajax.localhost_dir;
			} else {
				ajax.root = window.location.origin + '/';
			}
		}
		return ajax.root;
	};
	
	ajax.change_address = function(url){
		window.history.pushState('', '', url);
	};
	
	ajax.is_ancre = function(url){
		return (!url || url.indexOf('#') >= 0);
	};
	
	ajax.load = function(url){
		$.ajax({
			url: ajax.get_root() + 'app/ajax/navigation_ajax.php',
			type: 'post',
			dataType: 'html',
			async: false,
			data: {
				url: url
			}
		}).done(function(data){
			ajax.change_address(url);
			$(ajax.content_selector).fadeOut(function() {
				$(this).empty().append(data).fadeIn(300);
			});
		}).fail(function() {
			alert('fail');
		});
	};
	
	ajax.init = function(){
		$('a').on('click', function() {
			if (!ajax.is_ancre($(this).attr('href'))) {
				ajax.load($(this).attr('href'));
			}
			return false;
		});
	};
	
	ajax.init();
};

$(document).ready(function() {
	$("#SearchBox .icon.search").click(function() {
		var uri = $("#SearchBox .hide").val() + "?keywords=" + encodeURIComponent($("#SearchBox .data").val());
		$("#SearchBox a").attr("href", uri);
		$("#SearchBox a").click();
	});
	
	$("#SearchBox .data").keyup(function(e) {
		if(e.keyCode == 13) {
			$("#SearchBox .icon.search").click();
		}
	});
});