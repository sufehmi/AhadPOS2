/*
Harus dipisah karena kasir pakai jquery 1.9
*/

function popupform(myform, windowname)
{
	if (!window.focus)
		return true;
	popWindo=window.open('', windowname, 'height=400,width=700,scrollbars=yes');
	myform.target=windowname;
	popWindo.focus();
	return true;
}

function number_format(a, b, c, d) {
	// credit: http://www.krisnanda.web.id/2009/06/09/javascript-number-format/

	a=Math.round(a * Math.pow(10, b)) / Math.pow(10, b);

	e=a + '';
	f=e.split('.');
	if (!f[0]) {
		f[0]='0';
	}
	if (!f[1]) {
		f[1]='';
	}

	if (f[1].length < b) {
		g=f[1];
		for (i=f[1].length + 1; i <= b; i++) {
			g += '0';
		}
		f[1]=g;
	}

	if (d != '' && f[0].length > 3) {
		h=f[0];
		f[0]='';
		for (j=3; j < h.length; j += 3) {
			i=h.slice(h.length - j, h.length - j + 3);
			f[0]=d + i + f[0] + '';
		}
		j=h.substr(0, (h.length % 3 == 0) ? 3 : (h.length % 3));
		f[0]=j + f[0];
	}

	c=(b <= 0) ? '' : c;
	return f[0] + c + f[1];
}


function RecalcTotal(tot_pembelian) {
	var totalBeli=0;
	var Kembali=0;
	var uangDibayar=parseInt(document.getElementById("uangDibayar").value);
	var surcharge=parseInt(document.getElementById("surcharge").value);

	totalSurcharge=((tot_pembelian / 100) * surcharge);
	totalBeli=tot_pembelian + totalSurcharge;
	Kembali=uangDibayar - totalBeli;

	document.getElementById("uangKembali").value=Kembali;
	document.getElementById("kembalian").innerHTML='<span>' + number_format(Kembali, 0, ',', '.') + '</span>';

	document.getElementById("TotalSurcharge").value=number_format(totalSurcharge, 0, ',', '.');
	document.getElementById("tot_pembelian").innerHTML='<span>' + number_format(totalBeli, 0, ',', '.') + '</span>';
}

