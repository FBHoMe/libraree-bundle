<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

class BaseDetailElement extends \ContentElement
{
    /**
     * @var string
     */
    protected $strTemplate = 'cte_detail';

    /**
     * @return string
     */
    public function generate()
    {
        return parent::generate();
    }

    /**
     * generate module
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
        $this->Template->title      = $this->headline;
        $this->Template->wildcard   = "### Detailansicht ###";
    }

    /**
     * generate frontend for module
     */
    private function generateFrontend()
    {
        if($_GET['id']){
            $pin = $this->getById($_GET['id']);
            $this->Template->pin = $pin;
        }
    }

    /**
     * get record by its id or alias
     * @param $id
     * @return object
     */
    private function getById($id)
    {
        return \Model::findByIdOrAlias($id);
    }

}