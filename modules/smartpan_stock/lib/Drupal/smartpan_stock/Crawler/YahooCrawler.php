<?php

/**
 * Description of YahooIChartCrawler
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Crawler;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

class YahooCrawler extends YahooCronService
{
    var $stock_id;
    var $stock_symbol;
    
    public function __construct(ParameterBag $parameterBag)
    {
        $this->database = \Drupal::service('database');
        $this->isCron = false;
        $this->response = new Response();
        $this->parameterBag = $parameterBag;
        
        $this->stock_symbol = $this->parameterBag->get('stock.symbol', false);
        $this->stock_id = $this->paranmeterBag->get('stock.id', 0);
    }
    
    public function getFrequentdataUrl()
    {
        $url = $this->url();
        
        $urls = array();
        
        $tags = $this->tagMapper['Symbol'] . 
                $this->tagMapper['LastTradePriceOnly'] . 
                $this->tagMapper['LastTradeDate'] .
                $this->tagMapper['LastTradeTime'] .
                $this->tagMapper['Change'] .
                $this->tagMapper['Volume'] .
                $this->tagMapper['PreviousClose'];
        
        if(empty($this->stock_symbol)) {
            throw new \Exception('Stock Symbol missing');
        }
        $urls = array(
            'size' => array(
                ':frequent:0:time-' . REQUEST_TIME => array(
                    'url' => $url['size']['url'],
                    'params' => array(
                        'get_data' => array(
                            's' => $this->stock_symbol,
                            'f' => $tags
                        )
                    )
                )
            )
        );
        return $urls;
    }
    
    public function getDailydataUrl()
    {
        $url = $this->url();
        
        $urls = array();
        
        $addKey = '';
        
        $params = $this->parameterBag->get('params');
        
        if(empty($this->stock_symbol)) {
            throw new \Exception('Stock Symbol missing');
        }
        $urls = array(
            'history' => array(
                ':daily:0:time-' . REQUEST_TIME => array(
                    'url' => $url['history']['url'],
                    'params' => array(
                        'get_data' => array(
                            's' => $this->stock_symbol,
                            'f' => $params['endYear'],
                            'e' => $params['endDay'],
                            'd' => $params['endMonth'] - 1,
                            'c' => $params['startYear'],
                            'b' => $params['startDay'],
                            'a' => $params['startMonth'] -1
                        )
                    )
                )
            ),
            'dividend' => array(
                ':daily:0:time-' . REQUEST_TIME => array(
                    'url' => $url['dividend']['url'],
                    'params' => array(
                        'get_data' => array(
                            's' => $this->stock_symbol,
                            'f' => $params['endYear'],
                            'e' => $params['endDay'],
                            'd' => $params['endMonth'] - 1,
                            'c' => $params['startYear'],
                            'b' => $params['startDay'],
                            'a' => $params['startMonth'] - 1
                        )
                    )
                )
            )
        );
        return $urls;
    }
    
    public function fetchFrequentData()
    {
        $url = $this->getFrequentdataUrl();
        if(empty($url)) throw new \Exception('Url not initialised');
        $this->urlContainer += $url;
        return $this;
    }
    
    public function fetchDailyData()
    {
        $url = $this->getDailydataUrl();
        if(empty($url)) throw new \Exception('Url not initialised');
        $this->urlContainer += $url;
        return $this;
    }
    
    public function csvParser($content)
    {
        $lines = explode(PHP_EOL, $content);
        $result = array();
        foreach($lines as $line) {
            $result[] = str_getcsv($line);
        }
        return $result;
    }
    
    public function refineUrlContainer($urlContainer, $type, $frequent = NULL)
    {
        $element = array();
        $code = $urlContainer['code'];
        
        $this->response->setStatusCode($code);
        $successful = $this->response->isSuccessful();
        
        if(!$successful) return NULL;
        if(empty($urlContainer['content'])) return NULL;
        if(stripos($urlContainer['content_type'], 'json')) {
            $content = json_decode($urlContainer['content']);
            if(empty($content->query->results) || $content->query->count == 0) return NULL;
            if(!is_array($content->query->results->quote)) {
                $element['content'] = array($content->query->results->quote);
            } else {
                $element['content'] = $content->query->results->quote;
            }
        } else {
            $element['content'] = $this->csvParser($urlContainer['content']);
        }
        return $element['content'];
    }
}

?>