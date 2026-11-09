<?php

/**
 * Description of Stock
 *
 * @author aswinvk28
 */

namespace SmartPan\Tests\TestBundle\Fixture;

use Drupal\Core\Controller\ControllerBase;
use Drupal\smartpan_stock\Crawler\YahooCrawler;

class Stock extends ControllerBase
{
    private $crawler;
    
    public function __construct()
    {
        $parameterBag = new ParameterBag(array());
        $this->crawler = new YahooCrawler($parameterBag);
    }
    
    public function createProfile()
    {
        $stockProfile = entity_create('stock_profile', array(
            'symbol' => 'GOOG',
            'industry' => 'IT',
            'name' => 'Google',
            'exchange' => 'NASDAQ',
            'currency' => 'USD'
        ));
        if($stockProfile->save()) {
            return $stockProfile;
        }
        return NULL;
    }
    
    public function populateSizeData()
    {
        $this->crawler->fetchFrequentData();
        
        $urlContainer = $this->crawler->execute();
        
        foreach($urlContainer['size'] as $urls) {
            if(!is_null($sizeElement = $this->crawler->refineUrlContainer($urls, 'size'))) {
                $sizeStorage = stock_enhanced_refine_fetch_size_data($sizeElement[0], $stock_id, $symbol, REQUEST_TIME);
            }
        }
        
        return $sizeStorage;
    }
    
    public function populateHistoryData()
    {
        $this->crawler->fetchDailyData();
        
        $urlContainer = $this->crawler->execute();
        
        foreach($urlContainer['size'] as $urls) {
            if(!is_null($historyElement = $this->crawler->refineUrlContainer($urls, 'history'))) {
                $historyStorage = stock_enhanced_refine_fetch_size_data($historyElement[0], $stock_id, $symbol, REQUEST_TIME);
            }
        }
    }
}
