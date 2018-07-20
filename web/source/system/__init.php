<?php
/**
 * [WeEngine System] Copyright (c) 2014 heirui.cn
 * WeEngine is NOT a free software, it under the license terms, visited http://bbs.heirui.cn/ for more details.
 */

define('IN_GW', true);
if ($controller == 'system' && $action == 'content_provider') {
	$system_activie = 2;
} else {
	$system_activie = 1;
}
