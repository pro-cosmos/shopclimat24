<?php
class ControllerExtensionModuleCheapering extends Controller {
	public function index() {
			$this->load->language('extension/module/featured');
			$this->load->language('extension/module/langtemplates');

			$this->load->model('catalog/product');
			$this->load->model('catalog/review');
			$this->load->model('tool/image');
			
			$this->load->model('extension/module/cheapering');
		
			$this->model_extension_module_cheapering->createCheapering();
			
			$this->model_extension_module_cheapering->createCheaperingliveprice();
			
			if (isset($this->request->get['prod_id'])) {
				$product_id = (int)$this->request->get['prod_id'];
			} else {
				$product_id = 0;
			}
			
			$data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$data['text_model'] = $this->language->get('text_model');
			$data['text_stock'] = $this->language->get('text_stock');
			$data['td_more'] = $this->language->get('td_more');
			$data['text_select'] = $this->language->get('text_select');
			$data['text_related'] = $this->language->get('text_related');
			$data['text_tax'] = $this->language->get('text_tax');
			$data['send'] = $this->language->get('send');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');
			$data['text_loading'] = $this->language->get('text_loading');
			
			$data['text_fio'] = $this->language->get('text_fio');
			$data['text_phone'] = $this->language->get('text_phone');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_href'] = $this->language->get('text_href');
			$data['text_comment'] = $this->language->get('text_comment');
			$data['cheapering'] = $this->language->get('cheapering');
			$data['text_options'] = $this->language->get('text_options');
			$data['text_qyantity'] = $this->language->get('text_qyantity');
			$data['text_cheapering'] = $this->language->get('text_cheapering');
			$data['text_cheaper'] = $this->language->get('text_cheaper');
			
			$data['text_success_zakon'] = $this->language->get('text_success_zakon');
			
			$data['close'] = $this->language->get('close');
			
			$data['text_tax'] = $this->language->get('text_tax');
			$data['text_price'] = $this->language->get('text_price');
			
			
			$data['product_id'] = $product_id;

		
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			if ($product_info['image']) {
				$data['image'] = $this->model_tool_image->resize($product_info['image'], 80, 80);
			} else {
				$data['image'] = $this->model_tool_image->resize('no_image.png', 80, 80);
			}
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}
			
			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}
			
			if (!$data['special']) {
				$data['pr'] = str_replace(" ","", $data['price']);
			} else {
				$data['pr'] = str_replace(" ","", $data['special']);
			}
			
			$data['description'] = utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 300) . '..';
			
			$data['link_manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			
			$data['name'] = $product_info['name'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['model'] = $product_info['model'];
			$data['minimum'] = $product_info['minimum'];
			
			if ($this->config->get('cheapering_status_your_name')) {$data['cheapering_status_your_name'] = $this->config->get('cheapering_status_your_name');} else {$data['cheapering_status_your_name'] = false;}
			if ($this->config->get('cheapering_status_phone')) {$data['cheapering_status_phone'] = $this->config->get('cheapering_status_phone');} else {$data['cheapering_status_phone'] = false;}
			if ($this->config->get('cheapering_status_email')) {$data['cheapering_status_email'] = $this->config->get('cheapering_status_email');} else {$data['cheapering_status_email'] = false;}
			if ($this->config->get('cheapering_status_comment')) {$data['cheapering_status_comment'] = $this->config->get('cheapering_status_comment');} else {$data['cheapering_status_comment'] = false;}
			if ($this->config->get('cheapering_format')) {$data['cheapering_format'] = $this->config->get('cheapering_format');} else {$data['cheapering_format'] = false;}
			
			if ($this->config->get('cheapering_text')) {
				$text = $this->config->get('cheapering_text');
				if (isset($text[$this->session->data['language']])) {
					$data['cheapering_text'] = html_entity_decode($text[$this->session->data['language']], ENT_QUOTES, 'UTF-8');
				} else {
					$data['cheapering_text'] = false;
				}
			} else {
				$data['cheapering_text'] = false;
			}
			
			$data['href'] = $this->url->link('product/product', '&product_id=' . $product_id);

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}
			
		
			$data['date'] = date("d.m.Y");

			return $this->response->setOutput($this->load->view('extension/module/cheapering', $data));
			
	}
	public function quick() {
		$this->load->model('extension/module/cheapering');
		
		$json = array();
		
		if ($this->config->get('cheapering_status_your_name')) {if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}}
		if ($this->config->get('cheapering_status_phone')) {if ((utf8_strlen($this->request->post['phone']) < 1) || (utf8_strlen($this->request->post['phone']) > 32)) {
			$json['error']['phone'] = $this->language->get('error_phone');
		}}
		if ($this->config->get('cheapering_status_email')) {if ((utf8_strlen($this->request->post['email']) < 1) || (utf8_strlen($this->request->post['email']) > 32)) {
			$json['error']['email'] = $this->language->get('error_email');
		}}
		if ($this->config->get('cheapering_text')) {
				$text = $this->config->get('cheapering_text');
				if (isset($text[$this->session->data['language']])) {
					$data['cheapering_text'] = html_entity_decode($text[$this->session->data['language']], ENT_QUOTES, 'UTF-8');
				} else {
					$data['cheapering_text'] = false;
				}
			} else {
				$data['cheapering_text'] = false;
			}
		if (!isset($this->request->post['zachita']) &&  ($this->config->get('cheapering_format') == "checkbox" && $data['cheapering_text'])) {
			$json['error']['zachita'] = $this->language->get('error_zachita');
		}
		
		if ((utf8_strlen($this->request->post['href']) < 3)) {
			$json['error']['href'] = $this->language->get('error_href');
		}
		
		if (!isset($json['error'])) {
			$json['message'] = $this->model_extension_module_cheapering->writesendquick($this->request->post);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	


}