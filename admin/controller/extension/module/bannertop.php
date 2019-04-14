<?php
class ControllerExtensionModuleBannertop extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('extension/module/bannertop');

		$this->document->setTitle($this->language->get('heading_title_title'));
		
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('bannertop', $this->request->post);
			
			$this->model_setting_setting->editSetting('module_bannertop', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_close'] = $this->language->get('entry_close');
		$data['entry_polchasa'] = $this->language->get('entry_polchasa');
		$data['entry_one_shas'] = $this->language->get('entry_one_shas');
		$data['entry_one_day'] = $this->language->get('entry_one_day');
		$data['entry_one_nedelya'] = $this->language->get('entry_one_nedelya');
		$data['entry_one_mesyas'] = $this->language->get('entry_one_mesyas');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/bannertop', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/bannertop', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		$data['action'] = $this->url->link('extension/module/bannertop', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['bannertop_status'])) {
			$data['bannertop_status'] = $this->request->post['bannertop_status'];
		} else {
			$data['bannertop_status'] = $this->config->get('bannertop_status');
		}
		
		if (isset($this->request->post['module_bannertop_status'])) {
			$data['module_bannertop_status'] = $this->request->post['module_bannertop_status'];
		} else {
			$data['module_bannertop_status'] = $this->config->get('module_bannertop_status');
		}
		
		if (isset($this->request->post['bannertop_time'])) {
			$data['bannertop_time'] = $this->request->post['bannertop_time'];
		} else {
			$data['bannertop_time'] = $this->config->get('bannertop_time');
		}
		
		
		$data['session'] = $this->session;
		$data['load'] = $this->load;
		
		$this->load->model('tool/image');
		$data['model_tool_image'] = $this->model_tool_image;
		
		$data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		if (isset($this->request->post['bannertop_image'])) {
			$data['bannertop_image'] = $this->model_tool_image->resize($this->request->post['bannertop_image'], 100, 100);
		} elseif ($this->config->get('bannertop_image')) {
			$data['bannertop_image'] = $this->config->get('bannertop_image');
		} else {
			$data['bannertop_image'] = array(
				'ru-ru' => 'Универсальный Многомодульный Шаблон для продажи товаров различной тематики. Встроенные в Шаблон модули ускоряют продажу товаров для Вашего клиента',
				'en-gb' => 'Universal Multimodular Template for selling goods of various subjects. Templates built into the Template speed up the sale of goods for your client'
			);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/bannertop', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/bannertop')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}