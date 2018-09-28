<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\BasePinModel;
use Home\LibrareeBundle\Resources\contao\models\BasePortfolioModel;
use Home\PearlsBundle\Resources\contao\Helper as Helper;

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

            #-- get portfolio from alias
            $portfolio = $this->getPortfolio($table, $alias);

            #-- if portfolio was found get child portfolios and pins
            if(is_array($portfolio) && count($portfolio) > 0){
                $portfolio = $portfolio[0];

                $this->Template->portfolios = $this->getPortfolios($table, $portfolio);
                $this->Template->pins = $this->getPins($table, $portfolio);

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
        $options = array(
            $table . '_portfolio.published = 1',
            $table . '_portfolio.pid = ' . $portfolio['pid'],
        );
        return BasePortfolioModel::findByTable($table, $options);
    }

    /**
     * get Portfolio
     * @param $table
     * @param $alias
     * @return array|null
     */
    private function getPortfolio($table, $alias)
    {
        $options = array(
            $table . '_portfolio.published = 1',
            $table . '_portfolio.alias = "' . $alias . '"',
        );
        return BasePortfolioModel::findByTable($table, $options);
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
        $options = array(
            $table . '_pin.published = 1',
            $table . '_pin.pid = ' . $portfolio['pid'],
        );
        return BasePinModel::findByTable($table, $options);
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
}