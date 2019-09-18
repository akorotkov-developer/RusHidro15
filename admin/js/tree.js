bpressedr = '';
flint = 0;

var XY;

function getElementPosition(elemId)
{

    var elem_1 = document.getElementById(elemId);

	//alert (elem_1);
	//return;

    var w_1 = elem_1.offsetWidth;
    var h_1 = elem_1.offsetHeight;
	
    var l_1 = 0;
    var t_1 = 0;
	
    while (elem_1)
    {
        l_1 += elem_1.offsetLeft;
        t_1 += elem_1.offsetTop;
        elem_1 = elem_1.offsetParent;
    }

    return {"left":l_1, "offsetY":t_1, "width": w_1, "height":h_1};
}



function mousePageXY(e)
{
  var x = 0, y = 0;

  if (!e) e = window.event;

  if (e.pageX || e.pageY)
  {
    x = e.pageX;
    y = e.pageY;
  }
  else if (e.clientX || e.clientY)
  {
    x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
    y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
	//y = y +  document.body.scrollTop;
  }

	//offY = e.offsetY;

  return {"x":x, "y":y};
}

function TrRightClick(n) {

	//alert (n);

	offXY = getElementPosition(n.id);

	if (bpressed != '') {
		if (bpressed == n.id) {
			document.all(bpressed).style.backgroundColor = "#E0E0E0";
			$('divcursor').style.visibility = 'hidden';
			bpressed = '';
			if (flint > 0) {clearInterval(flint);};
		} else {

			//alert (bpressedr);

			if (bpressedr == 'copy') {s = 'скопировать';};
			if (bpressedr == 'move') {s = 'переместить';};
			if (confirm('Вы действительно хотите ' + s + ' папку?')) {
				if (flint > 0) {clearInterval(flint);};
				yy = XY.y - offXY.offsetY;
				if (yy < 7) {document.selparent.copymove.value = 0;}
				else if (yy > 14) {document.selparent.copymove.value = 1;}
				else {document.selparent.copymove.value = 2;};
				if (bpressedr == 'copy') {document.selparent.copy.value = n.id.substr(2);};
				if (bpressedr == 'move') {document.selparent.move.value = n.id.substr(2);};
				document.selparent.parent2.value = bpressed.substr(2);
				document.selparent.submit();
			};
		};
	} else {
		TrClick(n);
		//alert (offXY.offsetY);
		popup.style.left = XY.x - 10;
		popup.style.top = XY.y - 10; // + document.body.scrollTop - 10;
		//popup.style.top = XY.y  + document.body.scrollTop - 10;
		popup.style.visibility = 'visible';
	};
	return false;
}
function TrOver(n) {
	if ((popup.style.visibility == 'hidden') && (bpressed == '')) {
		if (bpressed != document.selparent.parent2.value) {
			document.all(document.selparent.parent2.value).style.backgroundColor = "";
		};
		document.selparent.parent2.value = n.id;
		//if (bpressed != n.id) n.style.backgroundColor = "#E0E0E0";
	};
}



im0 = new Image; im0.src = '/admin/img/spacer.gif';
im1 = new Image; im1.src = '/admin/img/bg-top.gif';
im2 = new Image; im2.src = '/admin/img/bg-bottom.gif';
im3 = new Image; im3.src = '/admin/img/ico-page-add2.gif';
im4 = new Image; im4.src = '/admin/img/spacer.gif';

