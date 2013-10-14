<?php
//NChat by ThidMod.com
$nchatInfo['url']           = $boardurl.'/NChat/index.php';
$nchatInfo['group']         = $user_info['groups'][0];
$nchatInfo['id']            = $user_info['id'];
$nchatInfo['time']          = date($modSettings['nchat_time_format']);
$nchatInfo['nchatMess']     = $boarddir.'/NChat/NChatMess.php';
$nchatInfo['nchatLast']     = $boarddir.'/NChat/last.html';
$nchatInfo['nchatMutelist'] = $boarddir.'/NChat/NChatMuteList.php';

if($user_info['is_guest'])
	$nchatInfo['name'] = '<i>' . $txt['guest_title'] . '</i>';
else
	$nchatInfo['name'] = $user_info['name'];
	
//read and show all mess, just do it and fast as it can!
function NChatReader(){
	global $modSettings, $nchatInfo;
	if(allowedTo('nchat_read')){
		$mess = unserialize(file_get_contents($nchatInfo['nchatMess'], NULL, NULL, 14));
		if(count($mess) > 0){
			echo 'var nchat=new Array("'.$mess[0].'"';
			for($i=1;$i<count($mess);$i++){
				echo ',"'.nchatParser($mess[$i]).'"';
			}
			echo ');';
		}else{
			echo 'var nchat=new Array();';
		}
	}
}

//write it to your .txt file
function NChatWriter($subject = ''){
	global $modSettings, $nchatInfo, $context, $txt;
	
	if(allowedTo('nchat_write')){
		$mutes = unserialize(file_get_contents($nchatInfo['nchatMutelist'], NULL, NULL, 14));
		if(isset($mutes[$nchatInfo['id']]) && $mutes[$nchatInfo['id']][0] > time()){
			header('Content-type: text/javascript');
			die('var nchat=new Array("#ff00ff|!|-|!|1|!||!|Note|!|<b>'.sprintf($txt['nchat_be_muted'], $mutes[$nchatInfo['id']][1], $mutes[$nchatInfo['id']][2], date($modSettings['nchat_time_format'], $mutes[$nchatInfo['id']][0])).'</b>|!|0");');
		}else{
			if(isset($mutes[$nchatInfo['id']])){
				unset($mutes[$nchatInfo['id']]);
				$fp = fopen($nchatInfo['nchatMutelist'], 'w');
				fwrite($fp, '<?php die; ?>' . "\n" . serialize($mutes));
				fclose($fp);
			}
			$mess = unserialize(file_get_contents($nchatInfo['nchatMess'], NULL, NULL, 14));

			$subject= str_replace('|!|', '!!!', $subject);
			$subject = htmlspecialchars(shorten_subject($subject , $modSettings['nchat_lenght']));

			if(isset($_REQUEST['bold'])) $subject = '<b> '.$subject.'</b>';
			if(isset($_REQUEST['underline'])) $subject = '<u> '.$subject.'</u>';
			if(isset($_REQUEST['italic'])) $subject = '<i> '.$subject.'</i>';

			if(isset($_REQUEST['color']) && isHex($_REQUEST['color']))
				$format = $_REQUEST['color'];
			else
				$format = $modSettings['nchat_text'];		

			$onlineColor = getGroupOnlineColors();
			if(isset($onlineColor[$nchatInfo['group']]))
				$format .= '|!|'.$onlineColor[$nchatInfo['group']];
			else
				$format .= '|!|-';

			$format .= '|!|'.$nchatInfo['id'].'|!|'.$nchatInfo['name'].'|!|'.$nchatInfo['time'];

			$mess[] = $format.'|!|'.$subject.'|!|'.$nchatInfo['group'];

			if(count($mess)>$modSettings['nchat_line']) array_splice($mess, 0, (count($mess)-$modSettings['nchat_line']));

			$fp = fopen($nchatInfo['nchatLast'], 'w');
			fwrite($fp, time());
			fclose($fp);

			$fp = fopen($nchatInfo['nchatMess'], 'w');
			fwrite($fp, '<?php die; ?>' . "\n" . serialize($mess));
			fclose($fp);
		}
	}
}

//Delete a specific row or clean all
function NChatCleaner($MessID = ''){
	global $modSettings, $nchatInfo;
	
	if(allowedTo('nchat_delete')){
		if(is_numeric($MessID)){
			$MessID = (int) $MessID;
			$mess = unserialize(file_get_contents($nchatInfo['nchatMess'], NULL, NULL, 14));
			array_splice($mess, $MessID, 1);
			$fp = fopen($nchatInfo['nchatMess'], 'w');
			fwrite($fp, '<?php die; ?>' . "\n" . serialize($mess));
			fclose($fp);
		}else{
			$fp = fopen($nchatInfo['nchatMess'], 'w');
			fwrite($fp, '<?php die; ?>' . "\na:0:{}");
			fclose($fp);
		}
	}
}

//You can you your forum Smiles but you can use yahoo smile to save you BW
function NChatParser($subject = ''){
	global $modSettings;
	
	if($modSettings['nchat_censor'] == 1)
		censortext($subject);
		
	return $subject;
}


function isHex($hex)
{
	// Returns true if $hex is a valid CSS hex color.
	// The "#" character at the start is optional.

	// Regexp for a valid hex digit
	$d = '[a-fA-F0-9]';
	    
	// Make sure $hex is valid
	if (preg_match("/^#$d$d$d$d$d$d\$/", $hex) ||preg_match("/^#$d$d$d\$/", $hex)) {
		return true;
	}
	return false;
}

