<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 15.09.2017
 * Time: 11:58
 */

#-- add content elements -----------------------------------------------------------------------------------------------
array_insert($GLOBALS['BE_MOD']['content'], 3 ,[
    'recipee' => [
        'tables' => ['tl_recipee_portfolio','tl_recipee_pin','tl_recipee_closures'],
        'table' => ['TableWizard', 'importTable'],
        'list' => ['ListWizard', 'importList']
    ],
    /*'productee' => [
        'tables' => ['tl_productee_portfolio','tl_productee_pin','tl_productee_closures'],
        'table' => ['TableWizard', 'importTable'],
        'list' => ['ListWizard', 'importList']
    ],
    'personalee' => [
        'tables' => ['tl_personalee_portfolio','tl_personalee_pin','tl_personalee_closures'],
        'table' => ['TableWizard', 'importTable'],
        'list' => ['ListWizard', 'importList']
    ]*/
]);

#-- add content elements -----------------------------------------------------------------------------------------------
array_insert($GLOBALS['TL_CTE'], 2, array
(
    'recipee' => array
    (
        'recipee_list_cte'     => 'Home\LibrareeBundle\Resources\contao\elements\RecipeeListElement',
        'recipee_detail_cte'   => 'Home\LibrareeBundle\Resources\contao\elements\RecipeeDetailElement',
        'dyn_lib_list_cte'         => 'Home\LibrareeBundle\Resources\contao\elements\DynListElement',
    ),
    /*'productee' => array
    (
        'productee_list_cte'     => 'Home\LibrareeBundle\Resources\contao\elements\ProducteeListElement',
        'productee_detail_cte'   => 'Home\LibrareeBundle\Resources\contao\elements\ProducteeDetailElement',
    ),
    'personalee' => array
    (
        'personalee_list_cte'     => 'Home\LibrareeBundle\Resources\contao\elements\PersonaleeListElement',
        'personalee_detail_cte'   => 'Home\LibrareeBundle\Resources\contao\elements\PersonaleeDetailElement',
    )*/
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
#-- productee
$GLOBALS['TL_MODELS']['tl_productee_pin'] = 'Home\LibrareeBundle\Resources\contao\models\ProducteePinModel';
$GLOBALS['TL_MODELS']['tl_productee_portfolio'] = 'Home\LibrareeBundle\Resources\contao\models\ProducteePortfolioModel';
#-- personalee
$GLOBALS['TL_MODELS']['tl_personalee_pin'] = 'Home\LibrareeBundle\Resources\contao\models\PersonaleePinModel';
$GLOBALS['TL_MODELS']['tl_personalee_portfolio'] = 'Home\LibrareeBundle\Resources\contao\models\PersonaleePortfolioModel';
#-- recipee
$GLOBALS['TL_MODELS']['tl_recipee_pin'] = 'Home\LibrareeBundle\Resources\contao\models\RecipeePinModel';
$GLOBALS['TL_MODELS']['tl_recipee_portfolio'] = 'Home\LibrareeBundle\Resources\contao\models\RecipeePortfolioModel';

#-- filter -------------------------------------------------------------------------------------------------------------
$GLOBALS['TL_CTE_FILTER']['tl_taxonomee'] = array(
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
);

#-- redirects ----------------------------------------------------------------------------------------------------------
$GLOBALS['BE_MOD']['content']['Module'] = array(
    'callback'   => 'Home\PearlsBundle\Resources\contao\Helper\BeModRedirect',
    'action'     => array('link'=>'/contao?do=themes&table=tl_module&id=1'),
);

#-- hooks --------------------------------------------------------------------------------------------------------------
$GLOBALS['TL_HOOKS']['processFormData'][] = array('Home\LibrareeBundle\Resources\contao\hooks\LibrareeHooks', 'sendCartEmails');

#-- operation csvExport
$GLOBALS['BE_MOD']['content']['recipee']['csvExport'] = array('Home\PearlsBundle\Resources\contao\Helper\Dca\Operations\CsvExport','exportAsCsv');

