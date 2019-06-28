<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 15.09.2017
 * Time: 11:58
 */

#-- add content elements -----------------------------------------------------------------------------------------------
array_insert($GLOBALS['BE_MOD']['content'], 3 ,[
    /*'example' => [
        'tables' => ['tl_example_portfolio','tl_example_pin','tl_example_closures'],
        'table' => ['TableWizard', 'importTable'],
        'list' => ['ListWizard', 'importList']
    ],*/
]);

#-- add content elements -----------------------------------------------------------------------------------------------
array_insert($GLOBALS['TL_CTE'], 2, array
(
    'libraree' => array(
        'lib_list' => 'Home\LibrareeBundle\Resources\contao\elements\BaseListElement',
        'select_pin' => 'Home\LibrareeBundle\Resources\contao\elements\SelectPinElement',
        'dyn_lib_list' => 'Home\LibrareeBundle\Resources\contao\elements\DynListElement',
        'proximity_map' => 'Home\LibrareeBundle\Resources\contao\elements\ProximityMap',
    )
));

#-- add modules --------------------------------------------------------------------------------------------------------
array_insert($GLOBALS['FE_MOD'], 2, array
(
    'libraree' => array
    (
        'mod_nav_libraree'     => 'Home\LibrareeBundle\Resources\contao\modules\NavigationModule'
    ),
));

#-- add models ---------------------------------------------------------------------------------------------------------

#-- filter -------------------------------------------------------------------------------------------------------------
/*$GLOBALS['TL_CTE_FILTER']['tl_taxonomee'] = array(
    "filter" => array(
        "9" => array(
            "target" => "product",
        ),
        "10" => array(
            "target" => "article"
        ),
        "11" => array(
            "target" => "collection"
        ),
        "12" => array(
            "target" => "model"
        ),
        "13" => array(
            "target" => "features"
        ),
    ),
    "default" => array(
        "db" => "tl_taxonomee",
        "where" => "=",
    ),
);*/

#-- redirects ----------------------------------------------------------------------------------------------------------
$GLOBALS['BE_MOD']['content']['Module'] = array(
    'callback'   => 'Home\PearlsBundle\Resources\contao\Helper\BeModRedirect',
    'action'     => array('link'=>'/contao?do=themes&table=tl_module&id=1'),
);

#-- hooks --------------------------------------------------------------------------------------------------------------
#$GLOBALS['TL_HOOKS']['processFormData'][] = array('Home\LibrareeBundle\Resources\contao\hooks\LibrareeHooks', 'sendCartEmails');

#-- operation csvExport
#$GLOBALS['BE_MOD']['content']['recipee']['csvExport'] = array('Home\PearlsBundle\Resources\contao\Helper\Dca\Operations\CsvExport','exportAsCsv');

