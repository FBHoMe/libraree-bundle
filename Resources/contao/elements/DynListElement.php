<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\BasePinModel;
use Home\LibrareeBundle\Resources\contao\BasePinDca;
use Home\LibrareeBundle\Resources\contao\models\BasePortfolioModel;
use Home\PearlsBundle\Resources\contao\Helper as Helper;
use Home\TaxonomeeBundle\Resources\contao\models\TaxonomeeModel;

class DynListElement extends BaseListElement
{
    /**
     * @var string
     */
    protected $strTemplate = 'cte_list';

    /**
     * @return string
     */
    public function generate()
    {
        // #-- get file path from url
        $file = \Contao\Input::get('file', true);
        // #-- get file object from path
        $objFile = \FilesModel::findByPath($file);

        // Send the file to the browser and do not send a 404 header (see #4632)
        if ($file != '' && $file == $objFile->path)
        {
            \Contao\Controller::sendFileToBrowser($file);
        }

        return parent::generate();
    }

    /**
     * Generate module
     */
    protected function compile()
    {
        parent::compile();

        if (TL_MODE != 'BE') {
            $this->generateFrontend();
        }

    }

    /**
     * generate frontend for module
     */
    private function generateFrontend()
    {
        #-- check if pin/portfolio order is set
        if($GLOBALS['libraree']['pinOrder'] === null){
            $GLOBALS['libraree']['pinOrder'] = 'id DESC';
        }
        if($GLOBALS['libraree']['portfolioOrder'] === null){
            $GLOBALS['libraree']['portfolioOrder'] = 'id DESC';
        }

        #-- get auto item
        if (!isset($_GET['item']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            \Contao\Input::setGet('item', \Contao\Input::get('auto_item'));
        }

        if($_GET['auto_item']){
            $alias = $_GET['auto_item'];
            $table = $this->lib_nav_table;

            $this->Template->lib_nav_href = $this->lib_nav_href;

            #-- get portfolio from alias
            $portfolio = $this->getPortfolio($table, $alias);

            #-- if portfolio was found get child portfolios and pins
            if(is_array($portfolio) && count($portfolio) > 0){
                $portfolio = $portfolio[0];

                $this->Template->portfolio = $portfolio;
                $this->Template->portfolios = $this->getPortfolios($table, $portfolio);
                $this->Template->pins = $this->getPins($table, $portfolio);
                $this->Template->taxonomies = BasePinDca::getTaxonomieFromTable($this->lib_nav_table);

            }else{
                #-- if no portfolio with alias was fount check if there is a pin with alias
                $pin = $this->getPin($table, $alias);

                if(is_array($pin) && count($pin) > 0){
                    $this->Template->pin = $pin[0];
                }
            }
        }
    }

    /**
     * get child portfolios
     *
     * @param $table
     * @param $portfolio
     * @return array|null
     */
    private function getPortfolios($table, $portfolio)
    {
        $strColumn = array(
            $table . '_portfolio.published = 1',
            $table . '_portfolio.pid = ?'
        );
        $options = array('order'=>$GLOBALS['libraree']['portfolioOrder']);

        return BasePortfolioModel::findByTable($table, $strColumn, $portfolio['id'], $options);
    }

    /**
     * get Portfolio
     * @param $table
     * @param $alias
     * @return array|null
     */
    private function getPortfolio($table, $alias)
    {
        $strColumn = array(
            $table . '_portfolio.published = 1',
            $table . '_portfolio.alias = ?'
        );
        $options = array('order'=>$GLOBALS['libraree']['portfolioOrder']);

        return BasePortfolioModel::findByTable($table, $strColumn, $alias, $options);
    }

    /**
     * get pins
     *
     * @param $table
     * @param $portfolio
     * @return array|null
     */
    private function getPins($table, $portfolio)
    {
        $strColumn = array(
            $table . '_pin.published = 1',
            $table . '_pin.pid = ?'
        );
        $options = array('order'=>$GLOBALS['libraree']['pinOrder']);

        return BasePinModel::findByTable($table, $strColumn, $portfolio['id'], $options);
    }

    /**
     * get pin
     *
     * @param $table
     * @param $alias
     * @return array|null
     */
    private function getPin($table, $alias)
    {
        $options = array(
            $table . '_pin.published = 1',
            $table . '_pin.alias = "' . $alias . '"',
        );
        return BasePinModel::findByTable($table, $options);
    }

    public function getTaxonomieFromTable($table)
    {
        $return = array();
        $searchAlias = strtolower(explode('_', $table)[1]);

        $parent = TaxonomeeModel::findBy(array(
            TaxonomeeModel::getTable() . '.alias LIKE "' . $searchAlias . '"'
        ), null);

        if($parent){
            $parent = $parent->row();
        }

        if(is_array($parent) && count($parent) > 0){
            #-- get all pins with keywords portfolio as parent
            $options = TaxonomeeModel::findBy(array(
                TaxonomeeModel::getTable() . ".pid = " . $parent['id']
            ), null);

            if($options){
                $options = $options->fetchAll();
            }

            if(is_array($options) && count($options) > 0){
                foreach ($options as $row){
                    $return[$row['id']] = $row['name'];
                }
            }
        }

        return $return;
    }
}