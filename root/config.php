<?

class SiteData {
    protected static $_instance;

    private function __construct() {        
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;   
        }
 
        return self::$_instance;
    }
  
    private function __clone() {
    }

    private function __wakeup() {
    }   
	
	
	public static $warp_way = '../warp/';
	public static $warp_sync = '../warp/sync/';
	public static $warp_async = '../warp/async/';
	       
	public static $styles_way = '../styles/';
	public static $styles_js = '../styles/js/';
	public static $styles_css = '../styles/css/';
	public static $styles_img = '../styles/img/';
	public static $upload_dir = '../img';
	
	public static $tmpls = '../tmpls/';
	public static $err_tmpls = '../tmpls/error/';
	public static $mod_tmpls = '../tmpls/modules/';
	public static $lib = '../lib/';
           
	public static $site_name = 'MyHope';
	public static $host_role = 'dev';
	public static $site_url = 'myhope/';
	
	public static $db_ip = 'localhost';
	public static $db_name = 'myhope';
	public static $db_user = 'myhope';
	
	public static function get_db_name() {
		if ($_SERVER["SERVER_ADDR"] == '166.62.28.80')
			return 'myhope';
		else
			return 'root';
	}

	public static function get_db_pw() { 
		$username = getenv("username");
		if ($username == 'HIRULEZ-PC$')
			return '6300';
		else
			return '378378378';
	}
}

SiteData::getInstance();

?>