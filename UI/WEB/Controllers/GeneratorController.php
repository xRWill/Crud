<?php

namespace App\Containers\Crud\UI\WEB\Controllers;

use App\Containers\Crud\Actions\GenerateAngular2ModuleAction;
use App\Containers\Crud\Actions\GenerateConfigFileAction;
use App\Containers\Crud\Actions\GeneratePortoContainerAction;
use App\Containers\Crud\Actions\GenerateStandardLaravelApp;
use App\Containers\Crud\Actions\LoadOptionsAction;
use App\Containers\Crud\Providers\ModelGenerator;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GeneratorController extends WebController
{
    /**
     * Generate Laravel Packages Action.
     *
     * @var App\Containers\Crud\Actions\GeneratePortoContainerAction
     */
    private $generatePortoContainerAction;

    /**
     * Generate Standard Laravel App Action.
     *
     * @var App\Containers\Crud\Actions\GenerateStandardLaravelApp
     */
    private $generateStandardLaravelApp;

    /**
     * Generate Angular 2 Module Action.
     *
     * @var App\Containers\Crud\Actions\GenerateAngular2ModuleAction
     */
    private $generateAngular2ModuleAction;

    /**
     * Generate Config File Action.
     *
     * @var App\Containers\Crud\Actions\GenerateConfigFileAction
     */
    private $generateConfigFileAction;

    public $connectionName;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        GeneratePortoContainerAction $generatePortoContainerAction,
        GenerateStandardLaravelApp $generateStandardLaravelApp,
        GenerateAngular2ModuleAction $generateAngular2ModuleAction,
        GenerateConfigFileAction $generateConfigFileAction,
        Request $request = null
    ) {
        $this->generatePortoContainerAction = $generatePortoContainerAction;
        $this->generateStandardLaravelApp = $generateStandardLaravelApp;
        $this->generateAngular2ModuleAction = $generateAngular2ModuleAction;
        $this->generateConfigFileAction = $generateConfigFileAction;

      $this->connectionName = $request->get('connection_name') ? $request->get('connection_name') : config('database.default');
    }

    /**
     * Muestra el formulario donde se debe dar la tabla de la base de datos a la
     * cual crearemos la CRUD app.
     *
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['connection_name'] = $this->connectionName;
        $data['connections'] = array_keys(config('database.connections'));
        $data['tables'] = DB::connection($request->get('connection_name'))->getDoctrineSchemaManager()->listTableNames();
        $data['config_files'] = [];

        $savedConfigsPath =  storage_path('app/crud/options/');

        if (File::isDirectory($savedConfigsPath)) {
            $files = File::allFiles($savedConfigsPath);
            $files = array_sort($files, function($file) { return $file->getFilename(); });
            foreach ($files as $file) {
                $data['config_files'][] = str_replace('.php', '', $file->getFileName());
            }
        }

        return view('crud::wizard.index', $data);
    }

    public function generateMany(Request $request)
    {
        foreach($request->get('config') as $config) {
            $savedConfigsPath = storage_path('app/crud/options/');
            $data = require_once $savedConfigsPath . $config . ".php";

            $data = collect($request->except('_token') + $data);

            if ($request->get('generate_porto_container', false)) {
                $this->generatePortoContainerAction->run($data);
            }

            if ($request->get('generate_angular_module', false)) {
                $this->generateAngular2ModuleAction->run($data);
            }
        }

        return redirect()->back()->withInput();
    }

    /**
     * Ejecuta los scripts para generar los ficheros necesarios para la CRUD app
     * de la tabla de la base de datos elegida.
     *
     * @return Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        // store the given data for remember this settings in future
        $this->generateConfigFileAction->run($request->except(['_token']));

        // check if the given table exists
        if (!$this->tableExists($request->get('table_name'))) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    "The table ".$request->get('table_name')." doesn't exists on ". $this->connectionName ." connection!!"
                );
        }

        $data = collect($request->except('_token'));

        if ($request->get('generate_porto_container', false)) {
            $this->generatePortoContainerAction->run($data);
        }

        if ($request->get('generate_angular_module', false) && !empty($request->get('angular_module_location'))) {
            $this->generateAngular2ModuleAction->run($data);
        }

        if ((!$request->get('generate_angular_module', false) || empty($request->get('angular_module_location'))) && !$request->get('generate_porto_container', false)) {
            session()->flash('warning', 'Nothing to generate...');
            empty($request->get('angular_module_location')) && $request->get('generate_angular_module', false)
                ? session()->flash('error', "Angular module location can't be empty")
                : null;
        }

        // go to the CRUD settings page
        return redirect()->route(
            'crud.showOptions',
            ['connection_name' => $this->connectionName, 'table_name' => $request->get('table_name')]
        );
    }

    /**
     * Comprueba si existe o no una tabla en la base de datos.
     *
     * @return bool
     */
    private function tableExists($table)
    {
        return \Schema::connection($this->connectionName)->hasTable($table);
    }

    /**
     * Muestra formulario con las opciones de la CRUD app a generar.
     *
     * @return view
     */
    public function showOptions(Request $request)
    {
        // verifico que la tabla especificada existe en la base de datos
        if (!$this->tableExists($request->get('table_name', 'null'))) {
            return redirect()
                ->back()
                ->with('error',
                  "The table ".$request->get('table_name')." doesn't exists on ". $this->connectionName ." connection!!");
        }

        // try to retrieve the last given CRUD config options for this table
        $loadOptionsAction = new LoadOptionsAction();
        $data['options'] = $loadOptionsAction->run($request->get('table_name'));

        $modelGenerator = new ModelGenerator($request);

        $data['fields'] = $modelGenerator->fields($request->get('table_name'));
        $data['connection_name'] = $this->connectionName;
        $data['table_name'] = $request->get('table_name');
        $data['connections'] = array_keys(config('database.connections'));
        $data['tables'] = DB::connection($this->connectionName)->getDoctrineSchemaManager()->listTableNames();
        $data['UI_themes'] = [];

        return view('crud::wizard.options', $data);
    }
}
