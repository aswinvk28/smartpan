<?php

/**
 * Description of YahooCronService
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Crawler;

use Drupal\Core\Database\Connection;
use Drupal\smartpan_cron\Manager\StockCronManager;

class YahooCronService extends StockCrawler
{
    protected $tagMapper = array(
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
    
    protected $queryMapper = array(
        'PreviousStartMonth'        => 'a',
        'StartDay'                  => 'b',
        'StartYear'                 => 'c',
        'PreviousEndMonth'          => 'd',
        'EndDay'                    => 'e',
        'EndYear'                   => 'f'
    );
    
    protected $fieldMapper = array(
        'Daily'                     => 'd',
        'Monthly'                   => 'm'
    );
    
    protected $urlContainer = array();
    
    protected $response;
    
    protected $parameterBag;
    
    var $manager;
    
    public function __construct(Connection $database, StockCronManager $manager)
    {
        parent::__construct($database);
        $this->isCron = true;
        $this->response = new Response();
        $this->manager = $manager;
        $parameterBag = $this->manager->getParameterBag();
        $this->useBlockSize = $parameterBag->get('crawl.useblocksize', true);
        $this->postBlockSize = $parameterBag->get('crawl.postblocksize', array('size' => 60, 'history' => 30));
        if(!isset($this->blockSize)) {
            $this->blockSize = $parameterBag->get('crawl.blocksize', 200);
        }
        $this->parameterBag = $parameterBag;
    }
    
    public function execute()
    {
        if(empty($this->urlContainer)) return;
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
    
    protected function url($tags = '')
    {
        return array(
            'size' => array(
                'url' => "http://download.finance.yahoo.com/d/quotes.csv?e=.csv&h=0"
            ),
            'history' => array(
                'url' => "http://ichart.finance.yahoo.com/table.csv?g=d&ignore=.csv"
            ),
            'yql' => array(
                'url' => 'http://query.yahooapis.com/v1/public/yql?diagnostics=false&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys'
            ),
            'dividend' => array(
                'url' => 'http://ichart.finance.yahoo.com/table.csv?g=v&ignore=.csv'
            )
        );
    }
    
    private function queryQuotesListDataYql($symbolArray, $paramArray)
    {
        array_walk($symbolArray, function(&$value) {
            $value = '"' . $value . '"';
        });
        $symbols = implode(', ', $symbolArray);
        $params = implode(', ', $paramArray);
        $query = "select $params from yahoo.finance.quoteslist where symbol in ($symbols)";
        return $query;
    }
    
    private function queryHistoricalDataYql($symbolArray, $paramArray)
    {
        array_walk($symbolArray, function(&$value) {
            $value = '"' . $value . '"';
        });
        $symbols = implode(', ', $symbolArray);
        $startDateString = $paramArray['startYear'] . '-' . $paramArray['startMonth'] . '-' . $paramArray['startDay'];
        $endDateString = $paramArray['endYear'] . '-' . $paramArray['endMonth'] . '-' . $paramArray['endDay'];
        $startDate = new \DateTime($startDateString);
        $endDate = new \DateTime($endDateString);
        if($startDate > $endDate) {
            $tempDate = $startDate;
            $startDate = $endDate;
            $endDate = $tempDate;
        }
        $startDateString = $startDate->format('Y-m-d');
        $endDateString = $endDate->format('Y-m-d');
        $query = 'select * from yahoo.finance.historicaldata where symbol in ('.$symbols.') and startDate="'.$startDateString.'" and endDate="'.$endDateString.'"';
        return $query;
    }
    
    private function getFrequentdataUrl()
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
        
        $symbols = $this->symbolSplitterforMultiCurlStockSize();
        
        if(empty($symbols)) throw new \Exception('Symbols missing');
        
        if($this->useBlockSize) {
            foreach($symbols['size'] as $index => $chunkArray) {
                foreach($chunkArray as $key => $chunk) {
                    $symbolArray = array();
                    foreach($chunk as $item) {
                        $symbolArray[] = $item->symbol;
                    }
                    $urls['size.cron.' . ($index + 1)][':frequent:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                        'url' => $url['size']['url'],
                        'params' => array(
                            'post_data' => array(
                                's' => implode('+', $symbolArray),
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
                    $symbolArray[] = $item->symbol;
                }
                $urls['size.cron'][':frequent:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                    'url' => $url['size']['url'],
                    'params' => array(
                        'post_data' => array(
                            's' => implode('+', $symbolArray),
                            'f' => $tags
                        )
                    )
                );
                unset($symbolArray);
            }
        }
        
        return $urls;
    }
    
    private function getDailydataUrl($toRemove)
    {
        $url = $this->url();
        
        $urls = array();
        
        $addKey = '';
        
        $params = $this->parameterBag->get('params');
        
        $symbols = $this->symbolSplitterforMultiCurlStockHistory($toRemove);
        
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
                    $urls['history.cron.'. ($index + 1)][':daily:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                        'url' => $url['yql']['url'],
                        'params' => array(
                            'post_data' => array(
                                'q' => $this->queryHistoricalDataYql($symbolArray, $params)
                            )
                        )
                    );
                    unset($symbolArray);
                }
            }
        } else {
            foreach($symbols['history'] as $index => $symbolArray) {
                foreach($symbolArray as $key => $item) {
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
                $urls['history.cron.'][':daily:' . ($key + 1) . ':time-' . REQUEST_TIME] = array(
                    'url' => $url['yql']['url'],
                    'params' => array(
                        'post_data' => array(
                            'q' => $this->queryHistoricalDataYql(array_keys($symbolArray), $params)
                        )
                    )
                );
            }
        }
        return $urls;
    }
    
    protected function getLatestdataUrl()
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
        return $this;
    }
    
    public function fetchDailyData($toRemove = array())
    {
        $url = $this->getDailydataUrl($toRemove);
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
