<?php
class ControllerExtensionModuleCarous extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/carous');

		$this->document->setTitle($this->language->get('heading_title_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('carous', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['help_product'] = $this->language->get('help_product');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['text_parametr'] = $this->language->get('text_parametr');
		$data['text_show_parametr'] = $this->language->get('text_show_parametr');
		$data['text_on'] = $this->language->get('text_on');
		$data['text_off'] = $this->language->get('text_off');
		$data['text_attributes'] = $this->language->get('text_attributes');
		$data['text_show_attributes'] = $this->language->get('text_show_attributes');
		$data['kol_vo_show_attributes'] = $this->language->get('kol_vo_show_attributes');
		$data['limit_show_attributes'] = $this->language->get('limit_show_attributes');
		$data['text_options'] = $this->language->get('text_options');
		$data['text_description'] = $this->language->get('text_description');
		$data['limit_text_description'] = $this->language->get('limit_text_description');
		$data['text_show_description'] = $this->language->get('text_show_description');
		$data['text_schetchik'] = $this->language->get('text_schetchik');
		$data['text_show_schetchik'] = $this->language->get('text_show_schetchik');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
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
				'href' => $this->url->link('extension/module/carous', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/carous', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/carous', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/carous', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		$this->load->model('catalog/product');

		$data['products'] = array();

		if (isset($this->request->post['product'])) {
			$products = $this->request->post['product'];
		} elseif (!empty($module_info)) {
			$products = $module_info['product'];
		} else {
			$products = array();
		}

		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}
		
		if (isset($this->request->post['parametr'])) {
			$data['parametr'] = $this->request->post['parametr'];
		} elseif (!empty($module_info)) {
			$data['parametr'] = $module_info['parametr'];
		} else {
			$data['parametr'] = 1;
		}
		
		if (isset($this->request->post['schetchik'])) {
			$data['schetchik'] = $this->request->post['schetchik'];
		} elseif (!empty($module_info)) {
			$data['schetchik'] = $module_info['schetchik'];
		} else {
			$data['schetchik'] = 1;
		}
		
		if (isset($this->request->post['attributes_module_setting'])) {
			$data['attributes_module_setting'] = $this->request->post['attributes_module_setting'];
		} elseif (!empty($module_info)) {
			$data['attributes_module_setting'] = $module_info['attributes_module_setting'];
		} else {
			$data['attributes_module_setting'] = 0;
		}
		
		if (isset($this->request->post['limit_attribute'])) {
			$data['limit_attribute'] = $this->request->post['limit_attribute'];
		} elseif (!empty($module_info)) {
			$data['limit_attribute'] = $module_info['limit_attribute'];
		} else {
			$data['limit_attribute'] = 3;
		}
		
		if (isset($this->request->post['dlina_attribute'])) {
			$data['dlina_attribute'] = $this->request->post['dlina_attribute'];
		} elseif (!empty($module_info)) {
			$data['dlina_attribute'] = $module_info['dlina_attribute'];
		} else {
			$data['dlina_attribute'] = 40;
		}
		
		
		
		if (isset($this->request->post['decription_module_setting'])) {
			$data['decription_module_setting'] = $this->request->post['decription_module_setting'];
		} elseif (!empty($module_info)) {
			$data['decription_module_setting'] = $module_info['decription_module_setting'];
		} else {
			$data['decription_module_setting'] = 1;
		}
		
		if (isset($this->request->post['limit_decription_module_setting'])) {
			$data['limit_decription_module_setting'] = $this->request->post['limit_decription_module_setting'];
		} elseif (!empty($module_info)) {
			$data['limit_decription_module_setting'] = $module_info['limit_decription_module_setting'];
		} else {
			$data['limit_decription_module_setting'] = 300;
		}
		
		

		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 5;
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 250;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 250;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/carous', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/carous')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		return !$this->error;
	}
}