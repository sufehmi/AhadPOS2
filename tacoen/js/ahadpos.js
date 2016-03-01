function ta_toggle(obj) {
	var t= '#'+obj.data('menu');
	if ( obj.data('active')== 0 ) {
		$(t).show(); obj.data('active',1); obj.attr('data-active',1)
		$('body').addClass('with_drawer');
	} else {
		$(t).hide(); obj.data('active',0); obj.attr('data-active',0)
		$('body').removeClass('with_drawer');
	}
}

$(document).ready(function () {

	$('input').hover( function(e) {
		var ak = $(this).attr('accesskey');
		if(typeof ak !== "undefined") { $(this).attr('title',"Shortcut: alt+"+ak); }
	})

	$('select').hover( function(e) {
		var ak = $(this).attr('accesskey');
		if(typeof ak !== "undefined") { $(this).attr('title',"Shortcut: alt+"+ak); }
	})
	

});
