<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\ProducteePortfolioModel;
use Home\LibrareeBundle\Resources\contao\models\ProducteePinModel;

class BaseListElement extends \ContentElement
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
        if (TL_MODE == 'BE') {
            $this->generateBackend();
        } else {
            $this->generateFrontend();
        }
    }

    /**
     * generate backend for module
     */
    private function generateBackend()
    {
        $this->strTemplate          = 'be_wildcard';
        $this->Template             = new \BackendTemplate($this->strTemplate);
        $this->Template->title      = $this->title;
        $this->Template->wildcard   = "### Liste ###";
    }

    /**
     * generate frontend for module
     */
    private function generateFrontend()
    {
        $portfolios = $this->findAllPortfolios();
        $pins = $this->findAllRelatedPins($portfolios);

        $this->Template->portfolios = $portfolios;
        $this->Template->pins = $pins;
    }

    /**
     * @return \Contao\Model\Collection|null
     */
    private function findAllPortfolios()
    {
        return ProducteePortfolioModel::findAll();
    }


    /**
     * find all related pins by pid
     * @param \Contao\Model\Collection $portfolios
     * @return bool|\Contao\Model\Collection|null|static
     */
    private function findAllRelatedPins($portfolios)
    {
        $pids = [];

        #-- get id of all portfolios
        if(isset($portfolios)){
            $models = $portfolios->getModels();
            foreach ($models as $model){
                $pids[] = $model->__get('id');
            }
        }

        if(count($pids) > 0){
            return ProducteePinModel::findBy(array(
                ProducteePinModel::getTable() . ".pid IN('" . implode("','", $pids) . "')"), null
            );

        }

        return false;
    }

}