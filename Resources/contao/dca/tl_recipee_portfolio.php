<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 13:25
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_recipee_portfolio';

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
    ->addConfig('tree', array(
        'ctable' => array('tl_recipee_pin')
    ))
    #-- List
    ->addList('base')
    #-- Sorting
    ->addSorting('tree')
    #-- Fields
    ->addField('id', 'id'
        /*,
        array(
            'relation' => array(
                'type'=>'hasOne',
                'load'=>'eager',
                'table'=>'tl_productee_pin',
                'field'=>'pid',
            ),
        )*/
        )
    ->addField('pid', 'pid', array(
        'foreignKey' => 'tl_recipee_portfolio.id',
        'relation' => array(
            'type'=>'belongsTo',
            'load'=>'eager',
            'table'=>'tl_recipee_portfolio',
            'field'=>'id',
        ),
    ))
    ->addField('alias','alias')
    ->addField('tstamp', 'tstamp')
    ->addField('name', 'name')
    ->addField('published', 'published')
    ->addField('name','title')
    ->addField('sorting', 'sorting')
    // seitenlink
    #-- Operations
    ->addOperation('edit', 'edit', array('href' => 'table=tl_recipee_pin'),'_first')
    ->addOperation('editheader', 'editheader')
    ->addOperation('copy')
    ->addOperation('delete','delete',array('attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;"'))
    ->addOperation('show')
    ->addOperation('csvExport')
    #-- Global_Operations
    ->addGlobalOperation('toggleNodes')
    ->addGlobalOperation('all')
    #-- Palette
    ->addPaletteGroup('default', array('name', 'title', 'alias'))
    ->addPaletteGroup('published', array('published'))
;

//var_dump($GLOBALS['TL_DCA'][$moduleName]);
