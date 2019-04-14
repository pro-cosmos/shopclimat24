<?php  
class ControllerProductNewsall extends Controller {
	public function index() {
		$this->load->model('extension/module/newstemplate');
		
		$this->load->language('extension/module/newstemplate');
		$data['more_news'] = $this->language->get('more_news');
		
		$this->document->setTitle($this->language->get('newss'));
		
		$this->load->model('tool/image');
				
		$modules = $this->model_extension_module_newstemplate->getNewstemplate();
		
		$data['modules_news'] = array();
		
		if ($this->config->get('newstemplate_limit_description')) {$limit_description = $this->config->get('newstemplate_limit_description');} else {$limit_description = '60';}
		if ($this->config->get('newstemplate_image_width')) {$width = $this->config->get('newstemplate_image_width');} else {$width = '100';}
		if ($this->config->get('newstemplate_image_height')) {$height = $this->config->get('newstemplate_image_height');} else {$height = '100';}
		if ($this->config->get('newstemplate_limit_description_all')) {$limit_description_all = $this->config->get('newstemplate_limit_description_all');} else {$limit_description_all = '400';}
		
		If ($modules) {
			foreach ($modules as $mod) {
				if (empty($mod['news_on'])) {$mod['news_on'] = 0;}
				if (empty($mod['news_sort'])) {$mod['news_sort'] = 0;}
				if (empty($mod['news_date'])) {$mod['news_date'] = date("m/d/y");}
				
				if (!empty($mod['news_h1'])) {$decod_name = json_decode($mod['news_h1'], true); $name = strip_tags(html_entity_decode($decod_name[$this->session->data['language']], ENT_QUOTES, 'UTF-8'));} else {$name = false;}
				if (!empty($mod['news_text'])) {$decod_description = json_decode($mod['news_text'], true); $description = $decod_description[$this->session->data['language']];} else {$description = false;}
				
				$data['modules_news'][] = array(
					'news_h1' => $name,
					'news_image' => $this->model_tool_image->resize($mod['news_image'], $width, $height),
					'news_date' => $mod['news_date'],
					'news_sort' => $mod['news_sort'],
					'news_on' => $mod['news_on'],
					'news_id' => $mod['news_id'],
					'news_text_all' => strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8')),
					'news_text' => utf8_substr(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8')), 0, $limit_description_all) . '..'
				);
			}
		}
	
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('product/newsall', $data));
	}
}
?>