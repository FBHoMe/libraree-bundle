<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\BasePinModel;
use Home\CustomizeeBundle\Resources\contao\dca\BasePinDca;
use Home\LibrareeBundle\Resources\contao\models\BasePortfolioModel;
use Home\PearlsBundle\Resources\contao\Helper as Helper;
use Home\TaxonomeeBundle\Resources\contao\models\TaxonomeeModel;

class DynListElement extends BaseListElement
{
    /**
     * @var string
     */
    protected $strTemplate = 'cte_list';
    protected $order = 'id DESC';

    /**
     * @return string
     */
    public function generate()
    {
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
        return BasePortfolioModel::findByTable($table, [$table . '_portfolio.published = 1', $table . '_portfolio.pid = ?'], $portfolio['id'], array('order'=>$this->order));
    }

    /**
     * get Portfolio
     * @param $table
     * @param $alias
     * @return array|null
     */
    private function getPortfolio($table, $alias)
    {
        return BasePortfolioModel::findByTable($table, [$table . '_portfolio.published = 1', $table . '_portfolio.alias = ?'], $alias, array('order'=>$this->order));
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
        return BasePinModel::findByTable($table, [$table . '_pin.published = 1', $table . '_pin.pid = ?'], $portfolio['id'], array('order'=>$this->order));
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