<?php

/**
 * Description of StockFormAjaxController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\system\Controller\FormAjaxController;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class StockFormAjaxController extends FormAjaxController
{
    private $parameterBag;
    
    public function getSymbol(Request $request)
    {
        $ajaxResponse = new AjaxResponse();
        
        $stock_symbol = strtoupper(trim($request->request->get('symbol', false)));
        $stock_symbol_new = strip_tags($stock_symbol);
        
        if(empty($stock_symbol) || $stock_symbol_new !== $stock_symbol) {
            drupal_set_message("Stock Symbol '$stock_symbol' Invalid", 'error');
            $status_messages = array('#theme' => 'status_messages');
            $ajaxResponse->setStatusCode(400);
            return $ajaxResponse->addCommand(new ReplaceCommand(NULL, drupal_render($status_messages)));
        }
        
        $this->parameterBag = new ParameterBag(array(
            'stock.symbol' => $stock_symbol
        ));
        
        $stockController = new StockController(\Drupal::getContainer(), $this->parameterBag);
        
        $stockProfiles = $stockController->getStocks();
        
        if(empty($stockProfiles)) {
            drupal_set_message("Stock Symbols starting with \'$stock_symbol\' not found", 'error');
            $status_messages = array('#theme' => 'status_messages');
            $ajaxResponse->setStatusCode(400);
            return $ajaxResponse->addCommand(new ReplaceCommand(NULL, drupal_render($status_messages)));
        }
        
        return $ajaxResponse->setData($stockProfiles);
    }
}

?>
