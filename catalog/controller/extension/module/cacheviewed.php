<?php
class ControllerExtensionModuleCacheviewed extends Controller {
	public function index() {
		
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
		
		$this->load->language('extension/module/langtemplates');
		
		$data['text_viewed'] = $this->language->get('text_viewed');
		
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		
		if ($this->config->get('cacheviewed_status')) {
			if (isset($this->request->cookie['viewed_openstore'])) {
				$result_viewed = $this->request->cookie['viewed_openstore'];
			}
		}
		$result = array();
		if (isset($result_viewed)) {
			if (strpos($result_viewed, ",") !== false) {
				$result = explode(',', $result_viewed);
			} else {
				$result[] = $result_viewed;
			}
			$result = array_unique($result);
		}
		
		$data['result'] = array();
		
		if ($result) {
			foreach ($result as $key => $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
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
				if ($product_info) {
					$data['result'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'price'       => $price,
						'special'     => $special,
						'tax'     	  => $tax,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		return $this->load->view('extension/module/cacheviewed', $data);
	}
}