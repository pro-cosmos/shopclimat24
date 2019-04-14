<?php
class ControllerExtensionModuleNewsletter extends Controller {
	public function index() {
		$this->load->language('extension/module/newsletter');
		$this->load->model('extension/module/newsletters');
		
		$this->model_extension_module_newsletters->createNewsletter();

		$data['heading_title_newsletter'] = $this->language->get('heading_title_newsletter');
		$data['text_brands'] = $this->language->get('text_brands');
		$data['text_index'] = $this->language->get('text_index');
		$data['text_footer_letters'] = $this->language->get('text_footer_letters');
		$data['text_button_letters'] = $this->language->get('text_button_letters');
		$data['text_empty_email'] = $this->language->get('text_empty_email');
		$data['text_error_email'] = $this->language->get('text_error_email');
		$data['user_token'] = $this->session->getId();
		
	
		$data['brands'] = array();

		return $this->load->view('extension/module/newsletter', $data);
	}
	public function news()
	{
		$this->load->model('extension/module/newsletters');
		
		$json = array();
		$json['message'] = $this->model_extension_module_newsletters->subscribes($this->request->post);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
		$this->index();
	}
	
	public function confirmadd() {
		$this->load->model('extension/module/newsletters');
		$this->model_extension_module_newsletters->confirmadd($this->request->get['email'], $this->request->get['session_id'], '1');
		
		$this->load->language('extension/module/newsletter');
		$data['text_confirm_add'] = $this->language->get('text_confirm_add');
		$data['text_letter_add_success'] = $this->language->get('text_letter_add_success');
		$data['heading_title_newsletter'] = $this->language->get('heading_title_newsletter');
		
		$this->document->setTitle($this->language->get('text_letter_add_success'));
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$data['success'] = 'add';
		
		$this->response->setOutput($this->load->view('information/newsletterconfirm', $data));
	}
	
	public function confirmremove() {
		$this->load->model('extension/module/newsletters');
		$this->model_extension_module_newsletters->confirmremove($this->request->get['email'], $this->request->get['session_id']);
		
		$this->load->language('extension/module/newsletter');
		$data['text_letter_remove_success'] = $this->language->get('text_letter_remove_success');
		$data['heading_title_newsletter'] = $this->language->get('heading_title_newsletter');
		
		$this->document->setTitle($this->language->get('text_letter_remove_success'));
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$data['success'] = 'remove';
		
		$this->response->setOutput($this->load->view('information/newsletterconfirm', $data));
	}
	
}
