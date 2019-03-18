<?php
/**
 * Created by PhpStorm.
 * User: fb
 * Date: 18.03.2019
 * Time: 12:53
 */

namespace Home\LibrareeBundle\Resources\contao\elements;


use Home\LibrareeBundle\Resources\contao\models\BasePinModel;

class SelectPinElement extends \Contao\ContentElement
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
        $this->Template->wildcard   = "### Pin Auswahl ###";
    }

    /**
     * generate frontend for module
     */
    private function generateFrontend()
    {
        #-- overwrite default templates
        if ($this->hm_template) {
            $this->strTemplate = $this->hm_template;
        }

        #-- get pin
        if($this->lib_table && $this->lib_pin){
            $pin = BasePinModel::findByTable($this->lib_nav_table, 'id', $this->lib_pin);
            $this->Template->pin = $pin;
        }
    }
}