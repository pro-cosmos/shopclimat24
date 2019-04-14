<?php  
class ControllerProductNewspage extends Controller {
	public function index() {
		
		$this->load->language('extension/module/newstemplate');
		
		$this->load->model('extension/module/newstemplate');
		$modules = $this->model_extension_module_newstemplate->getNewstemplate();
		
		$this->load->model('tool/image');
		
		if (isset($this->request->get['news_id'])) {$news_id = $this->request->get['news_id'];} else {$news_id = false;}
		if ($this->config->get('newstemplate_image_width')) {$width = $this->config->get('newstemplate_image_width');} else {$width = '100';}
		if ($this->config->get('newstemplate_image_height')) {$height = $this->config->get('newstemplate_image_height');} else {$height = '100';}
		
		if (isset($modules)) {
			foreach ($modules as $mod) {
				if ($mod['news_id'] == $news_id) {
					if (!empty($mod['news_h1'])) {$decod_name = json_decode($mod['news_h1'], true); $data['news_name'] = strip_tags(html_entity_decode($decod_name[$this->session->data['language']], ENT_QUOTES, 'UTF-8'));} else {$data['news_name'] = false;}
					if (!empty($mod['news_text'])) {$decod_description = json_decode($mod['news_text'], true); $data['news_text'] = html_entity_decode($decod_description[$this->session->data['language']], ENT_QUOTES, 'UTF-8');} else {$data['news_text'] = false;}
					if (!empty($mod['news_date'])) {$data['news_date'] = $mod['news_date'];} else {$data['news_date'] = false;}
					if (!empty($mod['news_image'])) {$data['news_image'] = $this->model_tool_image->resize($mod['news_image'], 100, 100);} else {$data['news_image'] = false;}
				}
			}
		}
		
		
		
		$this->language->load('extension/module/langtemplates');
		
		if (empty($data['news_name'])) {$data['news_name'] = $this->config->get('config_title');}
		
		$this->document->setTitle($data['news_name']);
		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['heading_title'] = $this->config->get('config_title');
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('product/newspage', $data));
	}
}
?>