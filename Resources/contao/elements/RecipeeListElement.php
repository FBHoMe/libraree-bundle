<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\RecipeePortfolioModel;
use Home\LibrareeBundle\Resources\contao\models\RecipeePinModel;

class RecipeeListElement extends BaseListElement
{

    /**
     * @var string
     */
    protected $strTemplate = 'cte_recipee_list';

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

        if (TL_MODE == 'BE') {

        } else {
            $this->generateFrontend();
        }
    }

    /**
     * generate frontend for module
     */
    private function generateFrontend()
    {
        $portfolios = $this->findPortfolioById(1);
        $pins = $this->findAllRelatedPins($portfolios);

        $this->Template->portfolios = $portfolios;
        $this->Template->pins = $pins;
    }


    private function findPortfolioById($id)
    {
        return RecipeePortfolioModel::findByIdOrAlias($id, array('return'=>'Collection'));
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
            return RecipeePinModel::findBy(array(
                RecipeePinModel::getTable() . ".pid IN('" . implode("','", $pids) . "')"), null
            );

        }

        return false;
    }

}