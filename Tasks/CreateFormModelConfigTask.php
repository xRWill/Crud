<?php

namespace App\Containers\Crud\Tasks;

use Illuminate\Support\Collection;
use App\Containers\Crud\Traits\FolderNamesResolver;
use App\Containers\Crud\Traits\DataGenerator;

/**
 * CreateFormModelConfigTask Class.
 *
 * @author Johan Alvarez <llstarscreamll@hotmail.com>
 */
class CreateFormModelConfigTask
{
    use FolderNamesResolver;
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
     * The parsed fields from request.
     *
     * @var Illuminate\Support\Collection
     */
    public $parsedFields;

    /**
     * Create new CreateFormModelConfigTask instance.
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
        $file = $this->slugEntityName();

        $factoryFile = $this->configsFolder()."/{$file}-form-model.php";
        $template = $this->templatesDir().'.Porto.Configs.form-model';

        $content = view($template, [
            'crud' => $this,
            'fields' => $this->parsedFields
        ]);

        file_put_contents($factoryFile, $content) === false
            ? session()->push('error', "Error creating form model config file")
            : session()->push('success', "Form model config creation success");

        return true;
    }
}
