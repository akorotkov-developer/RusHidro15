function setCookie(cookieName,cookieValue,nDays) { // set cookie
	var today = new Date();
	var expire = new Date();
	if (nDays==null || nDays==0) nDays=1;
	expire.setTime(today.getTime() + 3600000*24*nDays);
	document.cookie = cookieName+"="+escape(cookieValue) + "; path=/; expires="+expire.toGMTString();
}

function getCookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}





	function changeWidthTree(move)		{

		var width = parseInt( (document.getElementById('tree').style.width), 10);
		if (move == 'left')		{
			width = width - 100;
		}
		if (move == 'right')		{
			width = width + 100;
		}

		if (width < 200)
		{
			width = 200;
		}

		//alert (cookies_time);
		setCookie('treeWidth', width, 30);
		//alert (width);
		document.getElementById('tree').style.width = width + 'px';

	}



	function sMenu(param) {

	
			//alert (getCookie('treeWidth'));


		if (param == 1)		{

			if (!iframeYes)
			{
				if (document.getElementById('demo_frame').src != iframeAddr)	{
					//alert ('s');
					document.getElementById('demo_frame').src = iframeAddr;
				}
				iframeYes = true;
			}

				document.getElementById('menu_open').style.zindex = '1000';
				//alert (document.getElementById('menu_open').style.zindex);

				document.getElementById('sub2').style.display = 'none';
				//document.getElementById('sub').style.left = '0px';
				document.getElementById('sub').style.display = 'block';
				//document.getElementById('sub').style.visibility = 'visible';
				//document.getElementById('sub').style.width = 'auto';
				if (getCookie('treeWidth'))
				{
					document.getElementById('tree').style.width = getCookie('treeWidth') + 'px';
				}



		}
		if (param == 0)		{
			document.getElementById('menu_open').style.zindex = '1';
			document.getElementById('sub2').style.display = 'block';
			//document.getElementById('sub').style.visibility = 'hidden';
			//document.getElementById('sub').style.width = '1px';
			document.getElementById('sub').style.display = 'none';
		}

		
	}


function showLog(obj) {
	if (obj.value == '') { obj.value = 'Логин'; } 
	else { if (obj.value == 'Логин') { obj.value = ""; }	}
}
function showLog2(obj) {
	if (obj.value == '') { obj.value = 'Пароль'; } 
	else { if (obj.value == 'Пароль') { obj.value = ""; }	}
}



function translit(textFieldId, resultFieldId){
    var space = '-';
    var text = document.getElementById(textFieldId).value.toLowerCase();

    var transl = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
        'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
        'о': 'o', 'п': 'p', 'р': 'r','с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'kh',
        'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'shch','ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
        ' ': space, '_': space, '`': '', '~': space, '!': '', '@': space,
        '#': space, '$': '', '%': space, '^': space, '&': space, '*': space,
        '(': '', ')': '','-': space, '\=': space, '+': space, '[': '',
        ']': '', '\\': space, '|': space, '/': space,'.': space, ',': space,
        '{': '', '}': '', '\'': space, '"': '', ';': space, ':': space,
        '?': space, '<': space, '>': space, '№': 'N', '—': '-', '«': '', '»': ''
    }

    var result = '';
    var curent_sim = '';

    for(i=0; i < text.length; i++) {
        if(transl[text[i]] != undefined) {
            if(curent_sim != transl[text[i]] || curent_sim != space){
                result += transl[text[i]];
                curent_sim = transl[text[i]];
            }                                                                            
        } else {
            result += text[i];
            curent_sim = text[i];
        }                             
    }         

    result = TrimStr(result);
    document.getElementById(resultFieldId).value = result;
}
function TrimStr(s) {
    s = s.replace(/^-/, '');
    return s.replace(/-$/, '');
}

	
