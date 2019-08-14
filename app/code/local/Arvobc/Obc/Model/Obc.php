<?php

class Arvobc_Obc_Model_Obc extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('obc/obc');
    }
}