<?php
class ControllerExtensionModuleOpenstoremodule extends Controller {
	public function index($setting) {
		
		static $module = 0;
		
		$this->load->language('extension/module/openstoremodule');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['text_end'] = $this->language->get('text_end');
		$data['text_empty_end'] = $this->language->get('text_empty_end');

		$this->load->model('catalog/product');
		$this->load->model('catalog/dateend');
		
		$this->load->model('tool/image');
		
		$this->load->model('design/banner');
		
		if (!empty($setting['status_left'])) {
			$data['status_left'] = $setting['status_left'];
		} else {
			$data['status_left'] = false;
		}
		if (!empty($setting['status_center'])) {
			$data['status_center'] = $setting['status_center'];
		} else {
			$data['status_center'] = false;
		}
		if (!empty($setting['status_special'])) {
			$data['status_special'] = $setting['status_special'];
		} else {
			$data['status_special'] = false;
		}
		if (!empty($setting['status_featured'])) {
			$data['status_featured'] = $setting['status_featured'];
		} else {
			$data['status_featured'] = false;
		}
		$data['banners_left'] = array();
		if (!empty($setting['banner_id_left'])) {
			$results = $this->model_design_banner->getBanner($setting['banner_id_left']);
			foreach ($results as $result) {
				if (is_file(DIR_IMAGE . $result['image'])) {
					$data['banners_left'][] = array(
						'title' => $result['title'],
						'link'  => $result['link'],
						'image' => "/image/" . $result['image']
					);
				}
			}
		}
		$data['banners_center'] = array();
		if (!empty($setting['banner_id_center'])) {
			$results = $this->model_design_banner->getBanner($setting['banner_id_center']);
			foreach ($results as $result) {
				if (is_file(DIR_IMAGE . $result['image'])) {
					$data['banners_center'][] = array(
						'title' => $result['title'],
						'link'  => $result['link'],
						'image' => "/image/" . $result['image']
					);
				}
			}
		}
		
		if (!$data['banners_left']) {$data['banners_left'] = false;}
		if (!$data['banners_center']) {$data['status_center'] = false;}
	
		$data['products_special'] = array();
		if (!empty($setting['product_special'])) {
			foreach ($setting['product_special'] as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], 180, 180);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', 180, 180);
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
					$data['products_special'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'date_end'    => $this->model_catalog_dateend->getProductSpecialsDateEnd($product_id),
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}
		
		if (!$data['products_special']) {$data['status_special'] = false;}
		
		$data['products_featured'] = array();
		if (!empty($setting['product_featured'])) {
			foreach ($setting['product_featured'] as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], 80, 80);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', 80, 80);
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
					$data['products_featured'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'date_end'    => $this->model_catalog_dateend->getProductSpecialsDateEnd($product_id),
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}
		
		if (!$data['products_featured']) {$data['status_featured'] = false;}

		$data['module'] = $module++;
		
		return $this->load->view('extension/module/openstoremodule', $data);
		
	}
}