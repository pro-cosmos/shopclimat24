<?php
class ControllerExtensionModuleCarous extends Controller {
	public function index($setting) {
		
		static $module = 0;
		
		$this->load->language('extension/module/carous');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		
		$this->load->language('extension/module/langtemplates');
		
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_model'] = $this->language->get('text_model');
		$data['text_stock'] = $this->language->get('text_stock');
		$data['td_more'] = $this->language->get('td_more');
		$data['text_select'] = $this->language->get('text_select');
		
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
		
		$this->document->addStyle('catalog/view/theme/unitystore/stylesheet/carous.css');
		$this->document->addScript('catalog/view/theme/unitystore/js/carous.js');
		
		$this->load->model('catalog/product');
		
		$this->load->model('catalog/carous'); 
		
		$this->load->model('catalog/review'); 

		$this->load->model('tool/image');

		$data['products'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}
		
		$data['width'] = $setting['width'];
		$data['height'] = $setting['height'];
		
		if (isset($setting['parametr'])) {
			$data['parametr'] = $setting['parametr'];
		} else {
			$data['parametr'] = 0;
		}
		
		if (isset($setting['schetchik'])) {
			$data['schetchik'] = $setting['schetchik'];
		} else {
			$data['schetchik'] = 0;
		}
		
		if (isset($setting['attributes_module_setting'])) {
			$data['attributes_module_setting'] = $setting['attributes_module_setting'];
		} else {
			$data['attributes_module_setting'] = 0;
		}
		
		if (isset($setting['limit_attribute'])) {
			$data['limit_attribute'] = $setting['limit_attribute'];
		} else {
			$data['limit_attribute'] = 0;
		}
		
		if (isset($setting['dlina_attribute'])) {
			$dlina_attribute = $setting['dlina_attribute'];
		} else {
			$dlina_attribute = 0;
		}
		
		if (isset($setting['options_module_setting'])) {
			$data['options_module_setting'] = $setting['options_module_setting'];
		} else {
			$data['options_module_setting'] = 0;
		}
		
		if (isset($setting['decription_module_setting'])) {
			$data['decription_module_setting'] = $setting['decription_module_setting'];
		} else {
			$data['decription_module_setting'] = 1;
		}
		
		if (isset($setting['limit_decription_module_setting'])) {
			$data['limit_decription_module_setting'] = $setting['limit_decription_module_setting'];
		} else {
			$data['limit_decription_module_setting'] = 300;
		}
		

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				$carous_info = $this->model_catalog_carous->getCarous($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}
					
					$options = array();

					foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
						$product_option_value_data = array();

						foreach ($option['product_option_value'] as $option_value) {
							if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
								if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
									$price_option = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
								} else {
									$price_option = false;
								}

								$product_option_value_data[] = array(
									'product_option_value_id' => $option_value['product_option_value_id'],
									'option_value_id'         => $option_value['option_value_id'],
									'name'                    => $option_value['name'],
									'image'                   => $this->model_tool_image->resize($option_value['image'], 20, 20),
									'price'                   => $price_option,
									'quantity'                => $option_value['quantity'],
									'price_prefix'            => $option_value['price_prefix']
								);
							}
						}

						$options[] = array(
							'product_option_id'    => $option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $option['option_id'],
							'name'                 => $option['name'],
							'type'                 => $option['type'],
							'value'                => $option['value'],
							'required'             => $option['required']
						);
					}
					
					if ($this->model_catalog_product->getProductAttributes($product_id)) {
						$result_attributes = $this->model_catalog_product->getProductAttributes($product_id);
					} else {
						$result_attributes = false;
					}
					
					$attributes = array();
					if ($result_attributes) {
						foreach ($result_attributes as $key => $value) {
							foreach ($value['attribute'] as $res) {
								$dlina_all_attribute = strlen(strip_tags(html_entity_decode($res['text'], ENT_QUOTES, 'UTF-8')));
								if ($dlina_all_attribute > $dlina_attribute) {$attribute_text_more = "..";} else {$attribute_text_more = "";}
								$attributes[] = array(
									'name' => $res['name'],
									'text' => utf8_substr(strip_tags(html_entity_decode($res['text'], ENT_QUOTES, 'UTF-8')), 0, $dlina_attribute) . $attribute_text_more,
								);
							}
						}
					}
					
					if ($product_info['quantity'] <= 0) {
						$stock = $product_info['stock_status'];
					} else {
						$stock = $this->language->get('text_instock');
					}
					
					
					
					$imgs = $this->model_catalog_product->getProductImages($product_id);
					$imgt = array();
					foreach ($imgs as $imgi) {
						$imgt[] = array(
							'thumb' => $this->model_tool_image->resize($imgi['image'], $setting['width'], $setting['height']),
						);
					}
					
					$review_total = $this->model_catalog_review->getTotalReviewsByProductId($product_id);

					$data['products'][] = array(
						'product_id'  			=> $product_info['product_id'],
						'thumb'       			=> $image,
						'images'      			=> $imgt,
						'name'        			=> $product_info['name'],
						'price'       			=> $price,
						'special'     			=> $special,
						'tax'         			=> $tax,
						'rating'      			=> $rating,
						'description' 			=> utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $data['limit_decription_module_setting']) . '..',
						'href'       			=> $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
						'options' 				=> $options,
						'attributes' 			=> $attributes,
						'review_total'    		=> $review_total,
						'manufacturer'  		=> $product_info['manufacturer'],
						'link_manufacturers' 	=> $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']),
						'model'  				=> $product_info['model'],
						'stock'  				=> $stock,
						'year'  				=> $carous_info['date_end'] ? explode("-", $carous_info['date_end'])[0] : false,
						'mounth'  				=> $carous_info['date_end'] ? explode("-", $carous_info['date_end'])[1] : false,
						'day'  					=> $carous_info['date_end'] ? explode("-", $carous_info['date_end'])[2] : false,
					);
				}
			}
		}
		
		$data['module'] = $module++;

		if ($data['products']) {
			return $this->load->view('extension/module/carous', $data);
		}
	}
	
	public function review() {
		$this->load->language('product/product');
		$this->load->language('extension/module/langtemplates');
		
		$this->load->model('catalog/review');

		$data['text_no_reviews'] = $this->language->get('text_no_reviews');
		$data['napisal'] = $this->language->get('napisal');
		$data['ot'] = $this->language->get('ot');
		$data['text_all_reviews'] = $this->language->get('text_all_reviews');
		$data['text_add_reviews'] = $this->language->get('text_add_reviews');
		$data['text_reviews_popup'] = $this->language->get('text_reviews_popup');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_review'] = $this->language->get('entry_review');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_good'] = $this->language->get('entry_good');
		$data['entry_bad'] = $this->language->get('entry_bad');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_write'] = $this->language->get('text_write');
		$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
		$data['text_note'] = $this->language->get('text_note');
		$data['text_tags'] = $this->language->get('text_tags');
		$data['button_continue'] = $this->language->get('button_continue');
		
		if ($this->customer->isLogged()) {
			$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
		} else {
			$data['customer_name'] = '';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 1;
		}
		
		if (isset($this->request->get['popup_all'])) {
			$data['popup_all'] = $this->request->get['popup_all'];
		} else {
			$data['popup_all'] = false;
		}
		
		if (isset($this->request->get['popup_add'])) {
			$data['popup_add'] = $this->request->get['popup_add'];
		} else {
			$data['popup_add'] = false;
		}
		
		if ($this->config->get('captcha_' .$this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
		} else {
			$data['captcha'] = '';
		}
		
		$data['review_status'] = $this->config->get('config_review_status');
		
		if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
			$data['review_guest'] = true;
		} else {
			$data['review_guest'] = false;
		}
		
		$data['product_id'] = $this->request->get['prod_id'];

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['prod_id']);
		
		if ($review_total > 0) {
			$data['add_reviews'] = $this->url->link('extension/module/carous/review', 'prod_id=' . $this->request->get['prod_id']);
		} else {
			$data['add_reviews'] = false;
		}

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['prod_id'], ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}
		
		
		$reviewpagination = new Reviewpagination();
		$reviewpagination->total = $review_total;
		$reviewpagination->page = $page;
		$reviewpagination->product_id = $this->request->get['prod_id'];
		$reviewpagination->limit = $limit;
		$reviewpagination->url = $this->url->link('extension/module/carous/review', 'prod_id=' . $this->request->get['prod_id'] . '&page={page}');
		
		if ($data['popup_all']) {$reviewpagination->popup_all = true;} else {$reviewpagination->popup_all = false;}

		$data['pagination'] = $reviewpagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($review_total - $limit)) ? $review_total : ((($page - 1) * $limit) + $limit), $review_total, ceil($review_total / $limit));

		$this->response->setOutput($this->load->view('extension/module/carous_review', $data));
	}
	
}