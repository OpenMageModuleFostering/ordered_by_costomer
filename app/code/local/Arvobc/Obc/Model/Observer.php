<?php
class Arvobc_Obc_Model_Observer
{
    public function add_obc_block($observer)
	{
		$configs = Arvobc_Obc_Block_Obc::getObcConfigs();

		$orderIds = Arvobc_Obc_Block_Obc::getOrderIds();
		if ($configs['obc_enabled'] && $orderIds['totalRecords']) {
			$action = $observer->getEvent()->getAction();
			$fullActionName = $action->getFullActionName();
			$position = (isset($configs['obc_position'])) ? ($configs['obc_position']) : 'right';
			$sub_position = 'before="cart_sidebar"';
			if (isset($configs['obc_position_before']) && !empty($configs['obc_position_before'])) {
				$sub_position = 'before="'.$configs['obc_position_before'].'"';
			}
			if (isset($configs['obc_position_after']) && !empty($configs['obc_position_after'])) {
				$sub_position = 'after="'.$configs['obc_position_after'].'"';
			}

			$myXml = '<reference name="'.$position.'"><block type="obc/obc" name="obc" template="obc/obc.phtml" '.$sub_position.' /></reference>';
			$layout = $observer->getEvent()->getLayout();
			if ($fullActionName=='catalog_product_view') {
				$layout->getUpdate()->addUpdate($myXml);
				$layout->generateXml();
			}
		}
	}
}