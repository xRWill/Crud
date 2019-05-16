<?php

namespace App\Containers\Crud\Tasks;

use Illuminate\Support\Collection;
use App\Containers\Crud\Traits\DataGenerator;
use App\Containers\Crud\Traits\AngularFolderNamesResolver;

/**
 * CopyAngularModuleTask Class.
 *
 * @author Johan Alvarez <llstarscreamll@hotmail.com>
 */
class CopyAngularModuleTask
{
    use AngularFolderNamesResolver;
    use DataGenerator;

    /**
     * Container name to generate.
     *
     * @var string
     */
    public $container;

    /**
     * Container entity to generate (database table name).
     *
     * @var string
     */
    public $tableName;

    /**
     * Create new CopyAngularModuleTask instance.
     *
     * @param Collection $request
     */
    public function __construct(Collection $request)
    {
        $this->request = $request;
        $this->container = studly_case($request->get('is_part_of_package'));
        $this->connectionName = $request->get('connection_name');
        $this->tableName = $this->request->get('table_name');
    }

    public function run(string $targetDir)
    {
        $sourceDir = $this->moduleDir();
        $targetDir = rtrim($targetDir, '/');

        if (!empty($targetDir)) {
            exec("mkdir -p $targetDir");
            exec("cp -rf $sourceDir $targetDir");
        }
    }
}
