<?php
class ModelExtensionModuleQuickpay extends Model {
	public function createQuickpay() {
			
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."quickpay'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "quickpay` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `token` varchar(255) NOT NULL,
				  `date` varchar(255) NOT NULL,
				  `product_id` varchar(255) NOT NULL,
				  `qyantity` int(11) NOT NULL,
				  `option` varchar(255) NOT NULL,
				  `price` int(11) NOT NULL,
				  `name` varchar(255) NOT NULL,
				  `phone` varchar(255) NOT NULL,
				  `email` varchar(255) NOT NULL,
				  `comment` longtext NOT NULL,
				  `status` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		}
		
		
	}
	
	public function createQuickpayliveprice() {
			
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."quickpayliveprice'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "quickpayliveprice` (
				  `token` varchar(255) NOT NULL,
				  `product_id` int(11) NOT NULL,
				  `option` varchar(255) NOT NULL,
				  `qyantity` int(11) NOT NULL,
				  PRIMARY KEY (`token`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
			$this->db->query("INSERT INTO " . DB_PREFIX . "quickpayliveprice SET product_id = '', `option` = '', token = '" . $this->session->getId() . "'");
		}
		
		
	}
	
	public function writesendquick($data) {
		
		$this->load->language('extension/module/langtemplates');
		
		if (isset($data['option'])) {$options = " `option` = '" . $this->db->escape(json_encode($data['option'])) . "'";} else {$options = " `option` = '" . 0 . "'";}
		if($this->db->query("INSERT INTO " . DB_PREFIX . "quickpay SET date = '" . $this->db->escape($data['date']) . "', product_id = '" . (int)$data['product_id'] . "', qyantity = '" . (int)$data['qyantity'] . "'," . $options . ", price = '" . (int)$data['price'] . "', name = '" . $this->db->escape($data['name']) . "', phone = '" . $this->db->escape($data['phone']) . "', email = '" . $this->db->escape($data['email']) . "', comment = '" . $this->db->escape($data['comment']) . "', status = '0'")) {
			//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
			
			
			$email = $this->config->get('config_email');

			
			if (isset($data['product_id'])) {
				$product_id = (int)$data['product_id'];
			} else {
				$product_id = 0;
			}
			
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
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
			
			if (isset($data['option']) && $data['option'] != '0') {
				$this->load->model('extension/module/quickpay');
				$options = $this->model_extension_module_quickpay->getOptions($data['option'], $data['product_id'], $data['qyantity']);
				
				$option_data = array();
				
				$option_data = $this->language->get('option_klient') . "<br />";
				
				if ($options) {
					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
							$value = (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value) . " ";
						} else {
							$value = "";
						}
						$option_data .= $option['name'] . ": " . $value . "<br />";
					}
					$option_data .= "<br />";
				}
			} else {
				$option_data = "";
			}
			
			$price_tovar = 0;
			
			if (!$data['special']) {$price_tovar = $data['price'];} else {$price_tovar = $data['special'];}
			
			$mess = $this->language->get('name_klient') . $data['name'] . '<br /><br />' . $this->language->get('telefon_klient') . $data['phone'] . '<br /><br />' . $this->language->get('email_klient') . $data['email'] . '<br /><br />'  . $this->language->get('tovar_klient') . $product_info['name'] . '<br /><br />' . $option_data . $this->language->get('href_tovar_klient') . $this->url->link('product/product', '&product_id=' . $product_id) . '<br /><br />' . $this->language->get('qyantity_klient') . $data['qyantity'] . '<br /><br />' . $this->language->get('text_cheaper_comment') . $data['comment'];

			$message  = '<html dir="ltr" lang="en">' . "\n";
			$message .= '  <head>' . "\n";
			$message .= '    <title>' . $this->language->get('text_quickpay_mail') . $_SERVER["HTTP_HOST"] . '</title>' . "\n";
			$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
			$message .= '  </head>' . "\n";
			$message .= '  <body>' . html_entity_decode($mess, ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
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

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($email);
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->language->get("text_quickpay_mail") . $_SERVER["HTTP_HOST"], ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($message);
			$mail->send();
			
			
			return $this->language->get('success_send');
		} else {
			//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
			return $this->language->get('error_send');
		}
	}
	
