<?
	class Company {

		private $company_data = false;

		//+++++

		function __construct($company_id = null) {
			$this->db = DB::getInstance();
			$this->user = !empty($_SESSION['user']) ? $_SESSION['user'] : null;

			if (!empty($company_id) && is_numeric($company_id)){
				$sql = 'SELECT id, encoded_id, name, reg_key, created_on FROM company WHERE id = ?';
				$company = $this->db->query($sql, array($company_id));

				if (!empty($company))
					$this->company_data = $company[0];
				else
					return false;
			}
		}

		//+++++

		public function get_data() {
			return $this->company_data;	
		}


		//+++++
		public function add_new($name, $description = null, $phone = null) {
			if (empty($this->get_company_data_by_name($name)) && $this->user->role_id == 3){
				$sql = 'INSERT INTO company (name, description, reg_key, phone) VALUES (?, ?, ?, ?)';
				$company_id = $this->db->insert($sql, array($name, $description, $this->generate_reg_key(), $phone));
				$sql = 'UPDATE company SET encoded_id = ? WHERE id = ?';
				$this->db->execute($sql, array(Tools::encode_company_id($company_id), $company_id));
				return true;
			}
			else{
				return false;
			}
		}
		//+++++
		public function get_company_data_by_name($name){
			$sql = 'SELECT id FROM company WHERE name = ?';
			return $this->db->query($sql, array($name));
		}

		//+++++
		public function update($name, $description, $phone) {
			if($this->company_data && $this->user->role_id == 3 && !empty($name)){
				$sql = 'UPDATE company SET name = ?, description = ?, phone = ? WHERE id = ?';
				$this->db->execute($sql, array($name, $description, $phone, $this->company_data['id']));

				$this->company_data['name'] = $name;
				$this->company_data['description'] = $description;

				return $this->company_data;
			}
			else
				return false;
		}
		//+++++
		public function remove() {
			if($this->company_data && $this->user->role_id == 3){
				$sql = 'DELETE FROM company WHERE id = ?';
				$this->db->execute($sql, array($this->company_data['id']));
				return true;
			}
			else
				return false;
		}

		public function getAll(){
			if($this->user->role_id == 3){
				$sql = 'SELECT * FROM company';
				return $this->db->query($sql, array());
			}
			else
				return false;
		}

		public function generate_reg_key(){
			srand(time());
        	$random_key = rand(10000000, 99999999);
        	return base64_encode($random_key);
		}

		public function get_company_data_by_key($key) {
			$sql = 'SELECT id, encoded_id, name, reg_key, created_on FROM company WHERE reg_key = ?';
			$company = $this->db->query($sql, array($key));

			if (!empty($company)){
				$this->company_data = $company[0];
				return $company;
			}
			else
				return false;
		}

		public function add_user_to_company($user_id){
			if($this->company_data){
				$sql = 'INSERT INTO company_users (user_id,	company_id) VALUES (?,?)';
				$this->db->insert($sql, array($user_id, $this->company_data['id']));
				return true;
			}
			else
				return false;
		}
	}
?>