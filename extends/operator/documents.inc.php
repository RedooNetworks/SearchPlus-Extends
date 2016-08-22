<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 18.06.15 16:21
 * You must not use this file without permission.
 */
namespace SWSearchPlus\Plugin;

class DocumentsConditionOperator extends \SWSearchPlus\ConditionOperator
{
    public function getOperators($moduleName) {
        $operators = array();

        $operators = array(
            'have_related_documents' => array(
                'config' => array(
                ),
                'label' => 'have related Documents',
                'fieldtypes' => array('crmid'),
            ),
        );

        return $operators;
    }

    public function generateSQLCondition($key, $columnName, $config, $not) {
        $adb = \PearDatabase::getInstance();

        if(is_string($config)) {
            $config = array('value' => $config);
        }

        // default calculations
        switch($key) {
            case 'have_related_documents':
                $tableName = self::$tableHander->addJoinTable('vtiger_crmentity', 'crmid', 'vtiger_senotesrel', 'crmid');
                return "`".$tableName.'`.`crmid` ' . ($not ? "IS" : "IS NOT" ) . " NULL";
                break;
        }

    }

}

\SWSearchPlus\ConditionOperator::register('reldocuments', '\\SWSearchPlus\\Plugin\\DocumentsConditionOperator');