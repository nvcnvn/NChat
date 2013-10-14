<?php
//NChat by ThisMod.com
$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_order', '1'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_sound', '0'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_censor', '1'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_smile', '1'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_auto_link', '0'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_admin_mess', 'Thank for choosing NChat! Need Help or any questions? Read here: http://thismod.com/community/index.php?topic=2.0'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_time', '3000'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_line', '30'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_lenght', '100'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_background', '#ffffff'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_text', '#000000'),
array('variable'));

$smcFunc['db_insert']('ignore',
'{db_prefix}settings',
array('variable' => 'string-255', 'value' => 'string-65534'),
array('nchat_time_format', 'M d H:i:s'),
array('variable'));
?>