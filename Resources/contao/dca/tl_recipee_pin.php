<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 13:25
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_recipee_pin';

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
    #-- Config ---------------------------------------------------------------------------------------------------------
    ->addConfig('liste', array(
        'ptable' => 'tl_recipee_portfolio'
    ))
    #-- List -----------------------------------------------------------------------------------------------------------
    ->addList('base')
    #-- Sorting --------------------------------------------------------------------------------------------------------
    ->addSorting('liste')
    #-- Fields default -------------------------------------------------------------------------------------------------
    ->addField('id', 'id')
    ->addField('pid', 'pid', array(
        'foreignKey' => 'tl_recipee_portfolio.id',
        'relation' => array(
            'type'=>'belongsTo',
            'load'=>'eager',
            'table'=>'tl_recipee_portfolio',
            'field'=>'id',
        ),
    ))
    ->addField('tstamp', 'tstamp')
    ->addField('name', 'name')
    ->addField('published', 'published')
    ->addField('name','title')
    ->addField('alias','alias')
    #-- Fields ---------------------------------------------------------------------------------------------------------
    ->addField('textarea', 'teaser')
    ->addField('select_from_table','category', array(
            'foreignKey' => 'tl_taxonomee.name',
            'relation' => array(
                'type'=>'hasOne',
                'load'=>'eager',
                'table'=>'tl_taxonomee',
                'field'=>'id',
            ),
        )
    )
    ->addField('gallery', 'gallery', array(
        'orderField' => 'galleryOrder'
    ))
    ->addField('select', 'difficulty', array(
        'options'=>array(
            'Einfach',
            'Mittel',
            'Anspruchsvoll'
        )
    ))
    ->addField('integer', 'servings')
    ->addField('mcw', 'groceries', array(
        'eval' => array(
            'columnFields' => array(
                'grocery' => array(
                    'label'         => &$GLOBALS['TL_LANG'][$moduleName]['grocery'],
                    'inputType'		=> 'text',
                    'exclude'		=> true,
                    'eval'			=> array('maxlength'=>255),
                    'sql'			=> "varchar(255) NOT NULL default ''",
                ),
                'amount' => array(
                    'label'         => &$GLOBALS['TL_LANG'][$moduleName]['amount'],
                    'inputType'		=> 'text',
                    'exclude'		=> true,
                    'eval'			=> array('maxlength'=>10),
                    'sql'			=> "int(10) NULL",
                ),
                'unit' => array(
                    'label'         => &$GLOBALS['TL_LANG'][$moduleName]['unit'],
                    'exclude'       => true,
                    'inputType'     => 'select',
                    'sql'           => "varchar(255) NOT NULL default ''",
                    'options'       => array(
                        '',
                        'Teelöffel',
                        'Esslöffel',
                        'Gramm',
                        'Kilogramm',
                        'Milliliter',
                        'Liter',
                        'Päckchen',
                        'Packung',
                    )
                )
            )
        )
    ))
    ->addField('mcw', 'duration', array(
        'eval'                    => array(
            'columnFields' => array(
                'minutes' => array(
                    'label'         => &$GLOBALS['TL_LANG'][$moduleName]['minutes'],
                    'inputType'		=> 'text',
                    'exclude'		=> true,
                    'eval'			=> array('maxlength'=>10),
                    'sql'			=> "int(10) NULL",
                ),
                'duration-type'     => array(
                    'label'         => &$GLOBALS['TL_LANG'][$moduleName]['duration-type'],
                    'exclude'       => true,
                    'inputType'     => 'select',
                    'sql'           => "varchar(255) NOT NULL default ''",
                    'options'       => array(
                        'Vorbereiten',
                        'Kochen',
                        'Backen',
                        'Abkühlen',
                    )
                )
            )
        )
    ))
    ->addField('mcw', 'steps', array(
        'eval'                    => array(
            'columnFields' => array(
                'image' => array(
                    'label'         => &$GLOBALS['TL_LANG'][$moduleName]['image'],
                    'inputType'     => 'fileTree',
                    'exclude'       => true,
                    'eval'          => array(
                        'files'         => true,
                        'filesOnly'     => true,
                        'extensions'    => 'jpg,jpeg,gif,png,svg',
                        'fieldType'     => 'radio'
                    ),
                    'sql' => "blob NULL"
                ),
                'description' => array(
                    'label'         => &$GLOBALS['TL_LANG'][$moduleName]['description'],
                    'inputType'		=> 'textarea',
                    'exclude'		=> true,
                    'search'        => true,
                    'eval'			=> array('rte'=>'tinyMCE'),
                    'sql'			=> "text NULL",
                )
            )
        )
    ))
    ->addField('tiny', 'note')
    ->addField('text', 'author')
    #-- Operations -----------------------------------------------------------------------------------------------------
    ->addOperation('edit', 'edit', array(),'_first')
    ->addOperation('copy')
    ->addOperation('delete','delete',array(
        'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;"'
    ))
    //->addOperation('toggle', 'toggle')
    ->addOperation('show')
    #-- Global_Operations ----------------------------------------------------------------------------------------------

    #-- Palette --------------------------------------------------------------------------------------------------------
    ->addPaletteGroup('default', array(
        'name',
        'title',
        'alias',
        'teaser',
        'gallery',
        'category',
        'difficulty',
        'servings',
    ))
    ->addPaletteGroup('groceries', array(
        'groceries',
    ))
    ->addPaletteGroup('durations', array(
        'duration',
    ))
    ->addPaletteGroup('steps', array(
        'steps',
    ))
    ->addPaletteGroup('notes', array(
        'note',
    ))
    ->addPaletteGroup('published', array('published'))
;

//var_dump($GLOBALS['TL_DCA'][$moduleName]);
