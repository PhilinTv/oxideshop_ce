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
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: shopmainTest.php 38998 2011-10-03 14:55:28Z vilma $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for Shop_Main class
 */
class Unit_Admin_DeliverySetRDFaTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable( 'oxobject2delivery' );

        parent::tearDown();
    }

    /**
     * DeliverySet_RDFa::save() delete old records test case
     *
     * @return null
     */
    public function testSave_deleteOldRecords()
    {
        $sTestID = '_test_recid';
        modConfig::setParameter( 'oxid', $sTestID );

        $oMapping = oxNew('oxbase');
        $oMapping->init('oxobject2delivery');
        $oMapping->oxobject2delivery__oxdeliveryid = new oxField($sTestID);
        $oMapping->oxobject2delivery__oxobjectid = new oxField('test_del_objID');
        $oMapping->oxobject2delivery__oxtype = new oxField('rdfadeliveryset');
        $oMapping->save();

        $oDB = oxDb::getDb();

        $iExists = $oDB->GetOne(
            'SELECT 1 FROM oxobject2delivery WHERE oxdeliveryid = ? AND oxtype = ?'
            ,array($sTestID, 'rdfadeliveryset')
        );
        $this->assertFalse( empty($iExists) );

        $oView = oxNew('DeliverySet_RDFa');
        $oView->save();

        $iExists = $oDB->GetOne(
            'SELECT 1 FROM oxobject2delivery WHERE oxdeliveryid = ? AND oxtype = ?'
            ,array($sTestID, 'rdfadeliveryset')
        );
        $this->assertTrue( empty($iExists) );
    }

    /**
     * DeliverySet_RDFa::save() create records test case
     *
     * @return null
     */
    public function testSave_createRecords()
    {
        $sTestID = '_test_recid';
        $aObjIDs = array('_test_obj1', '_test_obj2');
        modConfig::setParameter( 'oxid', $sTestID );
        modConfig::setParameter( 'ardfadeliveries', $aObjIDs );
        modConfig::setParameter(
            'editval',
            array(
                'oxobject2delivery__oxdeliveryid' => $sTestID,
                'oxobject2delivery__oxtype' => 'rdfadeliveryset',
            )
        );

        $oDB = oxDb::getDb();

        $oView = oxNew('DeliverySet_RDFa');
        $oView->save();

        $aCurrObjIDs = $oDB->GetCol(
            'SELECT oxobjectid FROM oxobject2delivery WHERE oxdeliveryid = ? AND oxtype = ?'
            ,array($sTestID, 'rdfadeliveryset')
        );
        sort($aObjIDs);
        sort($aCurrObjIDs);
        $this->assertSame( $aObjIDs, $aCurrObjIDs );
    }

    /**
     * DeliverySet_RDFa::getAllRDFaDeliveries() test case
     *
     * @return null
     */
    public function testGetAllRDFaDeliveries()
    {
        $aAssignedRDFaDeliveries = array('DeliveryModeOwnFleet');
        $aExpResp = array();

        $oView = $this->getMock('DeliverySet_RDFa', array('getAssignedRDFaDeliveries'));
        $oView->expects( $this->once() )->method('getAssignedRDFaDeliveries')->will( $this->returnValue($aAssignedRDFaDeliveries) );
        $aCurrResp = $oView->getAllRDFaDeliveries();

        $this->assertTrue( is_array($aCurrResp), 'Array should be returned' );
        $this->assertTrue( count($aCurrResp) > 0, 'Empty array returned' );
        $this->assertTrue( current($aCurrResp) instanceof oxStdClass, 'Array elements should be of type oxStdClass' );

        $blFound = false;
        foreach ($aCurrResp as $oItem) {
            foreach ($aAssignedRDFaDeliveries as $sAssignedName) {
                if (strcasecmp($oItem->name, $sAssignedName) === 0) {
                    if ($oItem->checked !== true) {
                        $this->fail('Item "'.$sAssignedName.'" should be set as active');
                    }
                } else {
                    if ($oItem->checked === true) {
                        $this->fail('Item "'.$sAssignedName.'" should not be set as active');
                    }
                }
            }
        }
    }

    /**
     * DeliverySet_RDFa::getAssignedRDFaDeliveries() test case
     *
     * @return null
     */
    public function testGetAssignedRDFaDeliveries()
    {
        $sTestID = '_test_recid';
        $aObjIDs = array('_test_obj1', '_test_obj2');
        modConfig::setParameter( 'oxid', $sTestID );
        $oView = oxNew('DeliverySet_RDFa');

        $oDB = oxDb::getDb();
        $oDB->Execute('DELETE FROM oxobject2delivery WHERE oxdeliveryid = ? AND oxtype = ?', array($sTestID, 'rdfadeliveryset'));
        $this->assertSame(array(), $oView->getAssignedRDFaDeliveries(), 'Should be empty array');

        foreach ($aObjIDs as $sObjID) {
            $oMapping = oxNew('oxbase');
            $oMapping->init('oxobject2delivery');
            $oMapping->oxobject2delivery__oxdeliveryid = new oxField($sTestID);
            $oMapping->oxobject2delivery__oxobjectid = new oxField($sObjID);
            $oMapping->oxobject2delivery__oxtype = new oxField('rdfadeliveryset');
            $oMapping->save();
        }

        $aResp = $oView->getAssignedRDFaDeliveries();
        sort($aObjIDs);
        sort($aResp);
        $this->assertSame($aObjIDs, $aResp);
    }
}
