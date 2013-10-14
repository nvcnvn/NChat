<?php
$nchatInfo['id']        = $context['user']['id'];
$nchatInfo['url']       = $boardurl.'/NChat/index.php';
$nchatInfo['nchatLast'] = '/NChat/last.html';

if(allowedTo('nchat_read')){
	echo '
	<span class="clear upperframe"><span></span></span>
	<div class="roundframe"><div class="innerframe">
	<div class="cat_bar"><h3 class="catbg">'.$txt['nchat_chatbox'].'</h3></div>
	<div class="title_barIC"><h4 class="titlebg">'.parse_bbc($modSettings['nchat_admin_mess']).'</h4></div>
	<div id="nchat_admin_shoutbox" style="background-color:'.$modSettings['nchat_background'].';width:100%;height:150px;overflow:auto;border-left-style:solid;border-left-width:1px;"></div>
	<hr style="clear" />';
}

if(allowedTo('nchat_write')){
	echo '
	<div id="nchat_editor" class="windowbg">
	<div style="height:35px;">
		' . (allowedTo('nchat_delete') ? '<a href="javascript: void(0);" onclick="nchat_ajax(\'nchat=clean\', true);">'.$txt['nchat_clean'].'</a>  |  ' : '') . (allowedTo('nchat_mute') ? '<a href="' . $nchatInfo['url'] . '?action=mutelist">' . $txt['nchat_mute_list'] . '</a>' : '') .'
		<br />
		<button id="bold" onclick="nchat_format(this.id);" style="width:35px;font-weight:bold;">'.$txt['nchat_blod'].'</button>
		<button id="underline" onclick="nchat_format(this.id);" style="width:35px;text-decoration:underline;">'.$txt['nchat_underline'].'</button>
		<button id="italic" onclick="nchat_format(this.id);" style="width:35px;font-style:italic;">'.$txt['nchat_italic'].'</button>
		<select onchange="nchat_color();" id="nchat_color">
			<option style="background: '.$modSettings['nchat_text'].';" value="'.$modSettings['nchat_text'].'" selected="selected">'.$txt['nchat_text'].'</option>
			<option style="background: Red;" value="#ff0000">'.$txt['nchat_red'].'</option>
			<option style="background: Teal;" value="#008080">'.$txt['nchat_teal'].'</option>
			<option style="background: Blue;" value="#0000ff">'.$txt['nchat_blue'].'</option>
			<option style="background: Green;" value="#00ff00">'.$txt['nchat_green'].'</option>
			<option style="background: Brown;" value="#996633">'.$txt['nchat_brown'].'</option>
			<option style="background: Orange;" value="#ffa500">'.$txt['nchat_orange'].'</option>
		</select>
		'.(($modSettings['nchat_smile'] != 0) ? '<button onclick="nchat_show_smile(false);">'.$txt['nchat_add_smileys'].'</button>' : '').'
	</div>
	<div id="nchat_smiles"></div>
	<div id="nchat_smiles_2"></div>';
		echo '
	<div>
		<input id="nchat_input" onkeypress="if(event.keyCode == 13){ nchat_sender(); return false;}" contenteditable="true" style="width:90%;" />
		<input type="button" value="'.$txt['nchat_save'].'" onclick="nchat_sender(); return false;" />
	</div>
	</div>
	</div></div>
	<span class="clear lowerframe"><span></span></span>';
	if($modSettings['nchat_order'] == 0 && $modSettings['nchat_sound'] == 1){
		if($context['browser']['is_ie']){
			echo '
	<object classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" name="mediaPlayerObj" width="1" height="1" id="mediaPlayerObj">
		<param name="uiMode" value="none">
		<param name="url" value="'.$boardurl.'/NChat/sounds/bip.mp3">
		<param name="autoStart" value="false">
		<param name="loop" value="false">
	</object>';
		}else{
			echo '
	<audio id="nchat_sound" controls="controls" style="visibility:hidden;height:1px;">
		<source src="'.$boardurl.'/NChat/sounds/bip.ogg" type="audio/ogg" />
		<source src="'.$boardurl.'/NChat/sounds/bip.mp3" type="audio/mp3" />
	</audio>';
		}
	}
}

