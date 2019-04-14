<?php
class ControllerExtensionModuleLoginpopup extends Controller {
	public function index() {

			$this->load->language('extension/module/langtemplates');
					
			$data['login_enter'] = $this->language->get('login_enter');
			$data['text_email_login'] = $this->language->get('text_email_login');
			$data['text_password'] = $this->language->get('text_password');
			$data['text_forgotten'] = $this->language->get('text_forgotten');
			$data['button_login'] = $this->language->get('button_login');
			$data['login_en'] = $this->language->get('login_en');
			$data['klient'] = $this->language->get('klient');
			$data['text_login_popup'] = $this->language->get('text_login_popup');
			
			if (isset($this->request->post['email'])) {$data['$email'] = $this->request->post['email'];} else {$data['email'] = '';}
			if (isset($this->request->post['password'])) {$data['password'] = $this->request->post['password'];} else {$data['password'] = '';}
			
			$data['account_login'] = $this->url->link('account/login', '', 'SSL');
			$data['account_forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
			$data['account_register'] = $this->url->link('account/register', '', 'SSL');
			
			return $this->response->setOutput($this->load->view('extension/module/loginpopup', $data));
			
	}

}