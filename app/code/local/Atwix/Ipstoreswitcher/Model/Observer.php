<?php
/**
 * Atwix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Atwix Mod
 * @package     Atwix_Ipstoreswitcher
 * @author      Atwix Core Team
 * @copyright   Copyright (c) 2014 Atwix (http://www.atwix.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* app/code/local/Atwix/Ipstoreswitcher/Model/Observer.php */

class Atwix_Ipstoreswitcher_Model_Observer
{
    /**
     * redirects customer to store view based on GeoIP
     * @param $event
     */
    public function controllerActionPostdispatch($event)
    {
        $cookie = Mage::getSingleton('core/cookie');
        if ($cookie->get('geoip_processed') != 1) {
            $geoIPCountry = Mage::getSingleton('geoip/country');
            $countryCode = $geoIPCountry->getCountry();
            if ($countryCode) {
                $storeName = Mage::helper('atwix_ipstoreswitcher')->getStoreByCountry($countryCode);
                if ($storeName) {
                    $store = Mage::getModel('core/store')->load($storeName, 'name');
                    if ($store->getName() != Mage::app()->getStore()->getName()) {
                        $event->getControllerAction()->getResponse()->setRedirect($store->getCurrentUrl(false));
                    }
                }
            }
            $cookie->set('geoip_processed', '1', time() + 86400, '/');
        }
    }
}