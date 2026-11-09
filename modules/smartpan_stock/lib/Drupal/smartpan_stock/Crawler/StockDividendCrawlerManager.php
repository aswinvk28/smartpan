<?php

/**
 * Description of StockDividendCrawlerManager
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Crawler;

class StockDividendCrawlerManager
{
    private $crawler;
    
    public function __construct(StockCrawler $crawler)
    {
        $this->crawler = $crawler;
    }
    
    public function fetchCrawler($urlContainer)
    {
        $dividend = array();
        foreach($urlContainer['dividend'] as $urls) {
            if(!is_null($dividendElement = $this->crawler->refineUrlContainer($urls, 'dividend'))) {
                foreach($dividendElement as $values) {
                    $content = $this->dataRefine($values);
                    if(!empty($content)) {
                        $dividend[$content[0]] = $content[1];
                    }
                }
            }
        }
        return $dividend;
    }
    
    public function fetchCron($urlContainer)
    {
        $dividend = array();
        foreach($urlContainer['dividend.cron'] as $index => $url) {
            if(!is_null($dividendElement = $this->crawler->refineUrlContainer($url, 'dividend'))) {
                foreach($dividendElement as $values) {
                    $content = $this->dataRefine($values);
                    if(!empty($content)) {
                        $dividend[$content[0]] = $content[1];
                    }
                }
            }
        }
        return $dividend;
    }
    
    public function fetchCronBlock($urlContainer)
    {
        $dividend = array();
        $increment = 0;
        while(array_key_exists('dividend.cron.'.($increment + 1), $urlContainer)) {
            $key = 'dividend.cron.'.($increment + 1);
            foreach($urlContainer[$key] as $index => $url) {
                $symbolExtract = str_getcsv($index, ':');
                $symbol = $symbolExtract[1];
                if(!is_null($dividendElement = $this->crawler->refineUrlContainer($url, 'dividend'))) {
                    array_shift($dividendElement);
                    foreach($dividendElement as $values) {
                        $content = $this->dataRefine($values);
                        if(!empty($content)) {
                            $dividend[$symbol][$content[0]] = $content[1];
                        }
                    }
                }
            }
            $increment++;
        }
        return $dividend;
    }
    
    public function dataValuesAllowable($content)
    {
        return empty($content) || empty($content[0]) || empty($content[1]);
    }
    
    public function dataRefine($content)
    {
        if(!$this->dataValuesAllowable($content)) {
            return $content;
        }
        return NULL;
    }
}

?>
