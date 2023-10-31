<?
    class _404_controller extends controller {
        public function __construct() {
            parent::__construct();
            $this->load->model('_404');
            $this->data['authenticated'] = $this->authenticated();
        }

        public function index($parameters) {
			$this->data['title'] = 'Oops...';
			$this->data['banner'] = '/public/img/banners/default.webp';

            $this->load->view('header', $this->data);
            $this->load->view('_404', $this->data);
            $this->load->view('footer', $this->data);
        }
    }
?>