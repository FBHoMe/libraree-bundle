<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.09.2017
 * Time: 16:33
 */

namespace Home\LibrareeBundle\Resources\contao\dca;
use Contao\ContentModel;
use Contao\DataContainer;
use Home\LibrareeBundle\Resources\contao\models\BasePinModel;
use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_content';

try{
    $tl_content = new Helper\DcaHelper($moduleName);
    $tl_content
        #-- Libraree Navigation --------------------------------------------------------------------------------------------------------
        ->addField('select', 'lib_nav_table', array(
            'eval' => array(
                'mandatory' => true,
                'includeBlankOption' => true,
            ),
            'options_callback' => array('Home\LibrareeBundle\Resources\contao\dca\tl_module','getTableOptions'),
            'load_callback'    => array(array('Home\LibrareeBundle\Resources\contao\dca\tl_module','setTable'))
        ))
        ->addField('text', 'lib_nav_href')

        #-- select pin
        ->addField('select', 'lib_table', array(
            'eval' => array(
                'mandatory' => true,
                'includeBlankOption' => true,
                'submitOnChange' => true,
            ),
            'options_callback' => array('Home\LibrareeBundle\Resources\contao\dca\tl_module','getTableOptions'),
            'load_callback'    => array(array('Home\LibrareeBundle\Resources\contao\dca\tl_module','setTable'))
        ))
        ->addField('select', 'lib_pin', array(
            'eval' => array(
                'chosen' => true,
                'includeBlankOption' => true,
            ),
            'options_callback' => array('Home\LibrareeBundle\Resources\contao\dca\tl_content','getPinSelectOptions'),
            'selectOption' => array('name','title'),
        ))

        ->addField('select_template', 'hm_template', array(
            'tempPrefix' => 'ce_'
        ))

        #-- taxonomy id
        ->addField('text', 'lib_tax_id')

        #-- pid
        ->addField('text', 'lib_pid')

        #-- base lib list
        ->copyPalette('default', 'lib_list')
        ->addPaletteGroup('lib_list', array(
            'lib_table',
            'lib_pid',
            'lib_nav_href',
            'hm_template',
        ), 'lib_list')

        #-- dyn lib list
        ->copyPalette('default', 'dyn_lib_list')
        ->addPaletteGroup('dyn_lib_list', array(
            'lib_table',
            'lib_nav_href',
            'hm_template',
        ), 'dyn_lib_list')

        #-- select pin
        ->copyPalette('default', 'select_pin')
        ->addPaletteGroup('select_pin', array(
            'lib_table',
            'lib_pin',
            'hm_template'
        ), 'select_pin')

        #-- proximity_map
        ->copyPalette('default', 'proximity_map')
        ->addPaletteGroup('proximity_map', array(
            'lib_table',
            'lib_tax_id',
            'hm_template',
        ), 'proximity_map')
    ;
}catch(\Exception $e){
    var_dump($e);
}

class tl_content extends \Backend
{
    public function getPinSelectOptions(DataContainer $dc)
    {
        $return = array();
        $option = $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['selectOption'];
        $contentModel = ContentModel::findById($dc->id);

        if($contentModel){
            $contentRow = $contentModel->row();
            if($contentRow && is_array($contentRow) && array_key_exists('lib_table', $contentRow) && $contentRow['lib_table']){
                $pinModel = BasePinModel::findAllByTable($contentRow['lib_table']);

                if($pinModel && is_array($pinModel) && count($pinModel) > 0){
                    foreach ($pinModel as $pin){
                        if(is_array($option) && count($option) > 0){
                            foreach ($option as $key => $item){
                                if($key === 0){
                                    $return[$pin['id']] = $pin[$item];
                                }else{
                                    $return[$pin['id']] .= ', ' . $pin[$item];
                                }
                            }
                        }else{
                            $return[$pin['id']] = $pin[$option];
                        }
                    }
                    asort($return);
                }
            }
        }

        return $return;
    }

    public function setFilter($varValue)
    {
        $GLOBALS['CTE_FILTER'] = $varValue;
        return $varValue;
    }

    public function filterOptions($dc)
    {
        $id = $dc->__get('id');
        $filter = str_replace('cteFilter_','', $id);

        $options = array(0 => '-');

        if(array_key_exists($filter, $GLOBALS['TL_CTE_FILTER']) && (array_key_exists('filter', $GLOBALS['TL_CTE_FILTER'][$filter]))){
            $pids = array_keys($GLOBALS['TL_CTE_FILTER'][$filter]['filter']);

            $db = $GLOBALS['TL_CTE_FILTER'][$filter]['default']['db'];

            if(is_array($pids) == count($pids) > 0){
                foreach ($pids as $pid){

                    if($pid && $pid != '-'){
                        $sql = "
                            SELECT ".$db.".name, ".$db.".id
                            FROM ".$db."
                            WHERE id = ".$pid."
                        ";

                        $result = $this->Database->prepare($sql)->execute()->fetchAllAssoc();

                        if(is_array($result) && count($result) > 0){
                            foreach ($result as $row){
                                $options[$row['id']] = $row['name'];
                            }
                        }
                    }
                }
            }
        }

        return $options;
    }

    public function valueOptions($dc)
    {
        $id = $dc->__get('id');
        $filter = str_replace('cteFilter_','', $id);
        $options = array(0 => '-');

        if(array_key_exists($filter, $GLOBALS['TL_CTE_FILTER']) && $GLOBALS['CTE_FILTER']){
            $db = $GLOBALS['TL_CTE_FILTER'][$filter]['default']['db'];

            if($GLOBALS['CTE_FILTER'] && $GLOBALS['CTE_FILTER'] != '-') {
                $sql = '
                SELECT ' . $db . '.name, ' . $db . '.id
                FROM ' . $db . '
                WHERE pid = ' . $GLOBALS['CTE_FILTER'] . '
            ';

                $result = $this->Database->prepare($sql)->execute()->fetchAllAssoc();

                if (is_array($result) && count($result) > 0) {
                    foreach ($result as $row) {
                        $options[$row['id']] = $row['name'];
                    }
                }
            }
        }


        return $options;
    }
}