	public function writesendquickCart($products, $post) {
		
		$this->load->language('extension/module/langtemplates');
		$success = false;
		$count_tovar = 0;
		$text_mail = '';
		if ($products) {
			$text_mail .= $this->language->get('tovar_klient') . '<br /><br />';
			foreach ($products as $key => $product) {
				$count_tovar++;
				if (isset($product['option'])) {$options = " `option` = '" . $this->db->escape(json_encode($product['option'])) . "'";} else {$options = " `option` = '" . 0 . "'";}
				if($this->db->query("INSERT INTO " . DB_PREFIX . "quickpay SET date = '" . $this->db->escape($post['date']) . "', product_id = '" . (int)$product['product_id'] . "', qyantity = '" . (int)$product['qyantity'] . "'," . $options . ", price = '" . (int)$product['price'] . "', name = '" . $this->db->escape($post['name']) . "', phone = '" . $this->db->escape($post['phone']) . "', email = '" . $this->db->escape($post['email']) . "', comment = '" . $this->db->escape($post['comment']) . "', status = '0'")) {
					$success = true;
				}
				
				if (isset($product['product_id'])) {
					$product_id = (int)$product['product_id'];
				} else {
					$product_id = 0;
				}
				
				$this->load->model('catalog/product');
				$product_info = $this->model_catalog_product->getProduct($product_id);
				
				if (isset($product['option']) && $product['option'] != '0') {
					$this->load->model('extension/module/quickpay');
					$options = $this->model_extension_module_quickpay->getOptions($product['option'], $product['product_id'], $product['qyantity']);
					
					$option_data = array();
					
					$option_data = $this->language->get('option_klient') . "<br />";
					
					if ($options) {
						foreach ($options as $option) {
							if ($option['type'] != 'file') {
								$value = $option['value'];
								$value = (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value) . " ";
							} else {
								$value = "";
							}
							$option_data .= $option['name'] . ": " . $value . "<br />";
						}
						$option_data .= "<br />";
					}
				} else {
					$option_data = "";
				}
				
				$text_mail .= '<b>' . $count_tovar . '. ' . $product_info['name']. '</b><br /><br />' . $option_data . $this->language->get('href_tovar_klient') . $this->url->link('product/product', '&product_id=' . $product['product_id']) . '<br /><br />' . $this->language->get('qyantity_klient') . $product['qyantity'] . '<br /><br />';
			}
			
		}
		
		if ($success == true) {
			
			if ($products) {
				foreach ($products as $key => $product) {
					$this->cart->remove($product['cart_id']);
					unset($this->session->data['vouchers'][$product['cart_id']]);
				}
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['reward']);
			}
			
			$email = $this->config->get('config_email');

			$mess = $this->language->get('name_klient') . $post['name'] . '<br /><br />' . $this->language->get('telefon_klient') . $post['phone'] . '<br /><br />' . $this->language->get('email_klient') . $post['email'] . '<br /><br />'  . $text_mail . $this->language->get('text_cheaper_comment') . $post['comment'];

			$message  = '<html dir="ltr" lang="en">' . "\n";
			$message .= '  <head>' . "\n";
			$message .= '    <title>' . $this->language->get('text_quickpay_mail') . $_SERVER["HTTP_HOST"] . '</title>' . "\n";
			$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
			$message .= '  </head>' . "\n";
			$message .= '  <body>' . html_entity_decode($mess, ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
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

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($email);
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->language->get("text_quickpay_mail") . $_SERVER["HTTP_HOST"], ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($message);
			$mail->send();
			
			$json['message'] = $this->language->get('success_send');
			
			$json['total'] = ($this->cart->countProducts() != null ? $this->cart->countProducts() : 0);
			
			return $json;
			
		} else {
			return $this->language->get('error_send');
		}
	}
	
	public function writesendquickliveprice($data) {
		if (isset($data['option'])) {$options = " `option` = '" . $this->db->escape(json_encode($data['option'])) . "'";} else {$options = " `option` = '" . 0 . "'";}
		
		$test_empty_token = $this->db->query("SELECT * FROM " . DB_PREFIX . "quickpayliveprice WHERE token = '" . $this->session->getId() . "'");
		
		if ($test_empty_token->num_rows and $test_empty_token->row['product_id'] != 0) {
			$this->db->query("UPDATE " . DB_PREFIX . "quickpayliveprice SET product_id = '" . (int)$data['product_id'] . "'," . $options . ", qyantity = '" . (int)$data['qyantity'] . "' WHERE token = '" . $this->session->getId() . "'");
		} else {
			$this->db->query("INSERT INTO " . DB_PREFIX . "quickpayliveprice SET product_id = '" . (int)$data['product_id'] . "'," . $options . ", qyantity = '" . (int)$data['qyantity'] . "', token = '" . $this->session->getId() . "'");
		}
	}
	
	public function getQuickpayliveprice() {

		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."quickpayliveprice WHERE token = '" . $this->session->getId() . "'");

		return $query->rows;
	}
	
	public function DeleteQuickpayliveprice() {
		$this->db->query("DELETE FROM ". DB_PREFIX ."quickpayliveprice WHERE token = '" . $this->session->getId() . "'");
	}
	
	
	public function getOptions($id_option, $product_id, $quantity) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;
				
				$option_data = array();
				
				if ($id_option) {
				foreach ($id_option as $product_option_id => $value) {
					$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}

								if ($option_value_query->row['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row['weight'];
								} elseif ($option_value_query->row['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row['weight'];
								}

								if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
									$stock = false;
								}

								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity'],
									'subtract'                => $option_value_query->row['subtract'],
									'price'                   => $option_value_query->row['price'],
									'price_prefix'            => $option_value_query->row['price_prefix'],
									'points'                  => $option_value_query->row['points'],
									'points_prefix'           => $option_value_query->row['points_prefix'],
									'weight'                  => $option_value_query->row['weight'],
									'weight_prefix'           => $option_value_query->row['weight_prefix']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => '',
								'subtract'                => '',
								'price'                   => '',
								'price_prefix'            => '',
								'points'                  => '',
								'points_prefix'           => '',
								'weight'                  => '',
								'weight_prefix'           => ''
							);
						}
					}
				}
				}
				
		return $option_data;
	}
	
}