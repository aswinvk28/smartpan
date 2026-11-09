<?php

/**
 * Description of StockController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Controller;

use Drupal\Core\Controller\ControllerBase;
use SmartPan\Bundles\StockBundle\Model\StockReturn;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class StockController extends ControllerBase
{
    private $parameterBag;
    
    var $cacheMethodsConfig = array(
        'size' => 'getStockCurrentSize',
        'return' => 'getStockReturn',
        'history' => 'getStockRecentDailyHistory',
        'rank' => 'getStockRecentRank',
        'investor' =>'getStockInvestor'
    );
    
    public function __construct(ParameterBag $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }
    
    public function getParameterBag()
    {
        return $this->parameterBag;
    }
    
    public function getStocks()
    {
        $stock_symbol = $this->parameterBag->get('stock.symbol', false);
        $stockProfiles = $this->entityManager()->getStorageController('stock_profile')->loadStocksLike(array(
            'symbol' => $stock_symbol
        ));
        return $stockProfiles;
    }
    
    public function getStockProfile()
    {
        $values = array();
        $values['symbol'] = $this->parameterBag->get('stock.symbol', false);
        if(empty($values['symbol'])) return FALSE;
        $stock_id = $this->parameterBag->get('stock.id', false);
        if(!empty($stock_id)) {
            $stockProfile = $this->entityManager()->getStorageController('stock_profile')->load($stock_id);
        } else {
            $stockProfile = $this->entityManager()->getStorageController('stock_profile')->loadByProperties($values);
            $stockProfile = current($stockProfile);
        }
        return $stockProfile;
    }
    
    public function getStockCurrentSize($timestamp, $external = false)
    {
        $stock_id = $this->parameterBag->get('stock.id');
        $stockSizeStorageController = $this->entitymanager()->getStorageController('stock_size');
        $stockSizeStorage = $stockSizeStorageController->loadRecent($stock_id, $timestamp, $external);
        $this->parameterBag->set('stock.size', $stockSizeStorage);
        return $stockSizeStorage;
    }
    
    public function getStockReturn($timestamp, $external = true)
    {
        $stockSize = $this->parameterBag->get('stock.size');
        
        if(empty($stockSize)) {
            $stockSize = new StockSize(array(), 'stock_size');
        } else {
            if(empty($timestamp)) {
                $timestamp = $stockSize->getTimestamp();
            }
        }
        
        $stockHistory = $this->entityManager()->getStorageController('stock_history');
        if(empty($timestamp)) {
            $timestamp = $stockHistory->getTimestamp($stock_id);
        }
        
        return new StockReturn($stockSize, $stockHistory->loadStockReturn($stockSize, $timestamp, $external));
    }
    
    public function getStockRecentDailyHistory($timestamp, $external = false)
    {
        $stock_id = $this->parameterBag->get('stock.id');
        
        $stockHistory = $this->entityManager()->getStorageController('stock_history');
        if(empty($timestamp)) {
            $timestamp = $stockHistory->getTimestamp($stock_id);
        }
        
        return $stockHistory->loadRecent($stock_id, $timestamp, $external);
    }
    
    /**
     * 
     * @return \SmartPan\Bundles\StockBundle\Model\StockRank
     */
    public function getStockRecentRank($timestamp, $external = false)
    {
        $stock_id = $this->parameterBag->get('stock.id');
        $industryTimestamp = $marketTimestamp = NULL;
        $stockIndustryRank = $this->entityManager()->getStorageController('industry_rank');
        $stockMarketRank = $this->entityManager()->getStorageController('market_rank');
        
        if(empty($industryTimestamp)) {
            $industryTimestamp = $stockIndustryRank->getTimestamp($stock_id);
        }
        if(empty($marketTimestamp)) {
            $marketTimestamp = $stockMarketRank->getTimestamp($stock_id);
        }
        
        $industryRank = $stockIndustryRank->loadRecentRank($stock_id, $industryTimestamp, $external);
        $marketRank = $stockMarketRank->loadRecentRank($stock_id, $marketTimestamp, $external);
        
        $industryRank = !empty($industryRank) ? $industryRank : 
            new \Drupal\stock_enhanced\Entity\IndustryRank(array(), 'industry_rank');
        $marketRank = !empty($marketRank) ? $marketRank : 
            new \Drupal\stock_enhanced\Entity\MarketRank(array(), 'market_rank');
        
        return array($industryRank, $marketRank);
    }
    
    public function getStockInvestor($timestamp, $external = false)
    {
        $stock_id = $this->parameterBag->get('stock.id');
        
        $stockInvestorStorage = $this->entityManager()->getStorageController('stock_investor');
        
        if(empty($timestamp)) {
            $timestamp = $stockInvestorStorage->getTimestamp($stock_id);
        }
        
        $stockInvestor = $stockInvestorStorage->loadRecent($stock_id, $timestamp, $external);
        
        return $stockInvestor;
    }
}

?>
