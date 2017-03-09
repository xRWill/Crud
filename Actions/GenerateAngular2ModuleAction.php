<?php

namespace App\Containers\Crud\Actions;

use Illuminate\Http\Request;

use App\Containers\Crud\Tasks\CreateAngular2DirsTask;
use App\Containers\Crud\Tasks\CreateNgModulesTask;
use App\Containers\Crud\Tasks\CreateNgContainersTask;
use App\Containers\Crud\Tasks\CreateNgComponentsTask;
use App\Containers\Crud\Tasks\CreateNgTranslationsTask;
use App\Containers\Crud\Tasks\CreateNgModelTask;
use App\Containers\Crud\Tasks\CreateNgActionsTask;
use App\Containers\Crud\Tasks\CreateNgReducerTask;
use App\Containers\Crud\Tasks\CreateNgEffectsTask;
use App\Containers\Crud\Tasks\CreateNgServiceTask;

/**
 * GenerateAngular2ModuleAction Class.
 *
 * @author Johan Alvarez <llstarscreamll@hotmail.com>
 */
class GenerateAngular2ModuleAction
{
    public function run(Request $request)
    {
        // generate the base folders
        $createAngular2DirsTask = new CreateAngular2DirsTask($request);
        $createAngular2DirsTask->run();

        // generate module and routing module
        $createNgModulesTask = new CreateNgModulesTask($request);
        $createNgModulesTask->run();

        // generate translations
        $createNgTranslationsTask = new CreateNgTranslationsTask($request);
        $createNgTranslationsTask->run();

        // generate containers
        $createNgContainersTask = new CreateNgContainersTask($request);
        $createNgContainersTask->run();

        // generate components
        $createNgComponentsTask = new CreateNgComponentsTask($request);
        $createNgComponentsTask->run();

        // generate model
        $createNgModelTask = new CreateNgModelTask($request);
        $createNgModelTask->run();

        // generate actions
        $createNgActionsTask = new CreateNgActionsTask($request);
        $createNgActionsTask->run();

        // generate reducer
        $createNgReducerTask = new CreateNgReducerTask($request);
        $createNgReducerTask->run();

        // generate effects
        $createNgEffectsTask = new CreateNgEffectsTask($request);
        $createNgEffectsTask->run();

        // generate service
        $createNgServiceTask = new CreateNgServiceTask($request);
        $createNgServiceTask->run();
    }
}
