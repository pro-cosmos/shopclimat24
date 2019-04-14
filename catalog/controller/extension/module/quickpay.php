<?php
class ControllerExtensionModuleQuickpay extends Controller {
	public function index() {
			$this->load->language('product/product');
			$this->load->language('extension/module/langtemplates');

			$this->load->model('catalog/product');
			$this->load->model('catalog/review');
			$this->load->model('tool/image');
			
			$this->load->model('extension/module/quickpay');
		
			$this->model_extension_module_quickpay->createQuickpay();
			
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
			$data['button_continue'] = $this->language->get('button_continue');
			
			$data['text_loading'] = $this->language->get('text_loading');
			
			$data['text_fio'] = $this->language->get('text_fio');
			$data['text_phone'] = $this->language->get('text_phone');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_comment'] = $this->language->get('text_comment');
			$data['quick_pay'] = $this->language->get('quick_pay');
			$data['text_options'] = $this->language->get('text_options');
			$data['text_qyantity'] = $this->language->get('text_qyantity');
			$data['text_contacts'] = $this->language->get('text_contacts');
			$data['button_upload'] = $this->language->get('button_upload');
			
			$data['tab_description'] = $this->language->get('tab_description');
			$data['tab_attribute'] = $this->language->get('tab_attribute');
			$data['quick_pay'] = $this->language->get('quick_pay');
			$data['quick_check'] = $this->language->get('quick_check');
			$data['quick_view'] = $this->language->get('quick_view');
			
			$data['text_tax'] = $this->language->get('text_tax');
			$data['text_price'] = $this->language->get('text_price');
			$data['close'] = $this->language->get('close');
			
			$data['button_cart'] = $this->language->get('button_cart');
			$data['cart_text'] = $this->language->get('cart_text');
			$data['text_write'] = $this->language->get('text_write');
			
			$data['entry_qty'] = $this->language->get('entry_qty');
			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_review'] = $this->language->get('entry_review');
			$data['entry_rating'] = $this->language->get('entry_rating');
			$data['entry_good'] = $this->language->get('entry_good');
			$data['entry_bad'] = $this->language->get('entry_bad');
			
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
			$data['text_note'] = $this->language->get('text_note');
			$data['text_tags'] = $this->language->get('text_tags');
			$data['text_related'] = $this->language->get('text_related');
			$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
			$data['text_loading'] = $this->language->get('text_loading');
			$data['text_success_zakon'] = $this->language->get('text_success_zakon');
			
			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product_id);
			$data['review_status'] = $this->config->get('config_review_status');
			
			if ($this->config->get('module_quickpay_status_your_name')) {$data['quickpay_status_your_name'] = $this->config->get('module_quickpay_status_your_name');} else {$data['quickpay_status_your_name'] = false;}
			if ($this->config->get('module_quickpay_status_phone')) {$data['quickpay_status_phone'] = $this->config->get('module_quickpay_status_phone');} else {$data['quickpay_status_phone'] = false;}
			if ($this->config->get('module_quickpay_status_email')) {$data['quickpay_status_email'] = $this->config->get('module_quickpay_status_email');} else {$data['quickpay_status_email'] = false;}
			if ($this->config->get('module_quickpay_status_comment')) {$data['quickpay_status_comment'] = $this->config->get('module_quickpay_status_comment');} else {$data['quickpay_status_comment'] = false;}
			if ($this->config->get('module_quickpay_format')) {$data['quickpay_format'] = $this->config->get('module_quickpay_format');} else {$data['quickpay_format'] = false;}
			
			if ($this->config->get('module_quickpay_text')) {
				$text = $this->config->get('module_quickpay_text');
				$data['quickpay_text'] = html_entity_decode($text[$this->session->data['language']], ENT_QUOTES, 'UTF-8');
			} else {
				$data['quickpay_text'] = false;
			}

			$data['product_id'] = $product_id;

		
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			
			
