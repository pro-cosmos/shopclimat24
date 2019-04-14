<?php
class ControllerExtensionModulecategorycarousel extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('extension/module/categorycarousel');

		$this->document->setTitle($this->language->get('heading_title_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('catalog/category');
		
		$this->load->model('setting/module');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('categorycarousel', $this->request->post);
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
		
		$data['kol_vo_podcateg'] = $this->language->get('kol_vo_podcateg');
		$data['image_resize'] = $this->language->get('image_resize');
		
		$data['setting_module_carousel'] = $this->language->get('setting_module_carousel');
		$data['on_off_categ_2_module_carousel'] = $this->language->get('on_off_categ_2_module_carousel');
		$data['on'] = $this->language->get('on');
		$data['off'] = $this->language->get('off');
		$data['limit_categ_1_module_carousel'] = $this->language->get('limit_categ_1_module_carousel');
		$data['style_module_carousel'] = $this->language->get('style_module_carousel');
		$data['color_categ_1_style_module_carousel'] = $this->language->get('color_categ_1_style_module_carousel');
		$data['font_size_categ_1_style_module_carousel'] = $this->language->get('font_size_categ_1_style_module_carousel');
		$data['color_categ_2_style_module_carousel'] = $this->language->get('color_categ_2_style_module_carousel');
		$data['font_size_categ_2_style_module_carousel'] = $this->language->get('font_size_categ_2_style_module_carousel');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_content_top'] = $this->language->get('text_content_top');
		$data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$data['text_column_left'] = $this->language->get('text_column_left');
		$data['text_column_right'] = $this->language->get('text_column_right');
		
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_layout'] = $this->language->get('entry_layout');
		$data['entry_position'] = $this->language->get('entry_position');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_module'] = $this->language->get('button_add_module');
		$data['button_remove'] = $this->language->get('button_remove');
		
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
				'href' => $this->url->link('extension/module/categorycarousel', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/categorycarousel', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}
		
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/categorycarousel', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/categorycarousel', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['module_id']) && ($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->post['module_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['style_on_off_categ_2_module_carousel'])) {
			$data['style_on_off_categ_2_module_carousel'] = $this->request->post['style_on_off_categ_2_module_carousel'];
		} elseif (!empty($module_info)) {
			$data['style_on_off_categ_2_module_carousel'] = $module_info['style_on_off_categ_2_module_carousel'];
		} else {
			$data['style_on_off_categ_2_module_carousel'] = 1;
		}
		
		if (isset($this->request->post['style_kol_vo_podcateg'])) {
			$data['style_kol_vo_podcateg'] = $this->request->post['style_kol_vo_podcateg'];
		} elseif (!empty($module_info)) {
			$data['style_kol_vo_podcateg'] = $module_info['style_kol_vo_podcateg'];
		} else {
			$data['style_kol_vo_podcateg'] = 4;
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
			$data['width'] = 80;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 80;
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

		$this->response->setOutput($this->load->view('extension/module/categorycarousel', $data));
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/categorycarousel')) {
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
			
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>