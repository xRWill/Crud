<?php

namespace Crud;

use Crud\Page\Functional\Generate as Page;

class TestsCest
{
    public function _before(FunctionalTester $I)
    {
        new Page($I);
        $I->amLoggedAs(Page::$adminUser);

        $I->amOnPage(Page::route('?table_name='.Page::$tableName));
        $I->see(Page::$title, Page::$titleElem);

        // envío el formulario de creación del CRUD
        $I->submitForm('form[name=CRUD-form]', Page::$formData);
    }

    public function _after(FunctionalTester $I)
    {
    }

    /**
     * Comprueba las líneas de código generadas en las vistas del CRUD.
     *
     * @param FunctionalTester $I
     */
    public function checkTestsCode(FunctionalTester $I)
    {
        $I->wantTo('revisar los test generados');

        // abro el test Create
        $I->openFile(base_path().'/tests/functional/Books/CreateCest.php');
        $test = file_get_contents(__DIR__.'/../_data/functionalTests/CreateCest.php');
        $I->seeInThisFile($test);

        // abro el test Destroy
        $I->openFile(base_path().'/tests/functional/Books/DestroyCest.php');
        $test = file_get_contents(__DIR__.'/../_data/functionalTests/DestroyCest.php');
        $I->seeInThisFile($test);

        // abro el test Edit
        $I->openFile(base_path().'/tests/functional/Books/EditCest.php');
        $test = file_get_contents(__DIR__.'/../_data/functionalTests/EditCest.php');
        $I->seeInThisFile($test);

        // abro el test Index
        $I->openFile(base_path().'/tests/functional/Books/IndexCest.php');
        $test = file_get_contents(__DIR__.'/../_data/functionalTests/IndexCest.php');
        $I->seeInThisFile($test);

        // abro el test Permissions
        $I->openFile(base_path().'/tests/functional/Books/PermissionsCest.php');
        $test = file_get_contents(__DIR__.'/../_data/functionalTests/PermissionsCest.php');
        $I->seeInThisFile($test);

        // abro el test Show
        $I->openFile(base_path().'/tests/functional/Books/ShowCest.php');
        $test = file_get_contents(__DIR__.'/../_data/functionalTests/ShowCest.php');
        $I->seeInThisFile($test);
    }
}