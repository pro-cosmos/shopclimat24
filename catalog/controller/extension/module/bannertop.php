<?php
class ControllerExtensionModuleBannertop extends Controller {
	public function index() {
		$this->load->language('extension/module/bannertop');
		$data['text_close'] = $this->language->get('text_close');
		
		$data['bannertop_status'] = $this->config->get('bannertop_status');

		if ($this->config->get('bannertop_image')) {
			$bannertop_image = $this->config->get('bannertop_image');
			$data['bannertop_image'] = $bannertop_image[$this->session->data['language']];
		} else {
			$data['bannertop_image'] = false;
		}
		
		if ($this->config->get('bannertop_time')) {$bannertop_time = $this->config->get('bannertop_time');} else {$bannertop_time = 0;}
		if (!isset($this->request->cookie['top_banner'])) {
			$data['top_banner'] = true;
		} else {
			if ($this->request->cookie['top_banner'] == '0') {
				$data['top_banner'] = false;
			} else {
				$data['top_banner'] = true;
			}

		}

		return $this->load->view('extension/module/bannertop', $data);
		
	}
}