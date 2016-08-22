<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 18.06.15 16:21
 * You must not use this file without permission.
 */
namespace SWSearchPlus\Plugin\Field;

class TotalInvoiceSummarize extends \SWSearchPlus\Customfield
{
    private $columnName = null;
    public function getFields($moduleName) {
        $operators = array();

        if($moduleName === 'Accounts') {
            $operators = array (
                'invoice_sumA' => array (
                    'label' => 'Invoice total SUM',
                    'columnname' => '_totalinvoicesum',
                    'typeofdata' => 'N~O',
                    'uitype' => 72,
                    'displaytype' => 3,
                ),
                'invoice_openA' => array (
                    'label' => 'Invoice open SUM',
                    'columnname' => '_totalopeninvoicesum',
                    'typeofdata' => 'N~O',
                    'uitype' => 72,
                    'displaytype' => 3,
                ),
            );
        }

        return $operators;
    }

    public function execute($key, $moduleName) {
        $adb = \PearDatabase::getInstance();

        // default calculations
        switch($key) {
            case 'invoice_sumA':
                $tableName = self::$tableHander->addJoinTable('vtiger_crmentity', 'crmid', 'vtiger_invoice', 'accountid', 'SELECT ROUND(SUM(total)) as _totalinvoicesum, accountid FROM vtiger_invoice GROUP BY vtiger_invoice.accountid');
                break;
            case 'invoice_openA':
                $tableName = self::$tableHander->addJoinTable('vtiger_crmentity', 'crmid', 'vtiger_invoice', 'accountid', 'SELECT ROUND(SUM(total)) as _totalopeninvoicesum, accountid FROM vtiger_invoice WHERE invoicestatus != "Paid" GROUP BY vtiger_invoice.accountid');
                break;
        }

        return $tableName;
    }

}

\SWSearchPlus\Customfield::register('totalinvoice', '\\SWSearchPlus\\Plugin\\Field\\TotalInvoiceSummarize');