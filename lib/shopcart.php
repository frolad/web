<?
	class ShopCart {

		private $shopcart = false;

		function __construct() {
			$this->db = DB::getInstance();
			$this->user = !empty($_SESSION) ? $_SESSION['user'] : null;

			if (is_numeric($this->user->user_id)){
				$sql = 'SELECT i.id, i.name, i.price, i.description, i.img, ic.name as category_name, u.short_name as unit_name, i.category_id, sc.value
						FROM shop_cart as sc, items as i, items_categories as ic, units as u
						WHERE i.id = sc.item_id AND u.id = i.unit_id AND ic.id = i.category_id AND sc.user_id = ?';
				$shop_cart = $this->db->query($sql, array($this->user->user_id));

				if (!empty($shop_cart))
					$this->shopcart = $shop_cart;
				else
					return false;
			}
		}

		public function add_new_product($item_id, $qty) {
			if(is_numeric($this->user->user_id) && is_numeric($item_id) && is_numeric($this->user->company_id) && is_numeric($qty)){
				$old_qty = $this->check_for_existence($item_id);
				if ($old_qty === 0)
					$sql = 'INSERT INTO shop_cart (value, user_id, item_id, company_id) VALUES ( ?, ?, ?, ?)';
				else
					$sql = 'UPDATE shop_cart SET value = ? WHERE user_id = ? AND item_id = ? AND company_id = ?';

				$this->db->insert($sql, array($qty + $old_qty, $this->user->user_id, $item_id, $this->user->company_id));
				return true;
			}
			else
				return false;
		}

		public function get_shop_cart() {
			return $this->shopcart;
		}

		public function check_for_existence($item_id){
			foreach ($this->shopcart as $item) {
				if ($item['id'] == $item_id)
					return $item['value'];
			}
			return 0;
		}

		public function get_cart_users($company_id, $department_id){
			$sql = 'SELECT 	sc.user_id,
							ui.first_name,
							ui.last_name,
							ui.email,
							ui.phone
					FROM shop_cart AS sc, user_info AS ui, company AS c, company_users AS cu 
					WHERE sc.company_id = ? AND sc.user_id = ui.user_id AND cu.user_id = ui.user_id AND cu.department_id = ?
					GROUP BY sc.user_id';
			return $this->db->query($sql, array($company_id, $department_id));
		}

		public function get_user_shop_cart($company_id, $user_id){
			
			$sql = 'SELECT 	sc.item_id,
							sc.value,
							i.name,
							i.description,
							i.price,
							i.img,
							ic.name as cat_name,
							u.short_name
					FROM shop_cart AS sc, items AS i, items_categories AS ic, units AS u, user_info AS ui
					WHERE sc.item_id = i.id AND i.category_id = ic.id AND i.unit_id = u.id AND ui.user_id = sc.user_id  AND sc.company_id = ? AND sc.user_id = ?';
			return $this->db->query($sql, array($company_id, $user_id));
		}

		public function get_total($company_id){
			$sql = 'SELECT	sc.item_id,
							i.name,
							i.description,
							i.price,
							i.img,
							ic.name as cat_name,
							u.short_name,
					SUM(sc.value) as value 
					FROM shop_cart AS sc
					LEFT JOIN items AS i ON i.id = sc.item_id
					LEFT JOIN items_categories AS ic ON i.category_id = ic.id
					LEFT JOIN units AS u ON i.unit_id = u.id
					WHERE sc.company_id = ?
					GROUP BY sc.item_id';
			return $this->db->query($sql, array($company_id));
		}

		public function toggle_sale_closed($company_id){
			$sql = 'UPDATE company
					SET sale_closed=IF(sale_closed=0, 1, 0)
					WHERE id = ?';
			$this->db->execute($sql, array($company_id));		

		}

		public function delete_item($item_id){
			$sql = 'DELETE FROM shop_cart WHERE company_id = ? AND user_id = ? AND item_id = ?';
			$this->db->execute($sql, array($this->user->company_id, $this->user->user_id, $item_id));
			return true;
		}

		public function edit_item($item_id, $qty){
			$sql = 'UPDATE shop_cart SET value = ? WHERE user_id = ? AND item_id = ? AND company_id = ?';
			$this->db->execute($sql, array($qty, $this->user->user_id, $item_id, $this->user->company_id));
			return true;
		}
	}
?>