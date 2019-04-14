<?php
class ControllerExtensionModuleDivshadow extends Controller {
	public function index() {
			$this->load->language('extension/module/featured');
			$this->load->language('extension/module/langtemplates');

			$this->load->model('catalog/product');
			$this->load->model('catalog/review');
			$this->load->model('tool/image');
			
			$this->load->model('extension/module/quickpay');
			$this->model_extension_module_quickpay->createQuickpayliveprice();
			
			if (isset($this->request->get['product_id'])) {
				$product_id = (int)$this->request->get['product_id'];
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
			$data['button_cart'] = $this->language->get('button_cart');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');
			$data['text_loading'] = $this->language->get('text_loading');
			$data['quick_button'] = $this->language->get('quick_button');
			$data['text_tax'] = $this->language->get('text_tax');
			$data['text_write'] = $this->language->get('text_write');
			
			$data['entry_qty'] = $this->language->get('entry_qty');
			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_review'] = $this->language->get('entry_review');
			$data['entry_rating'] = $this->language->get('entry_rating');
			$data['entry_good'] = $this->language->get('entry_good');
			$data['entry_bad'] = $this->language->get('entry_bad');

			$data['button_cart'] = $this->language->get('button_cart');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');
			$data['button_upload'] = $this->language->get('button_upload');
			$data['button_continue'] = $this->language->get('button_continue');
			
			$data['text_note'] = $this->language->get('text_note');
			$data['text_tags'] = $this->language->get('text_tags');
			$data['text_related'] = $this->language->get('text_related');
			$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
			$data['text_loading'] = $this->language->get('text_loading');
			
			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}
			
			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}
			$data['text_log'] = sprintf($this->language->get('text_log'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
			
			$data['tab_description'] = $this->language->get('tab_description');
			$data['tab_attribute'] = $this->language->get('tab_attribute');
			$data['tab_general'] = $this->language->get('tab_general');
			
			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product_id);
			$data['review_status'] = $this->config->get('config_review_status');
			
			$data['product_id'] = $product_id;
		
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			
			if ($product_info['image']) {
				$data['image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
			} else {
				$data['image'] = $this->model_tool_image->resize('no_image.png', $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
			}
			
			if ($product_info['image']) {
				$data['image_addit'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'));
			} else {
				$data['image_addit'] = $this->model_tool_image->resize('no_image.png', $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'));
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
			
			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($product_id);

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'))
				);
			}
			
			$data['link_manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			
			$data['name'] = $product_info['name'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['model'] = $product_info['model'];
			$data['minimum'] = $product_info['minimum'];
			
			$data['href'] = $this->url->link('product/product', '&product_id=' . $product_id);

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}
			
			if ($this->model_catalog_product->getProductAttributes($product_id)) {
					$result_attributes = $this->model_catalog_product->getProductAttributes($product_id);
				} else {
					$result_attributes = false;
				}
				
				$attributes = array();
				foreach ($result_attributes as $res) {
					$dlina_all_attribute = strlen(strip_tags(html_entity_decode($attribute['text'], ENT_QUOTES, 'UTF-8')));
					if ($dlina_all_attribute > $dlina_attribute) {$attribute_text_more = "..";} else {$attribute_text_more = "";}
					$attributes[] = array(
						'name' => $res['name'],
						'text' => utf8_substr(strip_tags(html_entity_decode($res['text'], ENT_QUOTES, 'UTF-8')), 0, $dlina_attribute) . $attribute_text_more,
					);
				}
			
			if ($this->model_catalog_product->getProductOptions($product_id)) {
				$data['options'] = array();

				foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
							} else {
								$price = false;
							}

							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $option_value['image'] ? $this->model_tool_image->resize($option_value['image'], 50, 50) : '',
								'price'                   => $price,
								'quantity'             	  => $option_value['quantity'],
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}

					$data['options'][] = array(
						'product_option_id'    => $option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $option['option_id'],
						'name'                 => $option['name'],
						'type'                 => $option['type'],
						'value'                => $option['value'],
						'required'             => $option['required']
					);
				}
			} else {
				$data['options'] = false;
			}

			return $this->response->setOutput($this->load->view('extension/module/divshadow', $data));
			
	}
	
	public function readquickliveprice() {
		$this->load->model('extension/module/quickpay');
		
		$results = $this->model_extension_module_quickpay->getQuickpayliveprice();
		
		$this->load->model('catalog/product');
		
		$json = array();
		
		foreach($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);
			
			if ((float)$product_info['price']) {
				$price = $product_info['price'];
			} else {
				$price = false;
			}
			
			if ((float)$product_info['special']) {
				$special = $product_info['special'];
			} else {
				$special = false;
			}
			
			if ($this->config->get('config_tax')) {$tax = (float)$product_info['special'] ? $product_info['special'] : $product_info['price'];} else {$tax = false;}
			
			if ($result['option'] != '0') {
				$options = $this->model_extension_module_quickpay->getOptions($result['option'], $result['product_id'], $result['qyantity']);
				
				$option_data = array();
				
				foreach ($options as $option) {

					if ($option['price_prefix'] == '+') {
						$price += $option['price'];
						if ($special) {$special += $option['price'];} else {$special = false;}
						if ($tax) {$tax += $option['price'];}
					} elseif ($option['price_prefix'] == '-') {
						$price -= $option['price'];
						if ($special) {$special -= $option['price'];} else {$special = false;}
						if ($tax) {$tax -= $option['price'];}
					}
					
				}
			}
			
			$qyantity = $result['qyantity'];
			$tax_class_id = $product_info['tax_class_id'];
			
		}
		if (!$special) {
			$json['price_system'][] = $this->currency->format($this->tax->calculate($price*$qyantity, $tax_class_id, $this->config->get('config_tax')), $this->session->data['currency']);
		} else {
			$json['price_system'][] = $this->currency->format($this->tax->calculate($special*$qyantity, $tax_class_id, $this->config->get('config_tax')), $this->session->data['currency']);
		}
		
		

		$json['price'][] = $this->currency->format($this->tax->calculate($price*$qyantity, $tax_class_id, $this->config->get('config_tax')), $this->session->data['currency']);
		$json['special'][] = $this->currency->format($this->tax->calculate($special*$qyantity, $tax_class_id, $this->config->get('config_tax')), $this->session->data['currency']);
		if ($tax) {$json['tax'][] = $this->currency->format($tax, $this->session->data['currency']);}
		
		$json['special_noformat'][] = $special;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function quickliveprice() {
		$this->load->model('extension/module/quickpay');
		
		
		
		$json = array();
		$json['message'] = $this->model_extension_module_quickpay->writesendquickliveprice($this->request->post);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
}