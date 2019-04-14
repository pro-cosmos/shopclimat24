<?php
class ModelExtensionModuleNewsletters extends Model {
	public function createNewsletter()
	{
			
		$res0 = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."newsletter'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "newsletter` (
				  `news_id` int(11) NOT NULL AUTO_INCREMENT,
				  `news_email` varchar(255) NOT NULL,
				  `news_session` varchar(255) NOT NULL,
				  `news_confirm` varchar(255) NOT NULL,
				  PRIMARY KEY (`news_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		}
		
		
	}
	public function subscribes($data) {
		
		$this->load->language('extension/module/newsletter');
		
		$res = $this->db->query("select * from ". DB_PREFIX ."newsletter where news_email='".$this->db->escape($data['email'])."'");
		if($res->num_rows == 1)
		{
			return $this->language->get('text_alert_error_mail');
		}
		else
		{
		
			if($this->db->query("INSERT INTO " . DB_PREFIX . "newsletter(news_email, news_session, news_confirm) values ('".$this->db->escape($data['email'])."', '".$this->db->escape($data['user_token'])."', '0')"))
			{
				//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
				
				if ($this->config->get('config_name')) {
					$store_name = $this->config->get('config_name');
				}
				
				$text_items = sprintf($this->language->get('text_letter_add'), $_SERVER["HTTP_HOST"], $this->url->link('extension/module/newsletter/confirmadd&email=' . $data['email'] . '&session_id=' . $data['user_token'])) . sprintf($this->language->get('text_letter_remove'), $this->url->link('extension/module/newsletter/confirmremove&email=' . $data['email'] . '&session_id=' . $data['user_token']));
				
				$message  = '<html dir="ltr" lang="en">' . "\n";
				$message .= '  <head>' . "\n";
				$message .= '    <title>' . $this->language->get('text_confirm_email') . '</title>' . "\n";
				$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
				$message .= '  </head>' . "\n";
				$message .= '  <body>' . html_entity_decode($text_items, ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
				$message .= '</html>' . "\n";

				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($data['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($this->language->get('text_confirm_email'), ENT_QUOTES, 'UTF-8'));
				$mail->setHtml($message);
				$mail->send();
				
				return $this->language->get('text_alert_success');
			}
			else
			{
				//$this->response->redirect($this->url->link('common/home', '', 'SSL'));
				return $this->language->get('text_alert_error');
			}
		}
	}
	
	public function confirmadd($news_email, $news_session, $news_confirm) {
		$this->db->query("UPDATE " . DB_PREFIX . "newsletter SET news_confirm = '" . $this->db->escape($news_confirm) . "' WHERE news_email = '" . $this->db->escape($news_email) . "' AND news_session = '" . $this->db->escape($news_session) . "'");
	}
	
	public function confirmremove($news_email, $news_session) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "newsletter` WHERE news_email = '" . $this->db->escape($news_email) . "' AND news_session = '" . $this->db->escape($news_session) . "'");
	}
}