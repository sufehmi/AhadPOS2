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


/*
			function number_format(a, b, c, d) {
			// credit: http://www.krisnanda.web.id/2009/06/09/javascript-number-format/

			a= Math.round(a * Math.pow(10, b)) / Math.pow(10, b);

			e= a + '';
			f= e.split('.');
			if (!f[0]) {
				f[0]= '0';
			}
			if (!f[1]) {
				f[1]= '';
			}

			if (f[1].length< b) {
				g= f[1];
				for (i= f[1].length + 1; i<= b; i++) {
					g += '0';
				}
				f[1]= g;
			}

			if (d != '' && f[0].length >3) {
				h= f[0];
				f[0]= '';
				for (j= 3; j< h.length; j += 3) {
					i= h.slice(h.length - j, h.length - j + 3);
					f[0]= d + i + f[0] + '';
				}
				j= h.substr(0, (h.length % 3== 0) ? 3 : (h.length % 3));
				f[0]= j + f[0];
			}

			c= (b<= 0) ? '' : c;
			return f[0] + c + f[1];
			}


			function RecalcTotal(tot_pembelian) {
			var totalBeli= 0;
			var Kembali= 0;
			var uangDibayar= parseInt(document.getElementById("uangDibayar").value);
			var surcharge= parseInt(document.getElementById("surcharge").value);

			totalSurcharge= ((tot_pembelian / 100) * surcharge);
			totalBeli= tot_pembelian + totalSurcharge;
			Kembali= uangDibayar - totalBeli;

			document.getElementById("uangKembali").value= Kembali;
			document.getElementById("kembalian").innerHTML= '<span>' + number_format(Kembali, 0, ',', '.') + '</span>';

			document.getElementById("TotalSurcharge").value= number_format(totalSurcharge, 0, ',', '.');
			document.getElementById("tot_pembelian").innerHTML= '<span>' + number_format(totalBeli, 0, ',', '.') + '</span>';
			}
			
*/			
// mod_jual_barang
function popupform_full(myform, windowname)
{
	if (!window.focus)
		return true;
	window.open('', windowname, 'type=fullWindow,fullscreen=yes,scrollbars=yes');
	myform.target= windowname;
	return true;
}
	// mod_manage_workstation
	
		function CalculatePrinterCommands() {
			var ip_address= document.getElementById("workstation_address").value;
			var printer_type= document.getElementById("printer_type").value;

			if (printer_type== 'rlpr') {

				printer_commands= '-H ' + ip_address + ' -P printer' + ip_address;
				document.getElementById("printer_commands").value= printer_commands;
			}
		}
		
// src: js_input_retur_barang

function popupform(myform, windowname)
{
 if (!window.focus)
 return true;
 window.open('', windowname, 'height=400,width=700,scrollbars=yes');
 myform.target= windowname;
 return true;
}

function number_format(a, b, c, d) {
 // credit: http://www.krisnanda.web.id/2009/06/09/javascript-number-format/

 a= Math.round(a * Math.pow(10, b)) / Math.pow(10, b);
 e= a + '';
 f= e.split('.');
 if (!f[0]) {
 f[0]= '0';
 }
 if (!f[1]) {
 f[1]= '';
 }

 if (f[1].length< b) {
 g= f[1];
 for (i= f[1].length + 1; i<= b; i++) {
 g += '0';
 }
 f[1]= g;
 }

 if (d != '' && f[0].length >3) {
 h= f[0];
 f[0]= '';
 for (j= 3; j< h.length; j += 3) {
 i= h.slice(h.length - j, h.length - j + 3);
 f[0]= d + i + f[0] + '';
 }
 j= h.substr(0, (h.length % 3== 0) ? 3 : (h.length % 3));
 f[0]= j + f[0];
 }

 c= (b<= 0) ? '' : c;
 return f[0] + c + f[1];
}

// src: js_jual_barang2

function addComma(angka) {
	nStr += '';
	x= nStr.split('.');
	x1= x[0];
	x2= x.length >1 ? '.' + x[1] : '';
	var rgx= /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1= x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function RecalcTotal(tot_pembelian) {
	var totalBeli= 0;
	var Kembali= 0;
	var uangDibayar= parseInt(document.getElementById("uangDibayar").value);
	var surcharge= parseInt(document.getElementById("surcharge").value);

	totalSurcharge= ((tot_pembelian / 100) * surcharge);
	totalBeli= tot_pembelian + totalSurcharge;
	Kembali= uangDibayar - totalBeli;

	document.getElementById("uangKembali").value= Kembali;
	document.getElementById("kembalian").innerHTML= '<span>' + addComma(Kembali) + '</span>';
	document.getElementById("TotalSurcharge").value= addComma(totalSurcharge);
	//	document.getElementById("tot_pembelian").innerHTML= '<span>' + number_format(totalBeli, 0, ',', '.') + '</span>';
	document.getElementById("tot_pembelian").innerHTML= '<span>' + addComma(totalBeli) + '</span>';
}

function targetopener(mylink, closeme, closeonly)
{
	if (!(window.focus && window.opener))
		return true;
	window.opener.focus();
	if (!closeonly)
		window.opener.location.href= mylink.href;
	if (closeme)
		window.close();
	return false;
}

$(document).ready(function () {

	//collect#1
	var dropBox= document.getElementById("barcode");
	if (dropBox != null) { 	dropBox.focus(); }

	var dropBox1= document.getElementById("jumBarang");
	if (dropBox1 != null) { dropBox1.focus();	}
	

});

$(document).ready(function ()
{
	$('#layer1').Draggable(
			{
				zIndex: 60,
				ghosting: false,
				opacity: 0.7,
				handle: '#layer1_handle'
			}
	);
	$('#layer1_form').ajaxForm({
		target: '#frmTambahBarang',
		success: function ()
		{
			$("#layer1").hide();
		}
	});
	$("#layer1").hide();
	$('#tambahbarang').click(function ()
	{
		$("#layer1").show();
		$("#barcode").focus();
	});
	$('#close').click(function ()
	{
		$("#layer1").hide();
	});
});

