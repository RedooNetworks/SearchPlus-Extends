<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 18.06.15 16:21
 * You must not use this file without permission.
 */
namespace SWSearchPlus\Plugin;

class ProductsConditionOperator extends \SWSearchPlus\ConditionOperator
{
    public function getOperators($moduleName) {
        $operators = array();
        //var_dump($moduleName);exit();
        if($moduleName === 'Products') {
            $operators['no_asset'] = array(
                    'config' => array(

                    ),
                    'label' => 'have related assets',
                    'fieldtypes' => array('crmid'),
            );
        }

        if($moduleName === 'Accounts') {
            $operators['buy_products'] = array(
                'config' => array(
                    'products' => array(
                        'type' => 'recordsearch',
                        'modules' => array('Products', 'Services'),
                    )
                ),
                'label' => 'had buy Products/Services',
                'fieldtypes' => array('crmid'),
            );
        }

        return $operators;
    }

    public function generateSQLCondition($key, $columnName, $config, $not) {
        $adb = \PearDatabase::getInstance();

        if(is_string($config)) {
            $config = array('value' => $config);
        }

        // default calculations
        switch($key) {
            case 'no_asset':
                $customFreeTableName = self::$tableHander->getCustomTableName('vtiger_crmentity');
                $tableName = self::$tableHander->addJoinTable('vtiger_crmentity', 'crmid', 'vtiger_assets', 'product', 'SELECT product, assetsid FROM vtiger_assets INNER JOIN vtiger_crmentity as '.$customFreeTableName.' ON ('.$customFreeTableName.'.crmid = vtiger_assets.assetsid AND '.$customFreeTableName.'.deleted = 0) GROUP BY assetsid','no-assets');

                return "`".$tableName.'`.`assetsid` ' . ($not ? "IS" : "IS NOT" ) . " NULL";
                break;
            case 'buy_products':
                if(is_array($config['products'])) $config['products'] = $config['products'][0];
                $customFreeTableName = self::$tableHander->getCustomTableName('vtiger_crmentity');
                $customFreeTableName2 = self::$tableHander->getCustomTableName('vtiger_inventoryproductrel');
                $tableName = self::$tableHander->addJoinTable(
                    'vtiger_crmentity',
                    'crmid',
                    'vtiger_invoice',
                    'accountid',
                    'SELECT accountid, invoiceid
                          FROM vtiger_invoice
                          INNER JOIN vtiger_crmentity as '.$customFreeTableName.' ON ('.$customFreeTableName.'.crmid = vtiger_invoice.invoiceid AND '.$customFreeTableName.'.deleted = 0)
                          INNER JOIN vtiger_inventoryproductrel as '.$customFreeTableName2.' ON ('.$customFreeTableName2.'.id = vtiger_invoice.invoiceid AND '.$customFreeTableName2.'.productid IN('.$config['products'].'))
                          GROUP BY accountid',
                    'accounts-products-buy');

                return "`".$tableName.'`.`invoiceid` ' . ($not ? "IS" : "IS NOT" ) . " NULL";
                break;
        }

        return '';
    }

}

\SWSearchPlus\ConditionOperator::register('products', '\\SWSearchPlus\\Plugin\\ProductsConditionOperator');