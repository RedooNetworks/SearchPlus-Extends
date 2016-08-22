<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 18.06.15 16:21
 * You must not use this file without permission.
 */
namespace SWSearchPlus\Plugin\Field;

class PricebookField extends \SWSearchPlus\Customfield
{
    private $columnName = null;

    public function getFields($moduleName) {
        $fields = array();

        $adb = \PearDatabase::getInstance();
        $sql = 'SELECT pricebookid, bookname FROM vtiger_pricebook';
        $result = $adb->query($sql);

        while($row = $adb->fetchByAssoc($result)) {
            $fields['_pricebookprice_'.$row['pricebookid']] = array(
                'label' => vtranslate('SINGLE_PriceBooks', 'PriceBooks').' '.$row['bookname'],
                'columnname' => '_pricebookprice_'.$row['pricebookid'],
                'typeofdata' => 'N~O',
                'uitype' => 72,
                'displaytype' => 3,
            );
        }

        return $fields;
    }

    public function execute($key, $moduleName) {
        $tableName = '';
        $adb = \PearDatabase::getInstance();

        $parts = explode('_', $key);

        if($parts[1] == 'pricebookprice') {
            $tableName = self::$tableHander->addJoinTable('vtiger_products', 'productid', 'vtiger_products', 'productid', 'SELECT listprice as _pricebookprice_'.intval($parts[2]).',productid  FROM vtiger_pricebookproductrel WHERE pricebookid = '.intval($parts[2]));
        }

        return $tableName;
    }

}

\SWSearchPlus\Customfield::register('pricebooks', '\\SWSearchPlus\\Plugin\\Field\\PricebookField');