imTDspacer = new Image; imTDspacer.src = '/admin/img/spacer.gif';
imTDm1 = new Image; imTDm1.src = '/admin/img/m1.gif';
imTDm2 = new Image; imTDm2.src = '/admin/img/m2.gif';
imTDm3 = new Image; imTDm3.src = '/admin/img/m3.gif';
imTDm4 = new Image; imTDm4.src = '/admin/img/m4.gif';
imTDm1_plus = new Image; imTDm1_plus.src = '/admin/img/m1_plus.gif';
imTDm2_plus = new Image; imTDm2_plus.src = '/admin/img/m2_plus.gif';
//imTDm3_plus = new Image; imTDm3_plus.src = '/admin/img/m3_plus.gif';
//imTDm4_plus = new Image; imTDm4_plus.src = '/admin/img/m4_plus.gif';
imTDm1_minus = new Image; imTDm1_minus.src = '/admin/img/m1_minus.gif';
imTDm2_minus = new Image; imTDm2_minus.src = '/admin/img/m2_minus.gif';
//imTDm3_minus = new Image; imTDm3_minus.src = '/admin/img/m3_minus.gif';
//imTDm4_minus = new Image; imTDm4_minus.src = '/admin/img/m4_minus.gif';

imAdd = new Image; imAdd.src = '/admin/img/ico-add.gif';
imEdit = new Image; imEdit.src = '/admin/img/ico-edit.gif';
imEditbl = new Image; imEditbl.src = '/admin/img/ico-editbl.gif';
imCopy = new Image; imCopy.src = '/admin/img/ico-copy-s.gif';
imMove = new Image; imMove.src = '/admin/img/ico-move.gif';
imHide = new Image; imHide.src = '/admin/img/ico-hide.gif';
imDel = new Image; imDel.src = '/admin/img/ico-del.gif';

imAddS = new Image; imAddS.src = '/admin/img/ico-add-s.gif';
imEditS = new Image; imEditS.src = '/admin/img/ico-edit-s.gif';
imEditblS = new Image; imEditblS.src = '/admin/img/ico-editbl-s.gif';
imCopyS = new Image; imCopyS.src = '/admin/img/ico-copy-s.gif';
imMoveS = new Image; imMoveS.src = '/admin/img/ico-move-s.gif';
imHideS = new Image; imHideS.src = '/admin/img/ico-hide-s.gif';
imDelS = new Image; imDelS.src = '/admin/img/ico-del-s.gif';


documenty = -1;
function TrMove(n) {

	
	offXY = getElementPosition(n.id);
	
	if (bpressed != '') {

		
		yy = XY.y - offXY.offsetY;
		//alert (n.id + " " + yy);
		n.style.borderWidth = 1;
		if (bpressed != n.id) {

			if ((yy < 7) && (n.id != 'tr0')) {
				if ($('im').src != im1.src)
					$('im').src = im1.src;
				//$('divcursor').style.top = XY.y + document.body.scrollTop - offXY.offsetY - 2;
				$('divcursor').style.top = XY.y - 2;
			} else if ((yy > 14) && (n.id != 'tr0')) {
				if ($('im').src != im2.src)
					$('im').src = im2.src;
				//$('divcursor').style.top = XY.y + document.body.scrollTop - offXY.offsetY + 7;
				$('divcursor').style.top = XY.y - 2;
			} else if (n.id != 'tr0') {
				if ($('im').src != im3.src)
					$('im').src = im3.src;
				//$('divcursor').style.top = XY.y + document.body.scrollTop - offXY.offsetY - 2;
				$('divcursor').style.top = XY.y - 10;
			};
			documenty = XY.y + document.body.scrollTop;
			xx = XY.x - 27;
			if (xx < 15) xx = 15;
			$('divcursor').style.left = xx;

		} else {
			documenty = -1;
			$('im').src = im4.src;
		};
		//$('im').filters[0].opacity = 100;
		//if (flint > 0) {clearInterval(flint);};
		//flint = setInterval("javascript:if ($('im').filters[0].opacity == 100) {$('im').filters[0].opacity = 15;} else {$('im').filters[0].opacity = 100;};", 200);
	};
}
function bpress(oper) {
	divHide();
	bpressedr = oper;
	bpressed = document.selparent.parent2.value;
	$('im').src = im4.src;
	$('divcursor').style.visibility = 'visible';
	document.all(bpressed).style.backgroundColor = "#F0F0F0";
}
function TrOut(n) {
}
