<?
	/**
	* @package   cmangos-website
	* @version   1.0
	* @author    Victor Corbuleanu
	* @copyright 2023 Victor Corbuleanu
	* @link      https://github.com/vcorbuleanu/cmangos-website/
	* @license   https://github.com/vcorbuleanu/cmangos-website/blob/master/LICENCE.txt Attribution-NonCommercial-NoDerivatives 4.0 International  
	**/
	
    class news_controller extends controller {
        public function __construct() {
            parent::__construct();
            $this->load->model('news');
            $this->data['authenticated'] = $this->authenticated();
        }
        
        public function latest($parameters) {
			$this->data['title'] = 'Latest News';
			$this->data['banner'] = '/public/img/banners/default.webp';

			$this->data['latest_news'] = $this->model->get_latest_news();
			
			$this->load->view('header', $this->data);
			$this->load->view('news/latest', $this->data);
			$this->load->view('footer', $this->data);
		}

		public function archived($parameters) {
			$this->data['title'] = 'Archived News';
			$this->data['banner'] = '/public/img/banners/default.webp';

			$this->data['archived_news'] = $this->model->get_archived_news();

			$this->load->view('header', $this->data);
			$this->load->view('news/archived', $this->data);
			$this->load->view('footer', $this->data);
		}
    }
?>