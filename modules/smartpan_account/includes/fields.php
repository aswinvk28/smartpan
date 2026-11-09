<?php

function smartpan_load_multiple_taxonomy_options($vocabulary) {
    $entities = entity_load_multiple_by_properties('taxonomy_term', array(
        'vid' => $vocabulary
    ));

    if(empty($entities)) throw new \Exception('Environment not set up');

    $options = array();
    foreach($entities as $entityId => $entity) {
        $options[$entityId] = $entity->get('name')->value;
    }
    return $options;
}

function smartpan_load_multiple_taxonomy_names($vocabulary) {
    $entities = entity_load_multiple_by_properties('taxonomy_term', array(
        'vid' => $vocabulary
    ));

    if(empty($entities)) throw new \Exception('Environment not set up');

    $options = array();
    foreach($entities as $entity) {
        $options[$entity->get('name')->value] = $entity->get('name')->value;
    }
    return $options;
}

function smartpan_load_multiple_currency() {
    static $currencyOptions;
    if(!isset($currencyOptions)) {
        $resource_path = drupal_get_path('module', 'smartpan_account') . '/config/smartpan_account.currencies.yml';
        $currency = \Symfony\Component\Yaml\Yaml::parse($resource_path);
        $currencyOptions = $currency['smartpan_account.currencies'];
    }
    return $currencyOptions;
}

function smartpan_load_multiple_country() {
    static $countryOptions;
    if(!isset($countryOptions)) {
        $resource_path = drupal_get_path('module', 'smartpan_account') . '/config/smartpan_account.countries.csv';
        $content = file_get_contents($resource_path);
        $lines = explode(PHP_EOL, $content);
        $fields = array_shift($lines);
        $result = array();
        foreach($lines as $line) {
            $csv = str_getcsv($line, ',');
            $result[$csv[1]] = $csv[0];
        }
        $result['other'] = 'Other';
        $countryOptions = $result;
    }
    return $countryOptions;
}

/**
 * 
 * @param type $str_dt
 * @param type $str_dateformat
 * @param type $str_timezone
 * @return type
 */
function smartpan_isvalid_datetime_string($str_dt, $str_dateformat, $str_timezone = NULL) {
    if(empty($str_timezone)) {
        $date = \DateTime::createFromFormat($str_dateformat, $str_dt);
    } else {
        $date = \DateTime::createFromFormat($str_dateformat, $str_dt, new DateTimeZone($str_timezone));
    }
    return $date && $date->format($str_dateformat) == $str_dt;
}

function smartpan_csv_value($content, $key) {
    if(!is_array($content[0])) return FALSE;
    $index = array_search($key, $content[0]);
    
    return $content[1][$index];
}

function smartpan_handle_dashboard_data($data) {
    $modifiedData = array(
        'profile' => $data['profile'],
        'price' => new \SmartPan\Bundles\StockBundle\Model\StockPrice($data['size'], $data['history']),
        'volume' => new \SmartPan\Bundles\StockBundle\Model\StockVolume($data['size'], $data['history']),
        'industry' => $data['rank'][0],
        'market' => $data['rank'][1],
        'return' => $data['return']->result,
        'investor' => $data['investor'],
//        'news' => $data['news']
    );
    
    unset($data['rank']);
    unset($data['size']);
    unset($data['history']);
    
    $returnData = array();
    
    foreach($modifiedData as $index => $value) {
        $returnData[] = array($index => $value);
    }
    
    return $returnData;
}

function smartpan_stock_data_type_mapper($stockType) {
    $stockTypeMap = array(
        'investor' => 'Stock Investor',
        'profile' => 'Stock Profile',
        'return' => 'Stock Return',
        'news' => 'Stock News',
        'history' => 'Stock History',
        'size' => 'Stock Size',
        'rank' => 'Stock Rank'
    );
    return $stockTypeMap[$stockType];
}

?>
