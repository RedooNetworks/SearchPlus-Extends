<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 18.06.15 16:21
 * You must not use this file without permission.
 */
namespace SWSearchPlus\Plugin\Field;

class RelationsField extends \SWSearchPlus\Customfield
{
    private $columnName = null;
    private $relations = array(
        'Products' => array(
            'Assets' => array('vtiger_assets', 'product')
        ),
        'Assets' => array(
            'Documents' => array('vtiger_senotesrel', 'crmid')
        )
    );

    public function getFields($moduleName) {
        $fields = array();

        if(isset($this->relations[$moduleName])) {
            foreach($this->relations[$moduleName] as $relatedModule => $config) {
                $fields['_crelations_'.$relatedModule] = array(
                    'label' => 'Relations '.vtranslate($relatedModule, 'Vtiger').' COUNT',
                    'columnname' => '_crelations_'.strtolower($relatedModule),
                    'typeofdata' => 'V~O',
                    'uitype' => 1,
                    'displaytype' => 3,
                );
            }
        }

        return $fields;
    }

    public function execute($key, $moduleName) {
        $tableName = '';
        $adb = \PearDatabase::getInstance();

        $parts = explode('_', $key);

        if($parts[1] == 'crelations') {
            if(isset($this->relations[$moduleName][$parts[2]])) {
                $config = $this->relations[$moduleName][$parts[2]];
                $tableName = self::$tableHander->addJoinTable('vtiger_crmentity', 'crmid', $config[0], $config[1], 'SELECT COUNT(*) as '.strtolower($key).', '.$config[1].' FROM '.$config[0].' GROUP BY '.$config[0].'.'.$config[1]);
            }
        }


        return $tableName;
    }

}

\SWSearchPlus\Customfield::register('relations', '\\SWSearchPlus\\Plugin\\Field\\RelationsField');