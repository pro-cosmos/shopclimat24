<?php
class ControllerProductOrgproduct extends Controller {
	public function index() {

		$this->load->model('catalog/category');
		$this->load->model('catalog/review');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if (isset($category_info['name'])) {$data['category_name'] = $category_info['name'];}
			if (isset($category_info['description'])) {$data['description_org'] = strip_tags(html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8'));}

		}
		
		$data['curr'] = $this->session->data['currency'];

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
		
		$results_reviews = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], 0, 1000);
		$data['ratings'] = array();
		foreach ($results_reviews as $result) {
			$data['ratings'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date('Y-m-d', strtotime($result['date_added']))
			);
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {

			$data['heading_title'] = $product_info['name'];
			
			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'));

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}
			
			

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			} else {
				$data['popup'] = '';
			}

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			} else {
				$data['thumb'] = '';
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->convert($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->convert($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];
			
			$data['upc'] = (int)$product_info['upc'];
			$data['ean'] = (int)$product_info['ean'];
			$data['isbn'] = (int)$product_info['isbn'];
			$data['href'] = $this->url->link('product/product', 'product_id=' . $product_id);
			
			$data['products'] = array();

			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->convert($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->convert($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			return $this->load->view('product/orgproduct', $data);
		}
	}
}
