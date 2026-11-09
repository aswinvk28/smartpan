<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmartPan\Bundles\StockBundle\Crawler;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of YahooIChartCrawler
 *
 * @author aswinvk28
 */
class YahooYqlCrawler extends StockCrawler
{
    private $tagMapper = array(
        'Symbol'                    => 's',
        'LastTradePriceOnly'        => 'l1',
        'LastTradeDate'             => 'd1',
        'LastTradeTime'             => 't1',
        'Change'                    => 'c1',
        'Open'                      => 'o',
        'DaysHigh'                  => 'h',
        'DaysLow'                   => 'g',
        'Volume'                    => 'v',
        'PreviousClose'             => 'p',
        'Divident/Share'            => 'd'
    );
    
    private $queryMapper = array(
        'PreviousStartMonth'        => 'a',
        'StartDay'                  => 'b',
        'StartYear'                 => 'c',
        'PreviousEndMonth'          => 'd',
        'EndDay'                    => 'e',
        'EndYear'                   => 'f'
    );
    
    private $fieldMapper = array(
        'Daily'                     => 'd',
        'Monthly'                   => 'm'
    );
    
    private $parameterBag;
    
    private $urlContainer = array();
    
    /**
     * 
     * Constructs a \Drupal\aggregator\Controller\AggregatorController object.
     *
     * @param \Drupal\Core\Database\Connection $database
     *   The database connection.
     */
    public function __construct(Connection $database, ParameterBag $parameterBag) {
        parent::__construct($database);
        $this->isCron = $parameterBag->get('crawl.iscron', false);
        if($this->isCron && !isset($this->useBlockSize)) {
            $this->useBlockSize = $parameterBag->get('crawl.useblocksize', true);
            if(!isset($this->blockSize)) {
                $this->blockSize = $parameterBag->get('crawl.blocksize', 200);
            }
        }
        $this->parameterBag = $parameterBag;
    }
    
    public function execute()
    {
        if(empty($this->urlContainer)) throw new \Exception('Urls missing');
        $urlContainer = $newUrlContainer = array();
        foreach($this->urlContainer as $type => $url) {
            foreach($url as $index => $urlItem) {
                $urlContainer[$type . $index] = $urlItem;
                $newUrlContainer[$type] = array();
            }
        }
        $urlContainer = $this->requestMulti($urlContainer);
        foreach($urlContainer as $index => $url) {
            $string = substr($index, 0, strpos($index, ':'));
            if(array_key_exists($string, $this->urlContainer)) {
                $newUrlContainer[$string][str_replace($string, '', $index)] = $url;
            }
        }
        $this->urlContainer = array();
        return $newUrlContainer;
    }
    
    public function url($tags = '')
    {
        return array(
            'size' => array(
                'url' => "http://download.finance.yahoo.com/d/quotes.csv?e=.csv&h=0"
            ),
            'history' => array(
                'url' => "http://ichart.finance.yahoo.com/table.csv?g=d&ignore=.csv"
            ),
            'yql' => array(
                'url' => 'http://query.yahooapis.com/v1/public/yql?diagnostics=false&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys'
            ),
            'dividend' => array(
                'url' => 'http://ichart.finance.yahoo.com/table.csv?g=v&ignore=.csv'
            )
        );
    }
    
    public function urlBuilder($purpose = '', $query = array())
    {
        $url = array();
        switch ($purpose) {
            case 'frequent':
                $url = $this->url();
                break;
            
            case 'daily':
                $url = $this->url();
                break;
            
            case 'latest':
                $url = $this->url();
                
                break;
            
            default:
                break;
        }
        return $url;
    }
    