if(allowedTo('nchat_read')){
	if($modSettings['nchat_smile'] == 1){
		echo '
	<script type="text/javascript" src="'.$boardurl.'/NChat/NChatSmiles.js"></script>';
	}
	if($modSettings['nchat_smile'] == 2){
		echo '
	<script type="text/javascript" src="'.$nchatInfo['url'].'?action=.js"></script>';
	}	
	echo '
	<script type="text/javascript">
	var refreshtime = '.$modSettings['nchat_time'].';
	var nchatLast = '.file_get_contents($boarddir.$nchatInfo['nchatLast']).';
	var nchatInput = document.getElementById("nchat_input");
	var nchatOutput = document.getElementById("nchat_admin_shoutbox");';
	if($modSettings['nchat_order'] == 0 && $modSettings['nchat_sound'] == 1){
	echo '
	var nchatSound = '.($context['browser']['is_ie'] ? 'mediaPlayerObj.controls' : 'document.getElementById("nchat_sound")').';';
	}
	echo '
	var format_bold = false;
	var format_underline = false;
	var format_italic = false;
	var show_err = true;
	var last_chat = 0;
	var limit_time = 1; //in second
	var reload;
	
	function nchat_shut_it_down()
	{
		nchatOutput.scrollTop = nchatOutput.scrollHeight;
		clearTimeout(shut_it_down);
	}
	
	window.onbeforeunload = function (e) {
		show_err = false;
	}
	
	function nchat_reload()
	{
		nchat_ajax("", false);
		reload = setTimeout("nchat_reload();",refreshtime);
	}
	
	function nchat_ajax(param, load)
	{
		var xmlhttp;
		var nchatUrl;
		var nchatscroll = false;
		if(load == true){
			nchatUrl = "'.$nchatInfo['url'].'";
		}else{
			nchatUrl = "'.$boardurl.$nchatInfo['nchatLast'].'";
		}
		if (window.XMLHttpRequest){
			xmlhttp = new XMLHttpRequest();
		}else{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){
				if (xmlhttp.status == 200){
					if(load == true){
						eval(xmlhttp.responseText);
						'.(($modSettings['nchat_order'] == 0) ? 'if((nchatOutput.scrollHeight - nchatOutput.scrollTop) == 150)
						{
							nchatscroll = true;
						}' : '').'
						nchat_parser(nchat);
						if(nchatscroll)
						{
							nchatOutput.scrollTop = nchatOutput.scrollHeight;
						}
						'.(($modSettings['nchat_order'] == 0 && $modSettings['nchat_sound'] == 1) ? 'nchatSound.play();' : '').'
					}else{
						if(eval(xmlhttp.responseText) > nchatLast){
							nchatLast = eval(xmlhttp.responseText);
							nchat_ajax("nchat=read", true);
						}
					}
				}else if(show_err == true)
					nchatOutput.innerHTML = "<div class=\'errorbox\'>'.$txt['nchat_index_unavailable'].'</div>";

			}
		}

		xmlhttp.open("POST", nchatUrl, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", param.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(param);
	}
	function nchat_parser(nchat)
	{
		var nchat_content = "";
		var nchat_regex;
		if(typeof nchat[0] !== "undefined" && nchat[0] !== null)
		{
			'.(($modSettings['nchat_order'] == 0) ? 'for(i=0;i<nchat.length;i++){' : 'for(i=(nchat.length-1);i>=0;i--){').'
				nchat_small_mess = nchat[i].split("|!|");
				nchat_content += '.(allowedTo('nchat_delete') ? '"[<a href=\"javascript:nchat_ajax(\'nchat=clean&&nchat_mess=" + i + "\', true);\">X</a>]" + ' : '').'"<span style=\"font-size:70%;\">[" + nchat_small_mess[4] + "]</span>";
				
				'.(allowedTo('nchat_mute') ? 'if(nchat_small_mess[6] != -1 && nchat_small_mess[6] != 1 && nchat_small_mess[6] != 2){
					nchat_content += "(<a href=\"javascript:nchat_mute(nchat_small_mess[2])\">X</a>)";
				}' : '').'
				
				if(nchat_small_mess[1] != "-")
				{
					nchat_content +=  " <a href=\"'.$boardurl.'/index.php?action=profile;u=" + nchat_small_mess[2] + "\" style=\"color:" + nchat_small_mess[1] + ";\">" + nchat_small_mess[3] + "</a>:";
				}else{
					nchat_content +=  " <a href=\"'.$boardurl.'/index.php?action=profile;u=" + nchat_small_mess[2] + "\">" + nchat_small_mess[3] + "</a>:";
				}
				
				nchat_small_mess[5] = nchat_small_mess[5]'.(($modSettings['nchat_auto_link'] == 1) ? '.replace(/(\\b(https?|ftp):\\/\\/[-A-Z0-9+&@#\\/%?=~_|!:,.;]*[-A-Z0-9+&@#\\/%=~_|])/gi, "<a href=\'$1\' target=\'_blank\'>$1</a>")' : '').'
				
				for(j=0;j<nchat_smiles_list.length;j++)
				{
					nchat_regex = new RegExp(nchat_smiles_list[j][0], "gi");
					nchat_small_mess[5] = nchat_small_mess[5].replace(nchat_regex, "<img src=\'" + nchat_smiles_list[j][1] + "\' />");
				}
				nchat_content +=  "<span style=\"color:" + nchat_small_mess[0] + ";\">" + nchat_small_mess[5] + "</span><br />";
			}
		}
		nchatOutput.innerHTML = nchat_content;
	}
	function nchat_replace(str)
	{
		str = str.replace(/\+/g, "%2B").replace(/\&/g, "%26").replace(/\#/g, "%23");
		'.(($modSettings['nchat_smile'] == 1) ? 'str = str.replace(/\\m\//g, "\\\\m/").replace(/\\:d\//g, "\\\\:d/");' : '').'
		return str;
	}
	function nchat_format(what)
	{
		if(document.getElementById(what).innerHTML.length > 1) {
			document.getElementById(what).innerHTML = document.getElementById(what).innerHTML[0];
			if(what == "bold"){
				nchatInput.style.fontWeight = "normal";
				format_bold = false;
			}
			if(what == "italic"){
				nchatInput.style.fontStyle = "normal";
				format_italic = false;
			}
			if(what == "underline"){
				nchatInput.style.textDecoration = "none";
				format_underline = false;
			}
		}else{
			document.getElementById(what).innerHTML += "*";
			if(what == "bold"){
				nchatInput.style.fontWeight = "bold";
				format_bold = true;
			}
			if(what == "italic"){
				nchatInput.style.fontStyle = "italic";
				format_italic = true;
			}
			if(what == "underline"){
				nchatInput.style.textDecoration = "underline";
				format_underline = true;
			}
		}
		nchatInput.focus();
	}
	function nchat_color()
	{
		nchatInput.style.color = document.getElementById(\'nchat_color\').value;
		nchatInput.focus();
	}
	function nchat_mute(user_id)
	{
		var a = prompt("'.$txt['nchat_add_mute'].'", 5);
		
		if(a != null && a != "")
			nchat_ajax("nchat=setmute&&nchat_mess=" + user_id + "&&nchat_mute=" + a, true);

		return false;
	}
	function nchat_show_smile(more)
	{
		var smiles_show = "";
		var smiles_limit = 20;
		if(more && nchat_smiles_list.length > 20){
			for(i=20;i<nchat_smiles_list.length;i++)
			{
				smiles_show += "<span onclick=\'nchat_smile(nchat_smiles_list["+i+"][2])\'><img src=\'" + nchat_smiles_list[i][1] + "\' alt=\'*\' /></span>  ";
			}
			document.getElementById("nchat_smiles_2").innerHTML = smiles_show;
		}else{
			if(nchat_smiles_list.length < 20)
				smiles_limit = nchat_smiles_list.length;
			
			for(i=0;i<smiles_limit;i++)
			{
				smiles_show += "<span onclick=\'nchat_smile(nchat_smiles_list["+i+"][2])\'><img src=\'" + nchat_smiles_list[i][1] + "\' alt=\'*\' /></span>  ";
			}
			document.getElementById("nchat_smiles").innerHTML = smiles_show + "	<a href=\'javascript: void(0);\' onclick=\'nchat_show_smile(true);\'>'.$txt['nchat_more_smileys'].'</a>";				
		}
	}
	function nchat_smile(what)
	{
		nchatInput.value += what.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, "\"");
		nchatInput.focus();
	}
	function nchat_sender()
	{
		var d=new Date();
		if(nchatInput.value.length > 0)
		{
			if((d.getTime() - last_chat) >= limit_time*1000){
				var formatString = "";
				if(format_bold) formatString += "&&bold=true";
				if(format_underline) formatString += "&&underline=true";
				if(format_italic) formatString += "&&italic=true";
				formatString += "&&color=" + document.getElementById("nchat_color").value;
				sender = nchat_ajax("nchat=write&&nchat_mess=" + nchat_replace(nchatInput.value) + formatString, true);
				clearTimeout(reload);
				nchat_reload();
				nchatInput.value = "";
				last_chat = d.getTime();
			}else{
				alert("'.$txt['nchat_so_fast'].'");
			}
		}else{
			alert("'.$txt['nchat_empty_mess'].'");
		}
	}
	nchat_ajax("nchat=read", true);
	nchat_reload();
	'.(($modSettings['nchat_order'] == 0) ? 'shut_it_down = setTimeout("nchat_shut_it_down();", 1000);' : '').'
	window.onunload = function() {
		clearTimeout(reload);
	};
	</script>';
}
?>