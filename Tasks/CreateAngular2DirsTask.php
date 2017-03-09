<?php

namespace App\Containers\Crud\Tasks;

use Illuminate\Http\Request;
use App\Containers\Crud\Traits\DataGenerator;
use App\Containers\Crud\Traits\AngularFolderNamesResolver;

/**
* CreateAngular2DirsTask Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class CreateAngular2DirsTask
{
    use DataGenerator, AngularFolderNamesResolver;

    /**
     * Container name to generate.
     *
     * @var string
     */
    private $container = '';

    /**
     * Create new CreateModelAction instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->container = studly_case($request->get('is_part_of_package'));
        $this->tableName = $this->request->get('table_name');
    }

    /**
     * @param  string $container Contaner name.
     * @return bool
     */
    public function run()
    {
        // generate angular folder
        if (!file_exists($this->angularDir())) {
            mkdir($this->angularDir());
        }

        // generate angular module folder
        if (!file_exists($this->moduleDir())) {
            mkdir($this->moduleDir());
        }

        // generate angular translations folder
        if (!file_exists($this->translationsDir())) {
            mkdir($this->translationsDir());
        }

        // generate angular components folder
        if (!file_exists($this->componentsDir())) {
            mkdir($this->componentsDir());
        }

        // generate angular containers folder
        if (!file_exists($this->containersDir())) {
            mkdir($this->containersDir());
        }

        // generate angular models folder
        if (!file_exists($this->modelsDir())) {
            mkdir($this->modelsDir());
        }

        // generate angular reducers folder
        if (!file_exists($this->actionsDir())) {
            mkdir($this->actionsDir());
        }

        // generate angular reducers folder
        if (!file_exists($this->reducersDir())) {
            mkdir($this->reducersDir());
        }

        // generate angular effects folder
        if (!file_exists($this->effectsDir())) {
            mkdir($this->effectsDir());
        }

        // generate angular services folder
        if (!file_exists($this->servicesDir())) {
            mkdir($this->servicesDir());
        }

        return true;
    }
}
