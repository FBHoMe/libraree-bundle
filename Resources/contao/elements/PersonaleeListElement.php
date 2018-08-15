<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\PersonaleePinModel;

class PersonaleeListElement extends \ContentElement
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
            $this->strTemplate          = 'be_wildcard';
            $this->Template             = new \BackendTemplate($this->strTemplate);
            $this->Template->wildcard   = "### Personen Liste ###";
        } else {

            $promoter = $this->findAll();
            $this->Template->promoter = $promoter;
        }
    }

    /**
     * @return \Contao\Model\Collection|null
     */
    private function findAll()
    {
        return PersonaleePinModel::findAll();
    }

}