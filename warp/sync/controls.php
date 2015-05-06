<?
	class User {
	
		public $encoded_user_id = null;
		public $ip = true;
		public $first_name;
		public $last_name;
		public $email;
		private static $_instance;

		public static function getInstance() {
			if (self::$_instance === null) {
				try {
					self::$_instance = new User();
				} catch (PDOException $e) {
					die('User class fail');
				}
			}
	 
			return self::$_instance;
		}
		
		function __construct() { 
			$this->ip = $_SERVER["REMOTE_ADDR"];
			$this->encoded_user_id = null;
			$this->first_name = null;
			$this->last_name = null;
			$this->email = null;
			$this->db = DB::getInstance();
			session_start();
		}
		
		public function auth_user($email, $password){
			if (!empty($email) && !empty($password)){
				if ($this->check_user_password($email, $password)){
					$data = $this->get_user_data_by_email($email);
					if(!empty($data)){
						$this->encoded_user_id = $data[0]['encoded_id'];
						$this->first_name = $data[0]['first_name'];
						$this->last_name = $data[0]['last_name'];
						$this->email = $data[0]['email'];
						$this->upd_session_array(true);
					}
					return true;
				}
				else return false;
			}
		}
		
		public function upd_session_array($direct = false){
			if ((isset($_SESSION["id"]) && !is_numeric($_SESSION["id"])) || $direct) {
				$session_this = clone $this;
				unset($session_this->db);
	            $_SESSION['user'] = $session_this;
	            $_SESSION['id'] = 1;
        	}
		}
		
		public function make_new_user($email, $password, $first_name = 'anonymous', $last_name = 'anonymous'){
			if (empty($this->get_user_data_by_email($email))){
				$sql = 'INSERT INTO users (encoded_id) VALUES ("new")';
				$user_id = $this->db->insert($sql, array());
				$sql = 'UPDATE users SET encoded_id = ? WHERE user_id = ?';
				$this->db->execute($sql, array(Tools::encode_user_id($user_id), $user_id));
				$sql = 'INSERT INTO user_info (user_id, email, first_name, last_name, password) VALUES (?,?,?,?,?)';
				$this->db->insert($sql, array($user_id, $email, $first_name, $last_name, $this->encrypt_password($password)));
			}
			else{
				var_dump('already reg');
			}
		}
		
		public function save_user_password($password){
			$sql = 'UPDATE user_info SET password = ? WHERE user_id = ?';
			return $this->db->execute($sql, array($this->encrypt_password($password),Tools::decode_user_id($this->encoded_user_id)));
		}
		
		public function check_user_password($email, $password){
			$sql = 'SELECT password FROM user_info WHERE email = ?';
			$data = $this->db->query($sql, array($email));
			if (empty($data)) return false;
			return crypt($password,$data[0]['password']) === $data[0]['password'];
		}
		
		public function encrypt_password($password){
			$salt = '$2a$10$'.substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(),mt_rand()))), 0, 22) . '$';
			return crypt($password, $salt);
		}
		
		public function get_user_data_by_encoded_id(){
			$sql = 'SELECT uf.user_id, uf.first_name, uf.last_name, uf.email, u.encoded_id FROM user_info uf, users as u WHERE u.encoded_id = ?';
			return $this->db->query($sql, array($this->encoded_user_id));
		}
		
		public function get_user_data_by_email($email){
			$sql = 'SELECT uf.user_id, uf.first_name, uf.last_name, uf.email, u.encoded_id FROM user_info uf, users as u WHERE uf.email = ? AND u.user_id = uf.user_id';
			return $this->db->query($sql, array($email));
		}

		public function session_destroy(){
			session_destroy();
			return true;
		}
	}
?>