<?php

/**
 * Description of StockCrawler
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Crawler;

use Drupal\Core\Database\Connection;

class Crawler
{
    /**
     *
     * @var \Drupal\Core\Database\Connection $database
     */
    protected $database;
    
    var $debug = false;
    // proxies array of xxx.xxx.xxx.xxx:xxx
    var $proxies = array ();
    var $max_proxies_count = 1000;

    // settings
    var $use_proxy = false;
    var $use_curl = true;

    // curl handle
    var $ch;
    
    var $handles = array();

    // stuff
    var $cur_proxy = 0;
    var $proxy_timeout = 15;
    var $fetch_timeout = 60;
    var $savepos_backuped = false;
    var $min_content_len = 500;
    var $do_increment_proxy = false;        

    // csv stuff
    var $csv_separator = "\t";
    var $csv_first_iteration = array();
    var $csv_quotes = true;
    var $csvHandle = array();
    var $csvFilePath;

    // log
    var $log_to_file = false;
    var $log_filename = 'log.log';

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }
    
    public function requestMulti($urls)
    {
        //create the multiple cURL handle
        $mh = curl_multi_init();

        foreach ($urls as $uidx => $data) {
            $url = $data['url'];
            if (isset($data['params'])) {
                $params = $data['params'];
            }
            else {
                $params = array();
            }
            
            $this->handles[$uidx] = curl_init();
            
            if(isset($params['get_data'])) {
                $arr = array();
                foreach ($params["get_data"] as $idx => $value) {
                    if (is_array($value)) {
                        foreach ($value as $idx2 => $value2) {
                            $arr[] = urlencode($idx).'='.urlencode($value2);	
                        }
                    }
                    else {
                        $arr[] = urlencode($idx).'='.urlencode($value);
                    }
                }
                $arr = array_unique($arr);
                $get_fields = join ('&',$arr);
                $url .= '&' . $get_fields;
                unset($arr);
            }

            curl_setopt($this->handles[$uidx], CURLOPT_URL, $url);
//            if (isset($params['file_name'])) {
//
//                $path_parts = pathinfo($params['file_name']);
//                mkdirs($path_parts['dirname']);
//
//                if (file_exists($params['file_name'])) {
//                    unlink($params['file_name']);
//                }
//
//                if (!$handle = fopen($params['file_name'], 'w')) {
//                    $this->_log(ERROR, "Could not open file (".$params['file_name'].")");
//                    $handles[$uidx] = '';
//                    continue;
//                }
//
//                $urls[$uidx]['__fhandle'] = $handle;
//
//                curl_setopt($handles[$uidx], CURLOPT_HEADER, 0);
//                curl_setopt($handles[$uidx], CURLOPT_RETURNTRANSFER, 0);		        
//                curl_setopt($handles[$uidx], CURLOPT_FILE, $handle);
//                curl_setopt($handles[$uidx], CURLOPT_FAILONERROR, 1);            
//
//            }
//            else {
            curl_setopt($this->handles[$uidx], CURLOPT_RETURNTRANSFER, 1);
//            }
            curl_setopt($this->handles[$uidx], CURLOPT_POST, 0);
            curl_setopt($this->handles[$uidx], CURLOPT_FOLLOWLOCATION, 1);
            
            if (isset($params['no_follow'])) {
                curl_setopt($this->handles[$uidx], CURLOPT_FOLLOWLOCATION, 0);
            }

            if (isset($params['ssl']) && $params['ssl']) {
                curl_setopt($this->handles[$uidx], CURLOPT_SSL_VERIFYPEER, FALSE);
                //curl_setopt($handles[$uidx], CURLOPT_SSL_VERIFYHOST, 2);
            }

//            if (!isset($params['file_name'])) {
                $header_addon = array();
                if (isset($params["header"]) && is_array($params["header"]) ) {
                    // add cookies
                    $header_addon = $params["header"];
                }

                curl_setopt($this->handles[$uidx], CURLOPT_HTTPHEADER,
               array_merge(array('User-Agent: Mozilla/5.0 (Windows NT 6.1; U; en; rv:1.8.1) Gecko/20061208 Firefox/2.0.0'
               ,'Accept: Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1, text/vnd.wap.wml, image/vnd.wap.wbmp; level=0')
               , $header_addon));

                if (isset($params["post_data"])) {
                    // POST request
                    if (isset($params["multiform"])) {
                        $post_fields = $params["post_data"];
                    }
                    else {
                        $arr = array();
                        foreach ($params["post_data"] as $idx => $value) {
                            if (is_array($value)) {
                                foreach ($value as $idx2 => $value2) {
                                    $arr[] = urlencode($idx).'='.urlencode($value2);	
                                }
                            }
                            else {
                                $arr[] = urlencode($idx).'='.urlencode($value);
                            }
                        }
                        $arr = array_unique($arr);
                        $post_fields = join ('&',$arr);
                    }

                    curl_setopt($this->handles[$uidx], CURLOPT_POST, 1);
                    curl_setopt($this->handles[$uidx], CURLOPT_POSTFIELDS, $post_fields);
                }

                if(isset($params["get_header"])) {
                    // include header in result
                    curl_setopt($this->handles[$uidx], CURLOPT_HEADER, 1);
                }
                else {
                    curl_setopt($this->handles[$uidx], CURLOPT_HEADER, 0);
                }
//            } // file


//            if (isset($params["referer"])) {
//                curl_setopt($handles[$uidx], CURLOPT_REFERER, $params["referer"]);
//            }
//
//            if (isset($params["cookie"])) {
//                // add cookies
//                curl_setopt($handles[$uidx], CURLOPT_COOKIE, $params["cookie"]);
//            }
//            elseif(isset($params["cookiefile"])) {
//
//                curl_setopt($handles[$uidx],CURLOPT_COOKIEJAR,$params["cookiefile"]); 
//                curl_setopt($handles[$uidx],CURLOPT_COOKIEFILE,$params["cookiefile"]);
//            }
//            elseif(!empty($this->cookiefile)) {
//
//                curl_setopt($handles[$uidx],CURLOPT_COOKIEJAR,$this->cookiefile); 
//                curl_setopt($handles[$uidx],CURLOPT_COOKIEFILE,$this->cookiefile);
//            }
//            else {
//
////                    curl_setopt($handles[$uidx], CURLOPT_COOKIE, '');
//            }

            curl_setopt($this->handles[$uidx], CURLOPT_TIMEOUT, $this->fetch_timeout);

            if ($this->use_proxy) {

                // set proxy timeout
                curl_setopt($this->handles[$uidx], CURLOPT_TIMEOUT, $this->proxy_timeout);                 

                $this->_log(MESSAGE, 'Proxy '.$this->cur_proxy.':'.$this->proxies[$this->cur_proxy]);

                // set proxy
                curl_setopt($this->handles[$uidx], CURLOPT_PROXY, $this->proxies[$this->cur_proxy]);

            }
            curl_multi_add_handle($mh,$this->handles[$uidx]);
        }

        $running=null;
        //execute the handles
        do {
            while (CURLM_CALL_MULTI_PERFORM === curl_multi_exec($mh, $running));

            if (!$running) break;

            while (($res = curl_multi_select($mh)) === 0) {echo $res;};
            if ($res === false) {
                $this->_log(ERROR, 'Curl multi select error!');
                break;
            }
        } while (true);

        foreach ($urls as $uidx => $data) {
            if (($error = curl_error($this->handles[$uidx])) == '') {

//                if (!isset($params['file_name'])) {
                    $urls[$uidx]['content']=curl_multi_getcontent($this->handles[$uidx]);
//                }
//                else {
//                    fclose($urls[$uidx]['__fhandle']);
                    $urls[$uidx]['downloaded'] = true;
//                    chmod($urls[$uidx]['params']['file_name'], 0666);
//                }
            }
            else {
               $urls[$uidx]['content'] = '';
               $urls[$uidx]['error'] = $error;
               $urls[$uidx]['downloaded'] = false;
               $this->_log(MESSAGE, 'Curl error on handle '.$data['url'].': '.$error);
            }
            
            $urls[$uidx]['code'] = curl_getinfo($this->handles[$uidx], CURLINFO_HTTP_CODE);
            $urls[$uidx]['content_type'] = curl_getinfo($this->handles[$uidx], CURLINFO_CONTENT_TYPE);
            $urls[$uidx]['time'] = curl_getinfo($this->handles[$uidx], CURLINFO_TOTAL_TIME);

            curl_multi_remove_handle($mh, $this->handles[$uidx]);
            curl_close($this->handles[$uidx]);
        }
        curl_multi_close($mh);

        return $urls;
    }
    
    public function request($url, $params = array())
    {
        if (empty($url)) {
            $this->_log(ERROR, "Bad url!");
            return;
        }

        $this->ch = curl_init();
        if ($this->use_curl) {

            curl_setopt($this->ch, CURLOPT_URL, $url);

            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->ch, CURLOPT_ENCODING, '');
            curl_setopt($this->ch, CURLOPT_POST, 0);

            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

            if (isset($params['no_follow'])) {
                curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 0);
            }

            #if (isset($params['ssl']) && $params['ssl']) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            //curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
            #}

            $header_addon = array();
            if (isset($params["header"]) && is_array($params["header"])) {
                // add cookies
                $header_addon = $params["header"];
            }

            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array_merge(array(
                'User-Agent: Opera/9.' . rand(1, 20) . ' (Windows; U; Windows NT ' . rand(1, 4) . '.1; ru; rv:' . rand(1, 5) . '.8.0.4)'
                #    'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 (.NET CLR 3.5.30729)'
                , 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'
                , 'Accept-Language: q=0.8,en-us;q=0.5,en;q=0.3'
                , 'Cookie2: $Version=1'
#                   ,'Referer: '.$url
                , 'Accept-Charset: iso-8859-1, utf-8, utf-16, *;q=0.1'
                , 'Expect:'), $header_addon));

            if (isset($params["post_data"])) {
                // POST request
                if (isset($params["multiform"])) {

                    $post_fields = $params["post_data"];
                } else {
                    if (is_array($params["post_data"])) {
                        // transform array to string
                        $arr = array();
                        foreach ($params["post_data"] as $idx => $value) {
                            if (is_array($value)) {
                                foreach ($value as $idx2 => $value2) {
                                    $arr[] = urlencode($idx) . '=' . urlencode($value2);
                                }
                            } else {
                                $arr[] = urlencode($idx) . '=' . urlencode($value);
                            }
                        }
                        $arr = array_unique($arr);
                        $post_fields = join('&', $arr);
                    } else {

                        $post_fields = $params["post_data"];
                    }
                }

                curl_setopt($this->ch, CURLOPT_POST, 1);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_fields);
            }


            if (isset($params["get_header"])) {
                // include header in result
                curl_setopt($this->ch, CURLOPT_HEADER, 1);
            } else {
                curl_setopt($this->ch, CURLOPT_HEADER, 0);
            }

            if (isset($params["referer"])) {

                curl_setopt($this->ch, CURLOPT_REFERER, $params["referer"]);
            }

            if (isset($params["cookie"])) {
                // add cookies
                curl_setopt($this->ch, CURLOPT_COOKIE, $params["cookie"]);
            } elseif (isset($params["cookiefile"])) {

                curl_setopt($this->ch, CURLOPT_COOKIEJAR, $params["cookiefile"]);
                curl_setopt($this->ch, CURLOPT_COOKIEFILE, $params["cookiefile"]);
            } elseif (!empty($this->cookiefile)) {

                curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookiefile);
                curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookiefile);
            } else {

//                    curl_setopt($this->ch, CURLOPT_COOKIE, '');
            }

            curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->fetch_timeout);

            if ($this->use_proxy) {

                // set proxy timeout
                curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->proxy_timeout);

                $contents = '';
                $requestsc = 0;
                while (strlen($contents) <= $this->min_content_len) {

                    $requestsc++;
                    if ($requestsc > 10) {
                        return '';
                    }

                    if ($this->cur_proxy >= count($this->proxies)) {
                        $this->cur_proxy = 0;
                    }

                    if (count($this->proxies) <= 0) {
                        die('No proxies! Exit!');
                    }

                    if ($this->debug) {
                        $this->_log(MESSAGE, 'Proxy ' . $this->cur_proxy . ':' . $this->proxies[$this->cur_proxy]);
                    }

                    // set proxy
                    curl_setopt($this->ch, CURLOPT_PROXY, $this->proxies[$this->cur_proxy]);
                    curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                    // load contents
                    $contents = curl_exec($this->ch);

                    if (strlen($contents) <= $this->min_content_len || curl_error($this->ch)) {

                        if ($this->debug) {
                            $this->_log(WARNING, 'Bad proxy removed ' . $this->proxies[$this->cur_proxy]);
                        }


                        array_splice($this->proxies, $this->cur_proxy, 1);
                        if (count($this->proxies) <= 10) {
                            // load proxies list
                            $this->loadProxies();
                        }
                    } else {
                        // increment proxies counter
                        if ($this->do_increment_proxy) {
                            $this->cur_proxy++;
                        }
                        return $contents;
                    }
                }
            } else {

                $content = curl_exec($this->ch);

                $err = curl_error($this->ch);

                curl_close($this->ch);

                if ($err) {
                    $this->_log(ERROR, "error: $err");
                    return false;
                }
                return $content;
            }
        } else {
            return file_get_contents($url);
        }
    }
    
    public function _log($type, $data, $file_only = false) {

        if ($this->log_to_file){

            // save log to file
            if ($handle = fopen($this->log_filename, 'a')) {

                fwrite($handle, date("F j, Y, g:i a").': '.$data."\n");
                fclose($handle);
            }
            if (!$file_only) {
                                    echo '<b>'.date("F j, Y, g:i a").'</b>: '.$data."<br>\n";
                    flush();
            }
        }
        else {

            echo '<b>'.date("F j, Y, g:i a").'</b>: '.$data."<br>\n";
            flush();
        }
    }
}

?>
