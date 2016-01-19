<?php
class Graph_fb {
	######## 定義instance variable
	private $user_access_token;
	private $page_token;
	private $app_id;
	private $app_secret;
	public function __construct(){
		
	}
	public function grap_fb_configs($config){
		$this->app_id = $config['app_id'];
		$this->app_secret = $config['app_secret'];
	}
	/**
	 * 取得page token
	 * */
	public function get_page_token(){
		return $this->page_token;
	}
	/**
	 * 設定page token
	 * */
	public function set_page_token($new_user_token = ''){
		$this->page_token = $new_user_token;
	}
	/** 
	 * 取得user access token
	 * */
	public function get_user_access_token(){
		return $this->user_access_token;
	}
	/** 
	 * 設定user access token 
	 * */
	public function set_user_access_token($new_user_token = ''){
		$this->user_access_token = $new_user_token;
	}
	/** 
	 * 取得粉絲團相關資訊
	 * @param identifier : 粉絲團英文名字
	 * @return page_info : 粉絲團資訊
	 * */
    public function get_page_info($identifier=''){
    	try{
    		$token = (!empty($this->page_token))?$this->page_token:$this->user_access_token;
    		$url = 'https://graph.facebook.com/'.$identifier.'?access_token='.$token;
    		//echo $url;
    		@$page_info = file_get_contents($url);
    		
    		if(empty($page_info)){
    			echo "Cannot fetch Page Information : <br/>\r\n";
    			print_r($page_info);
    			return;
    		}
    		
    		return $page_info;
    		
    	}catch(Exception $e){
    		return "error:".$e;
    	}
    }
	/** 
	 * (使用前請先設定page_name)取得粉絲頁ID
	 * @param identifier : 粉絲團英文名字
	 * @return id : 粉絲團在FB的ID
	 * */
	public function get_facebook_page_id($identifier=''){
		try{
			$page_name = (!empty($identifier))?$identifier:$this->page_name;
			$page = json_decode(self::get_page_info($page_name));
			
			if(empty($page->id)){
				return array("message"=>"Cannot fetch Page Information : <br/>\r\n","response"=>$page);
			}
			
			return $page->id;
			
		}catch (Exception $e){
			return "error:".$e;
		}
	}
	/* 
	 * (使用前先設定page token)
	 * 取得粉絲頁所有相簿資訊 (page_id = 粉絲頁id)
	 * */
    public function get_page_album_by_graph($page_id){
    	try{
    		$token = (!empty($this->page_token))?$this->page_token:$this->user_access_token;
    		$url='https://graph.facebook.com/'.$page_id.'/albums?access_token='.$token;
    		$page_info = file_get_contents($url);
    		
    		if(empty($page_info)){
    			echo "Cannot fetch Page Information  : <br/>\r\n";
    			print_r($page_info);
    			return;
    		}
    		
    		return json_decode($page_info);
    		
    	}catch (Exception $e){
    		return "error:".$e;
    	}
    }
	/** 
	 * (使用前先設定page token)取得粉絲頁單一相簿內的所有照片 (fields = 要查詢的欄位,用逗號隔開)
	 * @param album_id : 相簿ID
	 * @param fields : 要查詢的欄位
	 * @param limit : 單次查詢筆數
	 * @return photo_info : 相簿內所有相片資訊
	 * */
    public function get_photos_in_album($album_id,$fields='',$limit=250){
    	try{
    		$token = (!empty($this->page_token))?$this->page_token:$this->user_access_token;
			if($fields==''){
				$fields = 'id,images,link,name,from,picture,height,source,album';
			}
    		$url='https://graph.facebook.com/'.$album_id.'/photos?fields='.$fields.'&limit='.$limit.'&access_token='.$token;
    		$photo_info = file_get_contents($url);
    		
    		if(empty($photo_info)){
    			echo "Cannot fetch Photos Information in this album : <br/>\r\n";
    			print_r($photo_info);
    			return;
    		}
    		
    		return json_decode($photo_info);
    		
    	}catch (Exception $e){
    		return "error:".$e;
    	}
    }
	/** 
	 * 根據粉絲團自己所上傳的照片相關資訊
	 * @param page_id : 粉絲團ID
	 * */
    public function get_photo_uploaded_by_graph($page_id){
    	try{
    		$url='https://graph.facebook.com/'.$page_id.'/photos/uploaded';
    		$page_info = file_get_contents($url);
    		return json_decode($page_info);
    	}catch (Exception $e){
    		return "error:".$e;
    	}
    }
    /**
     * 取得此粉絲團/使用者的所有相簿
     * @param id : 粉絲團或使用者ID
     */
    public function get_all_albums($id){
    	$token = (!empty($this->page_token))?$this->page_token:$this->user_access_token;
    	try{
    		$url = 'https://graph.facebook.com/'.$id.'/albums?access_token='.$token;
    		@$page_albums = file_get_contents($url);
    		
    		if(empty($page_albums)){
    			return "Cannot get albums of this page<br/>\r\n";
    		}
    		
    		return json_decode($page_albums);
    	}catch (Exception $e){
    		return "error:".$e;
    	}
    }
    
    
    public function get_obj($id){
    	$token = (!empty($this->page_token))?$this->page_token:$this->user_access_token;
    	try{
    		$url = 'https://graph.facebook.com/'.$id.'?access_token='.$token;
    		$page_albums = file_get_contents($url);
    	
    		if(empty($page_albums)){
    			return "Cannot get info of this id<br/>\r\n";
    		}
    	
    		return json_decode($page_albums);
    	}catch (Exception $e){
    		return "error:".$e;
    	}
    }
    
