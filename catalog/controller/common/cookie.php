<?php
class ControllerCommonCookie extends Controller {
	public function topbanner() {
		if ($this->config->get('bannertop_time')) {$bannertop_time = $this->config->get('bannertop_time');} else {$bannertop_time = 0;}
		setcookie('top_banner', '0', time() + 60*60*$bannertop_time, '/', $this->request->server['HTTP_HOST']);
	}
	public function bannerpopup() {
		$this->load->model('extension/module/bannerpopup');
		$result = $this->model_extension_module_bannerpopup->getSetting('bannerpopup');
		if (isset($result['bannerpopup_time'])) {$bannerpopup_time = $result['bannerpopup_time'];} else {$bannerpopup_time = 0;}

		setcookie('popup_banner', '0', time() + 60*60*$bannerpopup_time, '/', $this->request->server['HTTP_HOST']);
	}
}
