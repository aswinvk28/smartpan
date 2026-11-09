<?php

/**
 * Description of StockAjaxController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Controller\AjaxController;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\smartpan_stock\Entity\StockProfile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

class StockAjaxController extends AjaxController implements ContainerInjectionInterface
{
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag
     */
    private $parameterBag;
    
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('controller_resolver'),
            $container->get('ajax_response_renderer')
        );
    }
    
    public function getStock(Request $request)
    {
        $stock_symbol = strtoupper(trim($request->attributes->get('symbol', false)));
        $stock_id = $request->attributes->get('stock_profile', false);
        
        $stock_symbol_new = strip_tags($stock_symbol);
        
        $this->parameterBag = new ParameterBag(array(
            'stock.symbol' => $stock_symbol,
        ));
        
        if(!empty($stock_id)) {
            $stock_id_new = strip_tags($stock_id);
            $this->parameterBag->set('stock.id', (int) $stock_id);
        }
        
        $ajaxResponse = new AjaxResponse();
        
        if(empty($stock_symbol) || $stock_symbol_new !== $stock_symbol || !empty($stock_id) && $stock_id_new !== $stock_id) {
            drupal_set_message('Stock Symbol Invalid', 'error');
            $status_messages = array('#theme' => 'status_messages');
            $ajaxResponse->setStatusCode(400);
            return $ajaxResponse->addCommand(new ReplaceCommand(NULL, drupal_render($status_messages)));
        }
        
        $stockController = new StockController(\Drupal::getContainer(), $this->parameterBag);
        
        $stockProfile = $stockController->getStockProfile();
        
        if(empty($stockProfile)) {
            drupal_set_message('Stock Symbol Not Found', 'error');
            $status_messages = array('#theme' => 'status_messages');
            $ajaxResponse->setStatusCode(400);
            return $ajaxResponse->addCommand(new ReplaceCommand(NULL, drupal_render($status_messages)));
        }
        
        if(!($stockProfile instanceof StockProfile)) {
            drupal_set_message('Stock Symbol Not Instantiated', 'error');
            $status_messages = array('#theme' => 'status_messages');
            $ajaxResponse->setStatusCode(400);
            return $ajaxResponse->addCommand(new ReplaceCommand(NULL, drupal_render($status_messages)));
        }
        
        if(empty($stock_id)) {
            $this->parameterBag->set('stock.id', $stockProfile->id());
        }
        
        // to be noted
        $external = (bool) !empty($form_state['values']['previous']) ? $form_state['values']['previous'] : false;
        $timestamps = NULL;
        
//        if(!empty($form_state['values']['size_timestamp']) && !empty($form_state['values']['rank_timestamp']) && !empty($form_state['values']['investor_timestamp'])) {
//            $timestamps = array(
//                'size' => $form_state['values']['size_timestamp'] ? $form_state['values']['size_timestamp'] : NULL,
//                'return' => $form_state['values']['size_timestamp'] ? $form_state['values']['size_timestamp'] : NULL,
//                'rank' => $form_state['values']['rank_timestamp'] ? $form_state['values']['rank_timestamp'] : NULL,
//                'investor' => $form_state['values']['investor_timestamp'] ? $form_state['values']['investor_timestamp'] : NULL
//            );
//        }
        
        $data = \Drupal::getContainer()->get('memcache.stock_controller')->getDashboard($stockProfile, $stockController, $timestamps, $external);
        
        if($data instanceof Response) {
            return $data;
        }
        
        if(empty($data)) {
            drupal_set_message('Error in fetching Current Stock Price and Volume from Database', 'error');
            $status_messages = array('#theme' => 'status_messages');
            $ajaxResponse->setStatusCode(400);
            return $ajaxResponse->addCommand(new ReplaceCommand(NULL, drupal_render($status_messages)));
        } else {
            $flag = false;
            array_walk($data, function($value, $index) use(&$flag, $ajaxResponse) {
                if(empty($value)) {
                    $key = \smartpan_stock_data_type_mapper($index);
                    drupal_set_message("Error in fetching $key from Database", 'error');
                    $status_messages = array('#theme' => 'status_messages');
                    $ajaxResponse->addCommand(new AppendCommand(NULL, drupal_render($status_messages)));
                    $flag = true;
                }
            });
        }
        
        if($flag) {
            $ajaxResponse->setStatusCode(400);
            return $ajaxResponse;
        }
        
        $data['profile'] = $stockProfile;
//        $data['news'] = $rssController->getNewsFeed($stockProfile);
        
        $data = \smartpan_handle_dashboard_data($data);
        
        return $ajaxResponse->setData($data);
    }
}

?>