			if ($product_info['image']) {
				$data['image'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
			} else {
				$data['image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			}
			
			if ($product_info['image']) {
				$data['image_addit'] = $this->model_tool_image->resize($product_info['image'], 40, 40);
			} else {
				$data['image_addit'] = $this->model_tool_image->resize('no_image.png', 40, 40);
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

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);
			$data['description_all'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			
			$data['description'] = utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 300) . '..';
			
			$data['link_manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			
			$data['name'] = $product_info['name'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['model'] = $product_info['model'];
			$data['minimum'] = $product_info['minimum'];
			
			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}
			
			$data['href'] = $this->url->link('product/product', '&product_id=' . $product_id);

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
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
							
							if ($option_value['image'] and $option_value['image'] != "no_image.jpg") {
								$image = $this->model_tool_image->resize($option_value['image'], 20, 20);
								$image_popup = $this->model_tool_image->resize($option_value['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width')*1.5, $this->config->get($this->config->get('config_theme') . '_image_product_height')*1.5);
							} else {
								$image = false;
								$image_popup = false;
							}

							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $image,
								'image_popup'             => $image_popup,
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
			
			$data['date'] = date("d.m.Y");

			return $this->response->setOutput($this->load->view('extension/module/quickpay', $data));
			
	}
	public function quick() {
		$this->load->model('extension/module/quickpay');
		$this->load->model('catalog/product');
		$this->load->language('checkout/cart');
		$json = array();
		
		if ($this->config->get('module_quickpay_status_your_name')) {if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}}
		if ($this->config->get('module_quickpay_status_phone')) {if ((utf8_strlen($this->request->post['phone']) < 1) || (utf8_strlen($this->request->post['phone']) > 32)) {
			$json['error']['phone'] = $this->language->get('error_phone');
		}}
		if ($this->config->get('module_quickpay_status_email')) {if ((utf8_strlen($this->request->post['email']) < 1) || (utf8_strlen($this->request->post['email']) > 32)) {
			$json['error']['email'] = $this->language->get('error_email');
		}}
		if (isset($this->request->post['option'])) {
			$option = array_filter($this->request->post['option']);
		} else {
			$option = array();
		}
		$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
		foreach ($product_options as $product_option) {
			if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
				$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
			}
		}
		
		if ($this->config->get('module_quickpay_text')) {
			$text = $this->config->get('module_quickpay_text');
			$data['quickpay_text'] = html_entity_decode($text[$this->session->data['language']], ENT_QUOTES, 'UTF-8');
		} else {
			$data['quickpay_text'] = false;
		}
		if (!isset($this->request->post['zachita']) &&  ($this->config->get('module_quickpay_format') == "checkbox" && $data['quickpay_text'])) {
			$json['error']['zachita'] = $this->language->get('error_zachita');
		}

		if (!isset($json['error'])) {
			$json['message'] = $this->model_extension_module_quickpay->writesendquick($this->request->post);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function quickCart() {
		$this->load->model('extension/module/quickpay');
		$this->load->model('catalog/product');
		$this->load->language('checkout/cart');
		$json = array();
		
		if ($this->config->get('module_quickpay_status_your_name')) {if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}}
		if ($this->config->get('module_quickpay_status_phone')) {if ((utf8_strlen($this->request->post['phone']) < 1) || (utf8_strlen($this->request->post['phone']) > 32)) {
			$json['error']['phone'] = $this->language->get('error_phone');
		}}
		if ($this->config->get('module_quickpay_status_email')) {if ((utf8_strlen($this->request->post['email']) < 1) || (utf8_strlen($this->request->post['email']) > 32)) {
			$json['error']['email'] = $this->language->get('error_email');
		}}
		if (isset($this->request->post['price']) and ($this->customer->isLogged() || !$this->config->get('config_customer_price'))) {$price = $this->request->post['price'];} else {$price = false;}
		$products = array();
		foreach ($this->cart->getProducts() as $product) {
			$option_array = array();
			foreach ($product['option'] as $option) {
				$option_array[$option['product_option_id']] = $option['product_option_value_id'];
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
					
					$price = $this->currency->format($unit_price, $this->session->data['currency']);
					$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
				} else {
					$price = false;
					$total = false;
				}
			}
			$products[] = array(
				'product_id'    => $product['product_id'],
				'cart_id'       => $product['cart_id'],
				'qyantity'      => $product['quantity'],
				'price'     	=> $price,
				'option'     	=> $option_array,
			);
		}
		
		if ($this->config->get('module_quickpay_text')) {
			$text = $this->config->get('module_quickpay_text');
			$data['quickpay_text'] = html_entity_decode($text[$this->session->data['language']], ENT_QUOTES, 'UTF-8');
		} else {
			$data['quickpay_text'] = false;
		}
		if (!isset($this->request->post['zachita']) &&  ($this->config->get('module_quickpay_format') == "checkbox" && $data['quickpay_text'])) {
			$json['error']['zachita'] = $this->language->get('error_zachita');
		}

		if (!isset($json['error'])) {
			$json = $this->model_extension_module_quickpay->writesendquickCart($products, $this->request->post);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function readquickliveprice() {
		$this->load->model('extension/module/quickpay');
		
		$results = $this->request->post;
		
		$this->load->model('catalog/product');
		
		$json = array();
		
		if ($results) {
			$product_info = $this->model_catalog_product->getProduct($results['product_id']);
			
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
			
			if (isset($results['option'])) {
				if ($results['option'] != '0') {
					$options = $this->model_extension_module_quickpay->getOptions($results['option'], $results['product_id'], $results['qyantity']);
					
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
			}
			
			$qyantity = $results['qyantity'];
			$tax_class_id = $product_info['tax_class_id'];
			
		}
		if (!$special) {
			$json['price_system'][] = $this->currency->format($this->tax->calculate($price, $tax_class_id, $this->config->get('config_tax')), $this->session->data['currency'], '', false)*$qyantity;
		} else {
			$json['price_system'][] = $this->currency->format($this->tax->calculate($special, $tax_class_id, $this->config->get('config_tax')), $this->session->data['currency'], '', false)*$qyantity;
		}
		
		

		$json['price'][] = $this->currency->format($this->tax->calculate($price, $tax_class_id, $this->config->get('config_tax'))*$qyantity, $this->session->data['currency']);
		$json['special'][] = $this->currency->format($this->tax->calculate($special, $tax_class_id, $this->config->get('config_tax'))*$qyantity, $this->session->data['currency']);
		if ($tax) {$json['tax'][] = $this->currency->format($tax*$qyantity, $this->session->data['currency']);}
		
		$json['special_noformat'][] = $special;
		
		$this->model_extension_module_quickpay->DeleteQuickpayliveprice();
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function quickliveprice() {
		$this->load->model('extension/module/quickpay');
		
		
		
		$json = array();
		$json['message'] = $this->model_extension_module_quickpay->readquickliveprice($this->request->post);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
}