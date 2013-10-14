<?php
//NChat by ThidMod.com
require_once('../SSI.php');
require_once('NChatHandle.php');

if(!empty($_REQUEST['nchat'])){

	$mess = '';
	if(isset($_REQUEST['nchat_mess']))
		$mess = $_REQUEST['nchat_mess'];
	
	if($_REQUEST['nchat'] == 'write')
		NChatWriter($mess);

	if($_REQUEST['nchat'] == 'clean')
		NChatCleaner($mess);
		
	if($_REQUEST['nchat'] == 'setmute'){
		if(isset($_REQUEST['nchat_mute']))
			NChatSetMute($mess, (int) $_REQUEST['nchat_mute']);
		else
			NChatSetMute($mess);
	}
	
	NChatReader();
}
if(!empty($_REQUEST['action']) && $_REQUEST['action'] == '.js'){
	header('Content-type: text/javascript');
	getSmilesList();
}
if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'mutelist'){
	$user_id = '';
	if(isset($_REQUEST['u']))
		$user_id = $_REQUEST['u'];
	
	NChatRemoveMute($user_id);
}
if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'room'){
	template_init();
	$context['page_title_html_safe'] = $txt['nchat_chatbox'];
	$context['linktree'][] = array(
		'url' => $nchatInfo['url'] . '?action=mutelist',
		'name' => $txt['nchat_mute_list'],
		);
	template_html_above();
	template_body_above();
	require_once('NChatBoardIndex.php');
	template_body_below();
	template_html_below();
}
?>