<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 13:25
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_personalee_portfolio';

$GLOBALS['TL_DCA'][$moduleName] = [
    'palettes' => [
        '__selector__' => [],
    ],
    'subpalettes' => [
        '' => ''
    ]
];

$tl_promotee = new Helper\DcaHelper($moduleName);

$tl_promotee
    #-- Config
    ->addConfig('liste', array('ctable' => array('tl_personalee_pin')))
    #-- List
    ->addList('base')
    #-- Sorting
    ->addSorting('tree')
    #-- Fields
    ->addField('id', 'id')
    ->addField('pid', 'pid', array('foreignKey' => 'tl_personalee_portfolio.id'))
    ->addField('alias','alias')
    ->addField('tstamp', 'tstamp')
    ->addField('name', 'name')
    ->addField('published', 'published')
    ->addField('name','title')
    ->addField('sorting', 'sorting')
    // seitenlink
    #-- Operations
    ->addOperation('edit', 'edit', array('href' => 'table=tl_personalee_pin'),'_first')
    ->addOperation('editheader', 'editheader')
    ->addOperation('copy')
    ->addOperation('delete','delete',array('attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;"'))
    ->addOperation('show')
    #-- Global_Operations
    ->addGlobalOperation('toggleNodes')
    ->addGlobalOperation('all')
    #-- Palette
    ->addPaletteGroup('default', array('name', 'title', 'alias'))
    ->addPaletteGroup('published', array('published'))
;

//var_dump($GLOBALS['TL_DCA'][$moduleName]);
