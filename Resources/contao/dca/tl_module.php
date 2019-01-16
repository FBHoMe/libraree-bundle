<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.09.2017
 * Time: 16:33
 */

namespace Home\LibrareeBundle\Resources\contao\dca;
use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_module';

try{
    $tl_content = new Helper\DcaHelper($moduleName);
    $tl_content
        #-- Libraree Navigation --------------------------------------------------------------------------------------------------------
        ->addField('integer','lib_nav_depth', array(
            'sql' => "int(10) DEFAULT 0 NOT NULL",
        ))
        ->addField('checkbox','lib_nav_pins')
        ->addField('select', 'lib_nav_table', array(
            'eval' => array(
                'mandatory' => true,
                'includeBlankOption' => true,
                'submitOnChange' => true,
            ),
            'options_callback' => array('Home\LibrareeBundle\Resources\contao\dca\tl_module','getTableOptions'),
            'load_callback'    => array(array('Home\LibrareeBundle\Resources\contao\dca\tl_module','setTable'))
        ))
        ->addField('select', 'lib_nav_portfolio', array(
            'eval' => array(
                'includeBlankOption' => true,
            ),
            'options_callback' => array('Home\LibrareeBundle\Resources\contao\dca\tl_module','getPortfolioOptions'),
        ))
        ->addField('select_template', 'lib_nav_template_mod', array(
            'tempPrefix' => 'mod_'
        ))
        ->addField('select_template', 'lib_nav_template_nav', array(
            'tempPrefix' => 'nav_'
        ))
        ->addField('text', 'lib_nav_href')

        #-- mod_nav_libraree
        ->copyPalette('default', 'mod_nav_libraree')
        ->addPaletteGroup('mod_nav_libraree', array(
            'lib_nav_table',
            'lib_nav_portfolio',
            'pages',
            'lib_nav_href',
            'lib_nav_depth',
            'lib_nav_pins',
            'lib_nav_template_mod',
            'lib_nav_template_nav'), 'mod_nav_libraree')

    ;
}catch(\Exception $e){
    var_dump($e);
}

class tl_module extends \Backend
{
    /**
     * sets lib_nav_table in $GLOBALS to be accessible in getPortfolioOptions
     *
     * @param $varValue
     * @param \DataContainer $dc
     * @return mixed
     */
    public function setTable($varValue, \DataContainer $dc)
    {
        $GLOBALS['lib_nav_table'] = $varValue;
        return $varValue;
    }

    /**
     * @param \DataContainer $dc
     * @return array
     */
    public function getTableOptions(\DataContainer $dc)
    {
        $sql = "
            SHOW TABLES LIKE '%_portfolio%';
        ";

        $result = $this->Database->prepare($sql)->execute()->fetchAllAssoc();
        $return = array();

        if(is_array($result) && count($result) > 0){
            foreach ($result as $tableArray){
                if(is_array($tableArray) && count($tableArray) > 0){
                    foreach ($tableArray as $table){
                        $return[] = str_replace('_portfolio', '', $table);
                    }
                }
            }
            asort($return);
        }

        return $return;
    }

    /**
     * @param \DataContainer $dc
     * @return array
     */
    public function getPortfolioOptions(\DataContainer $dc)
    {
        $return = array();

        if($GLOBALS['lib_nav_table']){
            $sql = "
                SELECT id, name FROM " . $GLOBALS['lib_nav_table'] ."_portfolio
            ";
            $result = $this->Database->prepare($sql)->execute()->fetchAllAssoc();

            if(is_array($result) && count($result) > 0){
                foreach ($result as $portfolio){
                    $return[$portfolio['id']] = $portfolio['name'];
                }
            }
            asort($return);
        }

        return $return;
    }
}


