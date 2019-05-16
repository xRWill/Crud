<?php

namespace App\Containers\Crud\Tasks;

use Illuminate\Support\Collection;
use App\Containers\Crud\Traits\DataGenerator;
use App\Containers\Crud\Traits\AngularFolderNamesResolver;

/**
 * CreateNgEffectsTask Class.
 *
 * @author Johan Alvarez <llstarscreamll@hotmail.com>
 */
class CreateNgEffectsTask
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
    private $indexStrToreplace = "\nexport const EFFECTS = [";

    /**
     * @var string
     */
    private $indexClassTemplate = "EffectsModule.run(:class)";

    /**
     * The parsed fields from request.
     *
     * @var Illuminate\Support\Collection
     */
    public $parsedFields;

    /**
     * Create new CreateNgEffectsTask instance.
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

        $this->effectFile = $this->slugEntityName();
    }

    /**
     * @return bool
     */
    public function run()
    {
        $indexFilePath = $this->effectsDir().'/index.ts';
        $template = $this->templatesDir().'.Angular2/effects/main-index';
        $className = $this->entityName().'Effects';
        $fileName = './'.$this->effectFile.'.effects';

        $this->setupIndexFile($indexFilePath, $template, $className, $fileName);

        $this->effectFile = $this->effectsDir()."/$this->effectFile.effects.ts";
        $template = $this->templatesDir().'.Angular2/effects/effects';

        $content = view($template, [
            'crud' => $this,
            'fields' => $this->parsedFields
        ]);

        file_put_contents($this->effectFile, $content) === false
            ? session()->push('error', "Error creating Angular Effects file")
            : session()->push('success', "Angular Effects creation success");

        return true;
    }
}
