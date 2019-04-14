<?php
class ControllerExtensionModuleSendmailamigo extends Controller {
	public function index() {
		$this->load->language('extension/module/sendmailamigo');
		
		$data['send_mail_amigo'] = $this->language->get('send_mail_amigo');
		$data['button_send'] = $this->language->get('button_send');
		$data['text_upload'] = $this->language->get('text_upload');
		$data['text_empty_email'] = $this->language->get('text_empty_email');
		$data['text_error_email'] = $this->language->get('text_error_email');
		$data['text_h3_amigo'] = $this->language->get('text_h3_amigo');
		$data['text_placeholder_name'] = $this->language->get('text_placeholder_name');
		$data['text_success_send'] = $this->language->get('text_success_send');
		
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status')) {
			$data['captcha'] = preg_replace('|[\s]+|s', ' ', $this->load->controller('extension/captcha/' . $this->config->get('config_captcha')));
		} else {
			$data['captcha'] = '';
		}

		$data['REQUEST_URI'] =  $this->request->server['HTTPS'] . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
		
		$data['brands'] = array();
		
		$data['product_id'] = $this->request->get['product_id'];

		return $this->load->view('extension/module/sendmailamigo', $data);
	}
	public function sendmail() {
		
		$this->load->language('extension/module/sendmailamigo');
		$this->load->language('extension/captcha/basic_captcha');
		
		$json = array();

		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status')) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$json['error']['captcha'] =  $this->language->get('error_captcha');
			}
		}
	
		if (!$this->request->post['fio']) {
			$json['error']['fio'] = $this->language->get('text_placeholder_name');
		}
		
		if (!isset($json['error'])) {
	
			$mess = $this->language->get('text_amigo_before') . $this->request->post['fio'] . $this->language->get('text_amigo') . "<br />";
			
			if ($this->request->post['prod_id']) {
				$this->load->model('catalog/product');
				$product_info = $this->model_catalog_product->getProduct($this->request->post['prod_id']);
				$name = $product_info['name'];
				if ($product_info['image']) {
					$this->load->model('tool/image');
					$thumb = '<div style="float: left; margin-right: 15px;"><a href="' . $this->request->post['href'] . '"><img src="' . $this->model_tool_image->resize($product_info['image'], 80, 80) . '" /></a></div>';
				} else {
					$thumb = '';
				}
				
				$messageHtml = '';
				$messageHtml .= '<div style="clear: both; width: 80%; margin: 0 auto;">';
				$messageHtml .= 	'<br />' . $mess . '<br />';
				$messageHtml .= 	'<div style="border: 1px solid #dddddd; padding-left: 15px;">';
				$messageHtml .= 		$thumb;
				$messageHtml .= 		'<div style="float: left; padding: 0 15px 15px;">';
				$messageHtml .= 			'<h3><a href="' . $this->request->post['href'] . '">' . $name . '</a></h3>';
				$messageHtml .= 			'<a href="' . $this->request->post['href'] . '" style="background: #3f6d98; color: #ffffff; line-height: 32px; font-size: 12px; border-radius: 3px; padding: 10px; margin-bottom: 15px;">' . $this->language->get('text_button_mail') . '</a>';
				$messageHtml .= 		'</div>';
				$messageHtml .= 		'<div style="clear: both;">';
				$messageHtml .= 	'</div>';
				$messageHtml .= '</div>';
			}
			
			$message  = '<html dir="ltr" lang="en">' . "\n";
			$message .= '  <head>' . "\n";
			$message .= '    <title>' . $this->language->get('text_subject_mail') . $_SERVER["HTTP_HOST"] . '</title>' . "\n";
			$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
			$message .= '  </head>' . "\n";
			$message .= '  <body>' . html_entity_decode($messageHtml, ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
			$message .= '</html>' . "\n";
			
			$store_name = $this->config->get('config_name');

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->request->post['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->language->get("text_subject_mail") . $_SERVER["HTTP_HOST"], ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($message);
			$mail->send();
		
			$json['success'] = $this->language->get('text_success_send');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}