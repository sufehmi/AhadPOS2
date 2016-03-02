function compile() {
	var url = $('#compila').attr('action');
	var data = $('#compila').serializeArray();
	$.post( url, data, function(res) {
		$('#compile').html(res);
	});		
}

function compile_modal() {


	$('#compile').empty();

	$('#compile').append("<form id='compila' action='compiler.php' method='post' onsubmit='return false;'></form>")
	var n = 0;
	$( "head link[type='text/css']" ).each(function(i){
		var url = $(this).attr('href'); var check='';
		if ( $(this).data('comp') == 1 ) { check='CHECKED' } else { check='' }
		$('#compile form').append("<p><input type='checkbox' "+check+" name='css["+n+"]' value='"+url+"'>"+url+"</p>")
		n = n+1;
	});

	$('#compile form').append("<p><label>Output:</label><input type='text' name='out' value='bootstrap.compile.min.css'></p>")
	$('#compile form').append("<p><input type='submit' class='btn btn-info btn-default name='s' value='Compile it!' onclick='compile()'></p></form>")

}

(function(){
/*  $(window).scroll(function () {
      var top = $(document).scrollTop();
      $('.splash').css({
        'background-position': '0px -'+(top/3).toFixed(2)+'px'
      });
      if(top > 50)
        $('#home > .navbar').removeClass('navbar-transparent');
      else
        $('#home > .navbar').addClass('navbar-transparent');
  });
*/

  $("a[href='#']").click(function(e) {
    e.preventDefault();
  });

  var $button = $("<div id='source-button' class='btn btn-primary btn-xs'>&lt; &gt;</div>").click(function(){
    var html = $(this).parent().html();
    html = cleanSource(html);
    $("#source-modal pre").text(html);
    $("#source-modal").modal();
  });

  $('.bs-component [data-toggle="popover"]').popover();
  $('.bs-component [data-toggle="tooltip"]').tooltip();

  $(".bs-component").hover(function(){
    $(this).append($button);
    $button.show();
  }, function(){
    $button.hide();
  });

  function cleanSource(html) {
//    html = html.replace(/×/g, "&times;")
 //              .replace(/«/g, "&laquo;")
   //            .replace(/»/g, "&raquo;")
//               .replace(/</g, "&lt;")
//               .replace(/>/g, "&gt;");

    var lines = html.split(/\n/);

    lines.shift();
    lines.splice(-1, 1);

    var indentSize = lines[0].length - lines[0].trim().length,
        re = new RegExp(" {" + indentSize + "}");

    lines = lines.map(function(line){
      if (line.match(re)) {
        line = line.substring(indentSize);
      }

      return line;
    });

    lines = lines.join("\n");

    return lines;
  }

})();