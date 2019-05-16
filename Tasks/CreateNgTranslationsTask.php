<?php

namespace App\Containers\Crud\Tasks;

use Illuminate\Support\Collection;
use App\Containers\Crud\Traits\DataGenerator;
use App\Containers\Crud\Traits\AngularFolderNamesResolver;

/**
 * CreateNgTranslationsTask Class.
 *
 * @author Johan Alvarez <llstarscreamll@hotmail.com>
 */
class CreateNgTranslationsTask
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
     * The modules files to generate.
     *
     * @var array
     */
    public $files = [
        'trans',
    ];

    /**
     * @var string
     */
    private $indexStrToreplace = "\nexport const ES = {";

    /**
     * @var string
     */
    private $indexClassTemplate = "...:class";

    /**
     * The parsed fields from request.
     *
     * @var Illuminate\Support\Collection
     */
    public $parsedFields;

    /**
     * Create new CreateNgTranslationsTask instance.
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
        $indexFilePath = $this->translationsDir().'/index.ts';
        $template = $this->templatesDir().'.Angular2.translations.main-index';
        $className = $this->entityNameSnakeCase();
        $fileName = './'.$this->slugEntityName();

        $this->setupIndexFile($indexFilePath, $template, $className, $fileName);

        foreach ($this->files as $file) {
            $transFile = $this->translationsDir()."/{$this->slugEntityName()}.ts";
            $template = $this->templatesDir().'.Angular2.translations.'.$file;

            $content = view($template, [
                'crud' => $this,
                'fields' => $this->parsedFields
            ]);

            file_put_contents($transFile, $content) === false
                ? session()->push('error', "Error creating $file translation file")
                : session()->push('success', "$file translation creation success");
        }

        return true;
    }
}
