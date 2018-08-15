<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.09.2017
 * Time: 16:33
 */

namespace Home\LibrareeBundle\Resources\contao\dca;
use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_content';

$tl_content = new Helper\DcaHelper($moduleName);

try{
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

    #-- mod_nav_libraree
    ->copyPalette('default', 'dyn_lib_list_cte')
    ->addPaletteGroup('dyn_lib_list_cte', array(
        'lib_nav_table'
    ), 'dyn_lib_list_cte')
;
}catch(\Exception $e){
    var_dump($e);
}

class tl_content extends \Backend
{
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