function getGroupOnlineColors(){
	global $smcFunc;
	$onlineColor = cache_get_data('onlineColor');
	
	if($onlineColor == NULL){
		$request = $smcFunc['db_query']('', '
			SELECT id_group, online_color
			FROM {db_prefix}membergroups
			WHERE online_color <> ""',
			array(
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request)){
			$onlineColor[$row['id_group']] = $row['online_color'];
		}
		$smcFunc['db_free_result']($request);
		cache_put_data('onlineColor', $onlineColor);
	}
	return $onlineColor;
}
function getSmilesList(){
	global $modSettings, $smcFunc, $user_info;

	$smilesListString = cache_get_data('smilesListString');
	if($smilesListString == NULL){
		if (empty($modSettings['smiley_enable']))
		{
			$smilesList = array(
				array(":))", "laugh.gif"),
				array(":)", "smiley.gif"), 
				array(";)", "wink.gif"),
				array(">:D", "evil.gif"),
				array(":D", "cheesy.gif"),
				array(";D", "grin.gif"),
				array(">:(", "angry.gif"),
				array(":(", "sad.gif"),
				array(":o", "shocked.gif"),
				array("8)", "cool.gif"),
				array("???", "huh.gif"),
				array("::)", "rolleyes.gif"),
				array(":P", "tongue.gif"),
				array(":-[", "embarrassed.gif"),
				array(":-X", "lipsrsealed.gif"),
				array(":-//", "undecided.gif"),
				array(":-*", "kiss.gif"),
				array(":'(", "cry.gif"),
				array("^-^", "azn.gif"),
				array("O0", "afro.gif"),
				array("C:-)", "police.gif"),
				array("O:-)", "angel.gif"),
			);
		}else{
				$result = $smcFunc['db_query']('', '
					SELECT code, filename
					FROM {db_prefix}smileys',
					array(
					)
				);
				$smilesList = array();
				while ($row = $smcFunc['db_fetch_assoc']($result))
				{
					$smilesList[] = array($row['code'], $row['filename']);
				}
				$smcFunc['db_free_result']($result);
		}
		$smilesListString = 'var nchat_smiles_list =new Array(';
		$smilesListString .= '
		Array("'.addslashes(preg_quote(htmlentities($smilesList[0][0]))).'", "'. $modSettings['smileys_url'] . '/' . $user_info['smiley_set'] . '/'.$smilesList[0][1] .'", "'.addslashes(htmlentities($smilesList[0][0])).'")';
		for($i=1;$i<count($smilesList);$i++)
		{
			$smilesListString .= ',
		Array("'.addslashes(preg_quote(htmlentities($smilesList[$i][0]))).'", "'. $modSettings['smileys_url'] . '/' . $user_info['smiley_set'] . '/'.$smilesList[$i][1] .'", "'.addslashes(htmlentities($smilesList[$i][0])).'")';
		}
		$smilesListString .= ');';
		cache_put_data('smilesListString', $smilesListString);
	}
	echo $smilesListString;
}
function NChatSetMute($id_member = '', $time_mute = 5){
	global $modSettings, $smcFunc, $nchatInfo;
	//No one can mute an admin or mod...
	if(allowedTo('nchat_mute')){
		$result = $smcFunc['db_query']('', '
			SELECT member_name, id_group, additional_groups
			FROM {db_prefix}members
			WHERE id_member = {int:id_member}
			LIMIT 1',
			array(
				'id_member' => (int) $id_member,
			)
		);
		$groups = array();
		while ($row = $smcFunc['db_fetch_assoc']($result))
		{
			$groups = explode(',', $row['additional_groups']);
			$groups[] = $row['id_group'];
			$name = $row['member_name'];
		}
		$smcFunc['db_free_result']($result);
		
		if(!in_array(1, $groups) && !in_array(2, $groups)){
			$mutes = unserialize(file_get_contents($nchatInfo['nchatMutelist'], NULL, NULL, 14));
			$mutes[$id_member] = array(time() + $time_mute*60, $name, $nchatInfo['name']);
			$fp = fopen($nchatInfo['nchatMutelist'], 'w');
			fwrite($fp, '<?php die; ?>' . "\n" . serialize($mutes));
			fclose($fp);
		}
	}
}
function NChatRemoveMute($id_member = ''){
	global $modSettings, $txt, $context, $boardurl, $nchatInfo;
	
	if(allowedTo('nchat_mute')){
		$mutes = unserialize(file_get_contents($nchatInfo['nchatMutelist'], NULL, NULL, 14));
		if(isset($mutes[$id_member])){
			unset($mutes[$id_member]);
			$fp = fopen($nchatInfo['nchatMutelist'], 'w');
			fwrite($fp, '<?php die; ?>' . "\n" . serialize($mutes));
			fclose($fp);
		}
		template_init();
		$context['page_title_html_safe'] = $txt['nchat_mute_list'];
		$context['linktree'][] = array(
			'url' => $nchatInfo['url'] . '?action=mutelist',
			'name' => $txt['nchat_mute_list'],
			);
		template_html_above();
		template_body_above();
		while($mute = current($mutes)){
			echo '<b>[<a href="' . $nchatInfo['url'] . '?action=mutelist&u=' . key($mutes) . '">X</a>] </b>' . sprintf($txt['nchat_be_muted'], '<a href="' . $boardurl . '?action=profile;u=' . key($mutes) . '">' . $mute[1] . '</a>', $mute[2], date($modSettings['nchat_time_format'], $mute[0])). '<br />';
			next($mutes);
		}
		template_body_below();
		template_html_below();
	}
}
?>