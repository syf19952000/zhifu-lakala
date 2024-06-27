<?php

		function is_weixin() {
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
				return true;
			} else {
				echo '<script>alert("请在微信端进行操作！");</script>';
				exit();
			}
		}
?>