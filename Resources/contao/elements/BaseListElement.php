<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\BasePinModel;

class BaseListElement extends \Contao\ContentElement
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
            #-- overwrite default templates
            if ($this->hm_template) {
                $this->Template = new \Contao\FrontendTemplate($this->hm_template);
            }

            #-- check if pin order is set
            if($GLOBALS['libraree']['pinOrder'] === null){
                $GLOBALS['libraree']['pinOrder'] = 'id DESC';
            }

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
        #-- get pins by portfolio id or all pins
        if($this->lib_pid){
            $pins = $this->getPinsByPid($this->lib_table, $this->lib_pid);
        }else{
            $pins = $this->getPins($this->lib_table);
        }

        $this->Template->pins = $pins;
    }

    /**
     * get pins by portfolio id
     *
     * @param $table
     * @param $pid
     * @return array|null
     */
    private function getPinsByPid($table, $pid)
    {
        $strColumn = array(
            $table . '_pin.published = 1',
            $table . '_pin.pid = ?'
        );
        $options = array('order'=>$GLOBALS['libraree']['pinOrder']);

        return BasePinModel::findByTable($table, $strColumn, $pid, $options);
    }

    /**
     * get all pins
     *
     * @param $table
     * @return array|null
     */
    private function getPins($table)
    {
        $strColumn = array(
            $table . '_pin.published = 1',
        );
        $options = array('order'=>$GLOBALS['libraree']['pinOrder']);

        return BasePinModel::findByTable($table, $strColumn, null, $options);
    }
}