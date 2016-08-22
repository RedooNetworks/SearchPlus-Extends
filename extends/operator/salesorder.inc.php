<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 18.06.15 16:21
 * You must not use this file without permission.
 */
namespace SWSearchPlus\Plugin;

class SalesOrderConditionOperator extends \SWSearchPlus\ConditionOperator
{
    public function getOperators($moduleName) {
        $operators = array();
        //var_dump($moduleName);exit();
        if($moduleName === 'SalesOrder') {
            $operators = array(
                'solinked_invoice' => array(
                    'config' => array(
                    ),
                    'label' => 'Invoice existing',
                    'fieldtypes' => array('crmid'),
                ),
            );
        }

        if($moduleName === 'Quotes') {
            $operators = array(
                'quotelinked_so' => array(
                    'config' => array(
                    ),
                    'label' => 'Order existing',
                    'fieldtypes' => array('crmid'),
                ),
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
            case 'solinked_invoice':
                $tableName = self::$tableHander->addJoinTable('vtiger_crmentity', 'crmid', 'vtiger_invoice', 'salesorderid', 'SELECT invoiceid, salesorderid FROM vtiger_invoice GROUP BY salesorderid');
                return "`".$tableName.'`.`invoiceid` ' . ($not ? "IS" : "IS NOT" ) . " NULL";
                break;
            case 'quotelinked_so':
                $tableName = self::$tableHander->addJoinTable('vtiger_crmentity', 'crmid', 'vtiger_salesorder', 'quoteid', 'SELECT salesorderid, quoteid FROM vtiger_salesorder GROUP BY quoteid');
                return "`".$tableName.'`.`salesorderid` ' . ($not ? "IS" : "IS NOT" ) . " NULL";
                break;
        }

    }

}

\SWSearchPlus\ConditionOperator::register('salesorder', '\\SWSearchPlus\\Plugin\\SalesOrderConditionOperator');