    /**
     * 給入粉絲團網址，轉成粉絲團ID出來
     * @param array $url_array 網址陣列
     * @return array $id_array FB_ID的陣列
     */
    public function get_fb_page_ids_by_urls($url_array){
    	if(!is_array($url_array)){
    		return "Please use an array as parameter.<br/>\r\n";
    	}
    	
    	$identifiers = array();
    	$ids = array();
    	
    	$pattern = '/^https\:\/\/www\.facebook\.com\/([^\/\?]+).*$/';
    	foreach($url_array as $url){
    		preg_match($pattern,$url,$matches);
    		
    		if(empty($matches)){
    			continue;
    		}
    		
    		$identifiers[] = $matches[1];
    	}
    	
    	if(empty($identifiers)){
    		return array('message'=>"Cannot get the identifier of these urls<br/>\r\n");
    	}
		
    	foreach($identifiers as $identifier){
    		$ids[] = self::get_facebook_page_id($identifier);
    	}

    	return $ids;
    }
    /**
     * 給入粉絲團網址，轉成粉絲團ID出來
     * @param array $url 單一網址
     * @return string $id FB_ID
     */
    public function get_fb_page_ids_by_url($url){
    	if(empty($url)){
    		return "Please input an url.<br/>\r\n";
    	}

    	 
    	$pattern = '/^https\:\/\/www\.facebook\.com\/([^\/\?]+).*$/';

    	preg_match($pattern,$url,$matches);
    	
    	if(empty($matches[1])){
    		return array('message'=>"Cannot get the identifier of these urls<br/>\r\n");
    	}
    	
    	$identifier = $matches[1];
    	
    	$id = self::get_facebook_page_id($identifier);
    
    	return $id;
    }
    
    /**
     * 抓取粉絲頁貼文
     * @param unknown 物件ID (粉絲團或使用者)
     * @param number $limit 一次抓幾筆
     * @param string $fields 所要查詢的欄位
     * @param datetime $since 從哪時候開始 (2016-01-13)
     * @return string|mixed 失敗訊息或是FB貼文資料
     */
    public function get_posts($id,$limit=1000,$fields='message,created_time,id',$from_date="",$to_date=""){
    	
    	$token = (!empty($this->page_token))?$this->page_token:$this->user_access_token;
    	
    	try{
    		
    		$url = 'https://graph.facebook.com/'.$id.'/posts?fields='.$fields.'&access_token='.$token.'&limit='.$limit;
    		if(!empty($from_date)){
    			$url .= '&since='.$from_date;
    		}
    		if(!empty($to_date)){
    			$url .= '&until='.$to_date;
    		}
    		
    		$page_albums = file_get_contents($url);
    		 
    		if(empty($page_albums)){
    			return "Cannot get posts of this id\r\n";
    		}
    		 
    		return json_decode($page_albums);
    	}catch (Exception $e){
    		return "error:".$e;
    	}
    }
    
