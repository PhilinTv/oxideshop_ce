<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

/**
 * Price list class. Deals with a list of oxPrice object.
 * The main reason why we can't just sum oxPrice objects is that they have different VAT percents.
 * @package core
 */

class oxPriceList
{
    /**
     * Array containing oxPrice objects
     *
     * @var array
     */
    protected $_aList = array();

   /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null;
     */
    public function __construct()
    {
    }

    /**
     * Returns Brutto price sum
     *
     * @return double
     */
    public function getBruttoSum()
    {
        $dSum = 0;
        foreach ( $this->_aList as $oPrice ) {
            $dSum += $oPrice->getBruttoPrice();
        }

        return $dSum;
    }

    /**
     * Returns the sum of list Netto prices
     *
     * @return double
     */
    public function getNettoSum()
    {
        $dSum = 0;
        foreach ( $this->_aList as $oPrice ) {
            $dSum += $oPrice->getNettoPrice();
        }

        return $dSum;
    }

    /**
     * Returns VAT values sum separated to different array elements depending on VAT
     *
     * @return array
     */
    public function getVatInfo()
    {
        $oLang = oxLang::getInstance();
        $aVatValues = array();
        foreach ( $this->_aList as $oPrice ) {
            $sVatKey = ( string ) $oLang->formatVat( $oPrice->getVat() );
            if ( !isset( $aVatValues[$sVatKey] )) {
                $aVatValues[$sVatKey] = 0;
            }
            $aVatValues[$sVatKey] += $oPrice->getVATValue();
        }

        return $aVatValues;
    }

    /**
     * Return prices separated to different array elements depending on VAT
     *
     * @return array
     */
    public function getPriceInfo()
    {
        $aPrices = array();
        foreach ( $this->_aList as $oPrice ) {
            $sVat = ( string ) $oPrice->getVat();
            if ( !isset( $aPrices[$sVat] )) {
                $aPrices[$sVat] = 0;
            }
            $aPrices[$sVat] += $oPrice->getBruttoPrice();
        }

        return $aPrices;
    }

    /**
     * Iterates through applied VATs and fetches VAT for delivery.
     * If not VAT was applied - default VAT (myConfig->dDefaultVAT) will be used
     *
     * @return double
     */
    public function getMostUsedVatPercent()
    {
        $aPrices = $this->getPriceInfo();
        if ( count( $aPrices ) == 0 ) {
            return;
        }

        return array_search( max( $aPrices ), $aPrices );
    }

    /**
     * Add an oxPrice object to prices array
     *
     * @param oxprice $oPrice oxprice object
     *
     * @return null
     */
    public function addToPriceList( $oPrice )
    {
        $this->_aList[] = $oPrice;
    }
}
