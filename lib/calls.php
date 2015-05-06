<?
	class Calls extends view {

		function __construct() { 
			$this->ip = $_SERVER["REMOTE_ADDR"];
			$this->encoded_user_id = null;
			$this->first_name = null;
			$this->last_name = null;
			$this->email = null;
			$this->db = DB::getInstance();
			$this->user = User::getInstance();
			$this->company = Company::getInstance();
		}
		
		public function my_first_api(){
			$this->return_json($_SESSION);
		}

		public function auth(){
			if (!empty($_REQUEST['email']) && !empty($_REQUEST['password'])){
				if ($this->user->auth_user($_REQUEST['email'], $_REQUEST['password']))
					$this->return_json($_SESSION);
				else
					$this->return_error(1);
			}
			else{
				$this->return_error(2);
			}
		}

		public function check_auth(){
			if (!empty($_REQUEST['email']) && !empty($_REQUEST['password'])){
				if ($this->user->check_user_password($_REQUEST['email'], $_REQUEST['password']))
					$this->return_json($_SESSION);
				else
					$this->return_error(1);
			}
			else{
				$this->return_error(2);
			}
		}

		public function register(){
			if(!empty($_REQUEST['email']) && !empty($_REQUEST['password']) && !empty($_REQUEST['first_name']) && !empty($_REQUEST['last_name'])){
				$this->user->make_new_user($_REQUEST['email'], $_REQUEST['password'], $_REQUEST['first_name'], $_REQUEST['last_name']);
			}
		}

		public function get_user_data() {
			$this->return_json($_SESSION);
		}

		public function logout(){
			$this->user->session_destroy();
			$this->return_json(array('req' => 'OK'));
		}
		// Beginning of Company calls
		public function add_company(){
			if(!empty($_REQUEST['name'])&& !empty($_REQUEST['description']) && !empty($_REQUEST['reg_key'])){
				$this->company->add_new($_REQUEST['name'], $_REQUEST['description'], $_REQUEST['reg_key']);
				$this->return_json($_REQUEST);
			}
			else{
				$this->return_error(1);
			}
		}

		public function update_company(){
			if(!empty($_REQUEST['name'])&& !empty($_REQUEST['description']) && !empty($_REQUEST['reg_key'])){
				$this->company->update($_REQUEST['name'], $_REQUEST['description'], $_REQUEST['reg_key']);
				$this->return_json($_REQUEST);
			}
			else{
				$this->return_error(1);
			}
		}
	}
?>