	/* 
	 * 抓取此FB ID的大頭照
	 * */
	public function get_avatar($fb_id,$type="large"){ 
	    switch ($type){
	        case "large":
            case "small":
	            return file_get_contents("https://graph.facebook.com/".$fb_id."/picture?type=".$type);
	            break;
	        default:
	            return file_get_contents("https://graph.facebook.com/".$fb_id."/picture?type=large");
	            break;
	    }
	}
	/*
	 * 取得公開相簿內的照片,使用app_token
	 * */
	public function get_photoes_in_public_album($limit=250,$album_id,$fields='',$accessToken=''){
		try{
			if($fields==''){
				$fields = 'id,images,link,name,from,picture,height,source,album';
			}
			if($accessToken === ''){
				$accessToken = $this->page_token;
			}
			$url='https://graph.facebook.com/'.$album_id.'/photos?fields='.$fields.'&limit='.$limit.'&access_token='.$accessToken;
			$photo_info = self::_get_to_fb($url);
			
			return $photo_info['data'];
			//$photo_info=file_get_contents($url);
			//return json_decode($photo_info);
	
		}catch (Exception $e){
			return "error:".$e;
		}
	}
	/*
	 * 取得帳戶中的資訊與Access token(用來管理粉絲團)
	 * */
	public function get_accounts_info($fb_id){
		$url = 'https://graph.facebook.com/'.$fb_id.'/accounts?access_token='.$this->user_access_token;
		$account_info = file_get_contents($url);
		return json_decode($account_info);
	}
	/*
	 * 取得使用者的資訊
	 * */
	public function get_me(){
		$url = 'https://graph.facebook.com/me?access_token='.$this->user_access_token;
		$me = file_get_contents($url);
		return json_decode($me);
	}
	/*
	 * 取得長時間存活的token
	 * */
	public function get_long_lived_token($short_lived_token){
		$url = 'https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&'.
				'client_id='.$this->app_id.'&client_secret='.$this->app_secret.'&fb_exchange_token='.$short_lived_token;
		$long_lived_token = file_get_contents($url);
		return $long_lived_token;
	}
	
	/**
	 * 使用FB搜尋的API
	 * @param string $str 要搜尋的字串
	 * @param string $type 要搜尋的類型
	 * @param unknown $limit 一次幾筆
	 * @return string|mixed
	 */
	public function get_search($str='',$type="place",$limit){
		if($str === ''){
			return "Please input a non-empty string";
		}
		$token = (!empty($this->page_token))?$this->page_token:$this->user_access_token;
		$url = 'https://graph.facebook.com/v2.5/search?q='.$str.'&type='.$type.'&limit='.$limit.'&access_token='.$token;
		$search_result = file_get_contents($url);
		return json_decode($search_result);
	}
	
	/**
	 * 取得自己的打卡位置
	 * @return void|mixed
	 */
	public function get_me_tagged_places($limit=1000){
		if(empty($this->user_access_token)){
			echo "Please set user access token first.";
			return;
		}
		$url = 'https://graph.facebook.com/v2.5/me/tagged_places?access_token='.$this->user_access_token.'&limit='.$limit;
		$result = file_get_contents($url);
		return json_decode($url);
	}
	
	/*
	 * 使用cURL post抓取資料
	 * */
	private function _get_to_fb($api_url){
	
		$curl = curl_init($api_url);
		curl_setopt($curl, CURLOPT_HEADER,true);


		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$json_response = curl_exec($curl);
		// get header size
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		// http header
		$header = substr($json_response, 0, $header_size);
		// get response data
		$body = substr($json_response, $header_size);
		curl_close($curl);
		$result['data'] = json_decode($body);
		$result['header'] = self::_httpHeader2Array($header);
		return $result;
	}
	/*
	 * cURL - Http DELETE
	 * */
	public function delete_permission_to_fb($api_url){
		$curl = curl_init($api_url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($curl, CURLOPT_HEADER,true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json',"OAuth-Token: $token"));
		$json_response = curl_exec($curl);
		// get header size
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		// http header
		$header = substr($json_response, 0, $header_size);
		// get response data
		$body = substr($json_response, $header_size);
		curl_close($curl);
		$result['data'] = json_decode($body);
		$result['header'] = self::_httpHeader2Array($header);
		return $result;
	}
	/*
	 * 將http header轉成陣列
	* */
	private function _httpHeader2Array($headers){
		$return_header_array = array();
		foreach(explode("\r\n",$headers) as $i => $header_item){
			if($i === 0){
				$return_header_array['http_code'] = $header_item;
			}else{
				$temp = explode(': ',$header_item);
				if(count($temp)==2){
					list($key,$value) = $temp;
					if($key=='Set-Cookie'){
						$return_header_array[$key][] = $value;
					}else{
						$return_header_array[$key] = $value;
					}
				}
			}
		}
		return $return_header_array;
	}
}
?>