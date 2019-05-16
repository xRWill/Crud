<?php

namespace App\Containers\Crud\Tasks;

use Illuminate\Support\Collection;
use App\Containers\Crud\Traits\DataGenerator;
use App\Containers\Crud\Traits\AngularFolderNamesResolver;

/**
 * CreateNgServiceTask Class.
 *
 * @author Johan Alvarez <llstarscreamll@hotmail.com>
 */
class CreateNgServiceTask
{
    use DataGenerator, AngularFolderNamesResolver;

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
     * @var string
     */
    private $indexStrToreplace = "\nexport const SERVICES = [";

    /**
     * The parsed fields from request.
     *
     * @var Illuminate\Support\Collection
     */
    public $parsedFields;

    /**
     * Create new CreateNgServiceTask instance.
     *
     * @param Collection $request
     */
    public function __construct(Collection $request)
    {
        $this->request = $request;
        $this->container = studly_case($request->get('is_part_of_package'));
        $this->connectionName = $request->get('connection_name');
        $this->tableName = $this->request->get('table_name');
        $this->parsedFields = $this->parseFields($this->request);
    }

    /**
     * @return bool
     */
    public function run()
    {
        $serviceFile = $this->slugEntityName();

        $indexFilePath = $this->servicesDir().'/index.ts';
        $template = $this->templatesDir().'.Angular2/services/main-index';
        $className = $this->entityName().'Service';
        $fileName = "./$serviceFile.service";

        $this->setupIndexFile($indexFilePath, $template, $className, $fileName);

        $serviceFile = $this->servicesDir()."/$serviceFile.service.ts";
        $template = $this->templatesDir().'.Angular2/services/service';

        $content = view($template, [
            'crud' => $this,
            'fields' => $this->parsedFields
        ]);

        file_put_contents($serviceFile, $content) === false
            ? session()->push('error', "Error creating Angular Service file")
            : session()->push('success', "Angular Service creation success");

        return true;
    }
}
