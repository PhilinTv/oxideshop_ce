<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardsCompatibleInstanceOfNewClassRealClassName_2_Test extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Test the backwards compatibility of class instances created with oxNew and the alias class name
     */
    public function testForwardsCompatibleInstanceOfNewClassRealClassName()
    {
        if ('CE' !== $this->getConfig()->getEdition()) {
            //$this->markTestSkipped(
            //    'This test will fail on Travis and CI as it MUST run in an own PHP process, which is not possible.'
            //);
        }

        $realClassName = \OxidEsales\EshopCommunity\Application\Model\Article::class;
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $backwardsCompatibleClassAlias = 'oxarticle';

        $object = new $realClassName();

        $message = 'An object created with new \OxidEsales\EshopCommunity\Application\Model\Article() is not an instance of "oxarticle"';
        $this->assertNotInstanceOf($backwardsCompatibleClassAlias, $object, $message);

        $message = 'An object created with new \OxidEsales\EshopCommunity\Application\Model\Article() is an instance of \OxidEsales\EshopCommunity\Application\Model\Article::class';
        $this->assertInstanceOf($realClassName, $object, $message);

        $message = 'An object created with new \OxidEsales\EshopCommunity\Application\Model\Article() is not an instance of \OxidEsales\Eshop\Application\Model\Article::class';
        $this->assertNotInstanceOf($unifiedNamespaceClassName, $object, $message);
    }
}