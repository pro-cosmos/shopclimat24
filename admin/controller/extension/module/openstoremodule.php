<?php
class ControllerExtensionModuleOpenstoremodule extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/openstoremodule');

		$this->document->setTitle($this->language->get('heading_title_title'));
		$data['heading_title'] = $this->language->get('heading_title');
		$data['entry_banner_left'] = $this->language->get('entry_banner_left');
		$data['entry_banner_center'] = $this->language->get('entry_banner_center');
		$data['entry_product_right'] = $this->language->get('entry_product_right');
		$data['entry_product_bottom'] = $this->language->get('entry_product_bottom');
		$data['help_product_right'] = $this->language->get('help_product_right');
		$data['entry_product_date_end'] = $this->language->get('entry_product_date_end');
		
		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('openstoremodule', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		
		$this->load->model('design/banner');
		$data['banners'] = $this->model_design_banner->getBanners();

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
				'href' => $this->url->link('extension/module/openstoremodule', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/openstoremodule', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/openstoremodule', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/openstoremodule', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
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
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		if (isset($this->request->post['status_left'])) {
			$data['status_left'] = $this->request->post['status_left'];
		} elseif (!empty($module_info['status_left'])) {
			$data['status_left'] = $module_info['status_left'];
		} else {
			$data['status_left'] = '';
		}
		if (isset($this->request->post['status_center'])) {
			$data['status_center'] = $this->request->post['status_center'];
		} elseif (!empty($module_info['status_center'])) {
			$data['status_center'] = $module_info['status_center'];
		} else {
			$data['status_center'] = '';
		}
		if (isset($this->request->post['status_special'])) {
			$data['status_special'] = $this->request->post['status_special'];
		} elseif (!empty($module_info['status_special'])) {
			$data['status_special'] = $module_info['status_special'];
		} else {
			$data['status_special'] = '';
		}
		if (isset($this->request->post['status_featured'])) {
			$data['status_featured'] = $this->request->post['status_featured'];
		} elseif (!empty($module_info['status_featured'])) {
			$data['status_featured'] = $module_info['status_featured'];
		} else {
			$data['status_featured'] = '';
		}
		if (isset($this->request->post['banner_id_left'])) {
			$data['banner_id_left'] = $this->request->post['banner_id_left'];
		} elseif (!empty($module_info)) {
			$data['banner_id_left'] = $module_info['banner_id_left'];
		} else {
			$data['banner_id_left'] = '';
		}
		
		if (isset($this->request->post['banner_id_center'])) {
			$data['banner_id_center'] = $this->request->post['banner_id_center'];
		} elseif (!empty($module_info)) {
			$data['banner_id_center'] = $module_info['banner_id_center'];
		} else {
			$data['banner_id_center'] = '';
		}

		$this->load->model('catalog/product');

		if (!empty($this->request->post['product_special'])) {
			$product_specials = $this->request->post['product_special'];
		} elseif (!empty($module_info['product_special'])) {
			$product_specials = $module_info['product_special'];
		} else {
			$product_specials = array();
		}
		$data['product_special'] = array();
		foreach ($product_specials as $product_id) {
			$product_info_special = $this->model_catalog_product->getProduct($product_id);

			if ($product_info_special) {
				$data['product_special'][] = array(
					'product_id' => $product_info_special['product_id'],
					'name'       => $product_info_special['name']
				);
			}
		}
		if (!empty($this->request->post['product_featured'])) {
			$product_featureds = $this->request->post['product_featured'];
		} elseif (!empty($module_info['product_featured'])) {
			$product_featureds = $module_info['product_featured'];
		} else {
			$product_featureds = array();
		}
		$data['product_featured'] = array();
		foreach ($product_featureds as $product_id) {
			$product_info_featured = $this->model_catalog_product->getProduct($product_id);

			if ($product_info_featured) {
				$data['product_featured'][] = array(
					'product_id' => $product_info_featured['product_id'],
					'name'       => $product_info_featured['name']
				);
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/openstoremodule', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/openstoremodule')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}
}
