<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 13:25
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_personalee_pin';

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
    ->addConfig('liste', array('ptable' => 'tl_personalee_portfolio'))
    #-- List
    ->addList('base')
    #-- Sorting
    ->addSorting('liste')
    #-- Fields
    ->addField('id', 'id')
    ->addField('pid', 'pid', array('foreignKey' => 'tl_productee_portfolio.id'))
    ->addField('alias','alias')
    ->addField('tstamp', 'tstamp')
    ->addField('name', 'name')
    ->addField('published', 'published')
    ->addField('name','title')
    ->addField('textarea', 'teaser')
    ->addField('tiny', 'text')
    ->addField('image', 'image')
    // seitenlink
    #-- Operations
    ->addOperation('edit', 'edit', array(),'_first')
    ->addOperation('copy')
    ->addOperation('delete','delete',array('attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;"'))
    ->addOperation('show')
    #-- Global_Operations

    #-- Palette
    ->addPaletteGroup('default', array('name', 'title', 'alias', 'teaser', 'text', 'image', 'taxonomee'))
    ->addPaletteGroup('published', array('published'))
;

//var_dump($GLOBALS['TL_DCA'][$moduleName]);
