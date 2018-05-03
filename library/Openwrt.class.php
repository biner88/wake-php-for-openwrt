<?php
//Openwrt
//#opkg install etherwake
/**
 * filename : Openwrt.class.php
 * author : biner
 * modify_time : 2015-06-17
 */
class Openwrt{
    protected $url='';
    protected $cookie_file=null;
    public function __construct(){
        //$this->cookie_file ='';
    }
    /**
     * [setWol wake pc]
     * @param [type] $mac [description]
     */
    public function setWol($mac){
        $result = $this->_eaPost('/admin/network/wol',array('cbi.submit'=>'1','cbid.wol.1.iface'=>'','cbid.wol.1.mac'=>$mac));
        preg_match ('/Sendto worked/i', $result, $matches);
        if (!empty($matches) && $matches['0']=='Sendto worked') {
           return true;
        }else{
           return false;
        }
    }
    /**
     * [getOnline description]
     * @return [type] [description]
     */
    public function getOnline(){
        $result            = $this->_eaPost('?status=1');
        $json = json_decode($result,true);print_r($result);
        $mac = array();
        if ($json['wifinets']) {
            foreach ($json['wifinets'] as $key => $value) {
                if ($value['networks']) {
                    foreach ($value['networks'] as $key1 => $value1) {
                        if ($value1['assoclist']) {
                            foreach ($value1['assoclist'] as $key2 => $value2) {
                                $mac[]=$key2;
                            }
                        }
                    }
                }
            }
        }
        return $mac;
    }
    /**
     * [clear description]
     * @return [type] [description]
     */
    public function clear(){
        unlink($this->cookie_file);
        unset($_SESSION['route']);
    }
    /**
     * [login description]
     * @return [type] [description]
     */
    public function login($config){
        $this->url         = $config['url'];
        $this->username    = $config['username'];
        $this->password    = $config['password'];


        if (isset($_SESSION['route']['stok'])) {
        	$this->cookie_file = $_SESSION['route']['cookie_file'];
        	return $_SESSION['route']['stok'];
        }else{
			if(!$this->username || !$this->password){
                throw new Exception("Username and Password is Required!", 1);
			}
            //create cookie file
            $this->cookie_file = tempnam('./temp','cookie');
            $result = $this->_eaPost('',array('username'=>$this->username,'password'=>$this->password));
            preg_match ('/\/cgi-bin\/luci\/;stok=([0-9A-Za-z]{32})\//i', $result, $matches);
	        if(isset($matches[1])){
				$_SESSION['route']['cookie_file'] = $this->cookie_file;
				return $_SESSION['route']['stok'] = $matches[1];
	        }else{
	        	unlink($this->cookie_file);
	        	return false;
	        }
        }
    }
    /**
     * [_eaPost description]
     * @param  string $url    [description]
     * @param  array  $body   [description]
     * @param  string $method [description]
     * @return [type]         [description]
     */
    public function _eaPost($url='',$body=array(),$method='post'){
        $ch   = curl_init();
        if ($url && isset($_SESSION)) {
            $url = $this->url.'/cgi-bin/luci/;stok='.$_SESSION['route']['stok'].$url;
        }else{
            $url = $this->url.'/cgi-bin/luci';
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, '');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($ch, CURLOPT_USERAGENT, "remote wake/1.0.0");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body) );
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}


 ?>