    public function getFrequentdataUrl()
    {
        $url = $this->urlBuilder('frequent');
        
        $urls = array();
        
        $tags = $this->tagMapper['Symbol'] . 
                $this->tagMapper['LastTradePriceOnly'] . 
                $this->tagMapper['LastTradeDate'] .
                $this->tagMapper['LastTradeTime'] .
                $this->tagMapper['Change'] .
                $this->tagMapper['Volume'] .
                $this->tagMapper['PreviousClose'];
        
        if(!$this->isCron) {
            $symbol = $this->parameterBag->get('stock.symbol');
            if(empty($symbol)) {
                throw new \Exception('Stock Symbol missing');
            }
            $urls = array(
                'size' => array(
                    ':frequent:0:time-' . REQUEST_TIME => array(
                        'url' => $url['size']['url'],
                        'params' => array(
                            'get_data' => array(
                                's' => $symbol,
                                'f' => $tags
                            )
                        )
                    )
                )
            );
            return $urls;
        }
        
        $symbols = $this->symbolSplitterforMultiCurl();
        
        if(empty($symbols)) throw new \Exception('Symbols missing');
        
        if($this->useBlockSize) {
            foreach($symbols['size'] as $index => $chunkArray) {
                foreach($chunkArray as $key => $chunk) {
                    $symbolArray = array();
                    foreach($chunk as $item) {
                        $symbolArray[$item->symbol] = $item->id;
                    }
                    $urls['size.cron.' . ($index + 1)][':frequent:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                        'url' => $url['size']['url'],
                        'params' => array(
                            'post_data' => array(
                                's' => implode('+', array_keys($symbolArray)),
                                'f' => $tags
                            )
                        )
                    );
                    unset($symbolArray);
                }
            }
        } else {
            foreach($symbols['size'] as $key => $chunk) {
                $symbolArray = array();
                foreach($chunk as $item) {
                    $symbolArray[$item->symbol] = $item->id;
                }
                $urls['size.cron'][':frequent:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                    'url' => $url['size']['url'],
                    'params' => array(
                        'get_data' => array(
                            's' => implode('+', array_keys($symbolArray)),
                            'f' => $tags
                        )
                    )
                );
                unset($symbolArray);
            }
        }
        
        return $urls;
    }
    
