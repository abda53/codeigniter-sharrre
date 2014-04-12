<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set('display_errors', '1');

class Sharrre extends CI_Controller {

	function __construct()
    {
        parent::__construct();
    }
	
	public function index()
	{   
        $this->load->view('main/_partials/header', $data);
        $this->load->view('main/sharrre');
        $this->load->view('main/_partials/footer', $data);	
    }
	
	public function google()
	{
        $json = array('url'=>'','count'=>0);
        $json['url'] = base64_decode($this->uri->segment(3));
        $nice_url = base64_decode($this->uri->segment(3));
        $url = urlencode($nice_url);
        $type = $this->uri->segment(4);
        
        if(filter_var($nice_url, FILTER_VALIDATE_URL)){
            if($type == 'googlePlus'){
                $content = $this->parse("https://plusone.google.com/u/0/_/+1/fastbutton?url=".$url."&count=true");
                $dom = new DOMDocument;
                $dom->preserveWhiteSpace = false;
                @$dom->loadHTML($content);
                $domxpath = new DOMXPath($dom);
                $newDom = new DOMDocument;
                $newDom->formatOutput = true;
                
                $filtered = $domxpath->query("//div[@id='aggregateCount']");
                if (isset($filtered->item(0)->nodeValue)){
                    $json['count'] = str_replace('>', '', $filtered->item(0)->nodeValue);
                }
            }
            else if($type == 'stumbleupon'){
                $content = $this->parse("http://www.stumbleupon.com/services/1.01/badge.getinfo?url=$url");
                $result = json_decode($content);
                if (isset($result->result->views)){
                    $json['count'] = $result->result->views;
                }
            }
        }
        header('Content-Type: application/json');        
        echo str_replace('\\/','/',json_encode($json));
        exit;
    }
    
    
        
    public function parse($encUrl){
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_USERAGENT => 'sharrre', // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 5, // timeout on connect
            CURLOPT_TIMEOUT => 10, // timeout on response
            CURLOPT_MAXREDIRS => 3, // stop after 10 redirects
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,
        );
        $ch = curl_init();
        
        $options[CURLOPT_URL] = $encUrl;  
        curl_setopt_array($ch, $options);
        
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        
        curl_close($ch);
        
        if ($errmsg != '' || $err != '') {
            /*print_r($errmsg);
            print_r($errmsg);*/
        }
        return $content;
    }
}


/* End of file sharrre.php */
/* Location: ./application/controllers/sharrre.php */