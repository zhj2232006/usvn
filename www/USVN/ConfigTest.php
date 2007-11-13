<?php
/**
 * Model for configuration pages
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage config
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

// Call USVN_ConfigTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_ConfigTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

define('USVN_CONFIG_FILE', 'tests/config.ini');
define('USVN_CONFIG_SECTION', 'general');

/**
 * Test class for USVN_Config.
 * Generated by PHPUnit_Util_Skeleton on 2007-03-26 at 17:42:45.
 */
class USVN_ConfigTest extends USVN_Test_Test {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_ConfigTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function setUp()
	{
		parent::setUp();
		file_put_contents(USVN_CONFIG_FILE, "
		[general]
translation.locale = \"en_US\"
version = \"0.84\"
update.checkforupdate = \"1\";
		");
	}

    public function testSetLanguage()
	{
		USVN_Config::setLanguage('fr_FR');
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$this->assertEquals('fr_FR', $config->translation->locale);
    }

    public function testSetTimezone()
	{
		USVN_Config::setTimezone('Europe/Paris');
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$this->assertEquals('Europe/Paris', $config->timezone);
    }

    public function testSetCheckForUpdate()
	{
		USVN_Config::setCheckForUpdate(true);
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$this->assertEquals(1, $config->update->checkforupdate);
		$this->assertEquals(0, $config->update->lastcheckforupdate);
	}

    public function testUnset()
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		unset($config->translation->locale);
		unset($config->version);
		$this->assertFalse(isset($config->translation->locale), "Ca serait pas le patch sur le framework pour supprimer un champ par hasard ?");
		$this->assertFalse(isset($config->version));
    }


    public function testSetLanguageInvalid()
	{
		$ok = false;
		try
		{
			USVN_Config::setLanguage('tutu');
		}
		catch (Exception $e)
		{
			$ok = true;
		}
		$this->assertTrue($ok);
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$this->assertEquals($config->translation->locale, 'en_US');
    }
}

// Call USVN_ConfigTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_ConfigTest::main") {
    USVN_ConfigTest::main();
}
?>
