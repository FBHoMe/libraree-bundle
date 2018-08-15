<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 13:25
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_productee_closures';

$GLOBALS['TL_DCA'][$moduleName] = [];

$tl_promotee = new Helper\DcaHelper($moduleName);

$tl_promotee
    #-- Config
    ->addConfig('closure')
    #-- List

    #-- Sorting

    #-- Fields
    ->addField('idref', 'ancestor_id')
    ->addField('idref', 'descendant_id')
    ->addField('integer', 'path_length', array('exclude'=>false))
    #-- Operations

    #-- Global_Operations

    #-- Palette

;

//var_dump($GLOBALS['TL_DCA'][$moduleName]);
