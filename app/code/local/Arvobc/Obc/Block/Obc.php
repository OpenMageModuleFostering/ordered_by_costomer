<?php
class Arvobc_Obc_Block_Obc extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getObc()     
     { 
        if (!$this->hasData('obc')) {
            $this->setData('obc', Mage::registry('obc'));
        }
        return $this->getData('obc');
        
    }

	/*
	 * Get all unique order IDs for items with a particular ID.
	 */
	public function getOrderIds() {
		if (!Mage::registry('current_product')) {
			return;
		}
		$product_id = Mage::registry('current_product')->getId();
		$orderItems = Mage::getResourceModel('sales/order_item_collection')
					->addFieldToFilter('product_id', $product_id)
					->toArray(array('order_id'))
					;
		$orderIds = array_unique(array_map(
			function($orderItem) {
				return $orderItem['order_id'];
			},
			$orderItems['items']
		));
		return $orderItems;
	}

	/*
	 * Now get all unique customers from the orders of these items.
	 */
	public function getOrderCollection($orderIds) {
		$configs = $this->getObcConfigs();

		$orderCollection = Mage::getResourceModel('sales/order_collection')
			->addAttributeToSort('created_at', 'DESC')
			->addFieldToFilter('entity_id',   array('in'  => $orderIds))
			->addFieldToFilter('customer_id', array('neq' => 'NULL'))
			;
		$orderCollection->setPageSize($configs['obc_records'])->getSelect()->group('customer_id');
		return $orderCollection;
	}

	public function getObcConfigs() {
		$configs = array();
		$configs['obc_enabled'] = Mage::getStoreConfig('obcconfig/obc_group/obc_enabled');
		$configs['obc_records'] = Mage::getStoreConfig('obcconfig/obc_group/obc_records');
		$configs['obc_showaddress'] = Mage::getStoreConfig('obcconfig/obc_group/obc_showaddress');
		$configs['obc_showdate'] = Mage::getStoreConfig('obcconfig/obc_group/obc_showdate');
		$configs['obc_position'] = Mage::getStoreConfig('obcconfig/obc_group/obc_position');
		$configs['obc_position_before'] = Mage::getStoreConfig('obcconfig/obc_group/obc_position_before');
		$configs['obc_position_after'] = Mage::getStoreConfig('obcconfig/obc_group/obc_position_after');
		return $configs;
	}

	public function format_interval($first_date, $second_date='') {
		$second_date = date('Y-m-d H:i:s');
		$first_date = new DateTime($first_date);
		$second_date = new DateTime($second_date);

		$interval = $first_date->diff($second_date);

		$result = "";
		if ($interval->y) {
			$result .= $interval->format("%y year");
		} else {
			if ($interval->m) {
				$result .= $interval->format("%m month");
			} else {
				if ($interval->d) {
					$result .= $interval->format("%d day");
				} else {
					if ($interval->h) {
						$result .= $interval->format("%h hour");
					} else {
						if ($interval->i) {
							$result .= $interval->format("%i minute");
						} else {
							if ($interval->s) {
								$result .= $interval->format("%s second");
							}
						}
					}
				}
			}
		}
		if (intval($result) > 1) {
			$result .= 's';
		}
		$result .= ' ago';
		return $result;
	}

}