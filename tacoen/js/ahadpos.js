function setCookie(cname, cvalue, exdays) {
    var d = new Date();
	if (exdays == -1) {
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+d.toUTCString();
		document.cookie = cname + "=" + cvalue + "; " + expires;
	} else {
		console.log('no exday');
		document.cookie = cname + "=" + cvalue + "; expires=-365";
	}
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

function ta_toggle_navExpand() {
	var obj = $('nav .nav-option .expand')
	if (obj.data('expand')==0) {
		obj.data('expand',1);
		$('nav .nav-option .expand i').removeClass('fa-square-o');
		$('nav .nav-option .expand i').addClass('fa-check-square-o');
		setCookie('nav_expand','1',0);
	} else {
		obj.data('expand',0);
		$('nav .nav-option .expand i').removeClass('fa-check-square-o');
		$('nav .nav-option .expand i').addClass('fa-square-o');
		setCookie('nav_expand','0',-365);
	}
}

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

	var navexpand = getCookie('nav_expand');
	
	if ((navexpand) == 1 ) {
		$('body').addClass('with_drawer');
		$('#drawer').show();
		$('#drawer-btn').data('active',1);
		$('#drawer-btn').attr('data-active',1);
		$('nav .nav-option .expand').data('expand',1);		
		$('nav .nav-option .expand i').removeClass('fa-square-o');
		$('nav .nav-option .expand i').addClass('fa-check-square-o');
	}

	$('input').hover( function(e) {
		var ak = $(this).attr('accesskey');
		if(typeof ak !== "undefined") { $(this).attr('title',"Shortcut: alt+"+ak); }
	})

	$('select').hover( function(e) {
		var ak = $(this).attr('accesskey');
		if(typeof ak !== "undefined") { $(this).attr('title',"Shortcut: alt+"+ak); }
	})
	

});