    public function getDailydataUrl()
    {
        $url = $this->urlBuilder('daily');
        
        $urls = array();
        
        $addKey = '';
        
        $params = $this->parameterBag->get('params');
        
        if(!$this->isCron) {
            $symbol = $this->parameterBag->get('stock.symbol');
            if(empty($symbol)) {
                throw new \Exception('Stock Symbol missing');
            }
            $urls = array(
                'history' => array(
                    ':daily:0:time-' . REQUEST_TIME => array(
                        'url' => $url['history']['url'],
                        'params' => array(
                            'get_data' => array(
                                's' => $symbol,
                                'f' => $params['endYear'],
                                'e' => $params['endDay'],
                                'd' => $params['endMonth'] - 1,
                                'c' => $params['startYear'],
                                'b' => $params['startDay'],
                                'a' => $params['startMonth'] - 1
                            )
                        )
                    )
                ),
                'dividend' => array(
                    ':daily:0:time' . REQUEST_TIME => array(
                        'url' => $url['dividend']['url'],
                        'params' => array(
                            'get_data' => array(
                                's' => $symbol,
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
        
        $symbols = $this->symbolSplitterforMultiCurl();
        
        if(empty($symbols)) throw new \Exception('Symbols missing');
        
        if($this->useBlockSize) {
            foreach($symbols['history'] as $index => $chunkArray) {
                foreach($chunkArray as $key => $chunk) {
                    $symbolArray = array();
                    foreach($chunk as $item) {
                        $symbolArray[] = $item->symbol;
                        $addKey = $item->symbol;
                        $urls['dividend.cron.'. ($index + 1)][':'.$addKey.':daily:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                            'url' => $url['dividend']['url'],
                            'params' => array(
                                'get_data' => array(
                                    's' => $addKey,
                                    'f' => $params['endYear'],
                                    'e' => $params['endDay'],
                                    'd' => $params['endMonth'] - 1,
                                    'c' => $params['startYear'],
                                    'b' => $params['startDay'],
                                    'a' => $params['startMonth'] - 1
                                )
                            )
                        );
                    }
                    if(count($symbolArray) === 1) $addKey = key($symbolArray);
                    $urls['history.cron.'. ($index + 1)][':'.$addKey.':daily:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                        'url' => $url['history']['url'],
                        'params' => array(
                            'get_data' => array(
                                's' => implode('+', $symbolArray),
                                'f' => $params['endYear'],
                                'e' => $params['endDay'],
                                'd' => $params['endMonth'] - 1,
                                'c' => $params['startYear'],
                                'b' => $params['startDay'],
                                'a' => $params['startMonth'] - 1
                            )
                        )
                    );
                    unset($symbolArray);
                }
            }
        } else {
            foreach($symbols['history'] as $key => $chunk) {
                $symbolArray = array();
                foreach($chunk as $item) {
                    $symbolArray[$item->symbol] = $item->id;
                }
                $urls['history.cron'][':daily:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                    'url' => $url['history']['url'],
                    'params' => array(
                        'get_data' => array(
                            's' => implode('+', array_keys($symbolArray)),
                            'f' => $params['endYear'],
                            'e' => $params['endDay'],
                            'd' => $params['endMonth'] - 1,
                            'c' => $params['startYear'],
                            'b' => $params['startDay'],
                            'a' => $params['startMonth'] - 1
                        )
                    )
                );
                unset($symbolArray);
            }
            foreach($symbols['dividend'] as $key => $chunk) {
                $symbolArray = array();
                foreach($chunk as $item) {
                    $symbolArray[$item->symbol] = $item->id;
                }
                $urls['dividend.cron'][':daily:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                    'url' => $url['dividend']['url'],
                    'params' => array(
                        'get_data' => array(
                            's' => implode('+', array_keys($symbolArray)),
                            'f' => $params['endYear'],
                            'e' => $params['endDay'],
                            'd' => $params['endMonth'] - 1,
                            'c' => $params['startYear'],
                            'b' => $params['startDay'],
                            'a' => $params['startMonth'] - 1
                        )
                    )
                );
                unset($symbolArray);
            }
        }
        return $urls;
    }
    
    public function getLatestdataUrl()
    {
        $tags = $this->tagMapper['Symbol'] . 
                $this->tagMapper['LastTradePriceOnly'] . 
                $this->tagMapper['LastTradeDate'] .
                $this->tagMapper['LastTradeTime'] .
                $this->tagMapper['Change'] .
                $this->tagMapper['Volume'] .
                $this->tagMapper['PreviousClose'] .
                $this->tagMapper['Open'] .
                $this->tagMapper['DaysHigh'] .
                $this->tagMapper['DaysLow'];
    }
    
    public function fetchFrequentData()
    {
        $url = $this->getFrequentdataUrl();
        if(empty($url)) throw new \Exception('Url not initialised');
        $this->urlContainer += $url;
    }
    
    public function fetchDailyData()
    {
        $url = $this->getDailydataUrl();
        if(empty($url)) throw new \Exception('Url not initialised');
        $this->urlContainer += $url;
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
        if(is_null($frequent)) {
            if(empty($urlContainer)) return NULL;
            $element['content'] = !empty($urlContainer['content']) ? $urlContainer['content'] : NULL;
        } elseif(empty($urlContainer[$type])) {
            drupal_set_message('URLs or fetched content Missing');
            return NULL;
        } else {
            $element = current($urlContainer[$type]);
        }
        if(empty($element['content'])) {  
            drupal_set_message('Fetched Content missing'); 
            return NULL;
        }
        $element['content'] = $this->csvParser($element['content']);
        $method = 'stock_enhanced_allowable_fetch_'.$type.'_data';
        $bool = call_user_func_array($method, $element['content']);
        if($bool) { 
            drupal_set_message('URLs or fetched content Missing');
            return NULL;
        }
        return $element['content'];
    }
}

?>