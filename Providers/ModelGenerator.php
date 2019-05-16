<?php

namespace App\Containers\Crud\Providers;

class ModelGenerator extends BaseGenerator
{
    /**
     * El nombre de la tabla en la base de datos.
     *
     * @var string
     */
    public $table_name;

    /**
     * La iformación dada por el usuario.
     *
     * @var object
     */
    public $request;

    /**
     * Crea nueva instancia de ModelGenerator.
     */
    public function __construct($request)
    {
        $this->connectionName = $request->get('connection_name') ? $request->get('connection_name') : config('database.default');
        $this->table_name = $request->get('table_name');
        $this->request = $request;
    }

    /**
     * Genera el archivo para el Modelo de la tabla.
     *
     * @return int|bool
     */
    public function run()
    {
        // no se ha creado la carpeta para los modelos?
        if (!file_exists($this->modelsDir())) {
            // entonces la creo
            mkdir($this->modelsDir(), 0755, true);
        }

        $modelFile = $this->modelsDir().'/'.$this->modelClassName().'.php';

        $content = view(
            $this->templatesDir().'.model',
            [
            'crud' => $this,
            'fields' => $this->advanceFields($this->request),
            'request' => $this->request,
            ]
        );

        file_put_contents($modelFile, $content) === false
            ? session()->push('error', 'Error generando el modelo')
            : session()->push('success', 'Modelo generado correctamente');

        return true;
    }

    /**
     * Devuelve la llave primaria para el modelo.
     *
     * @param stdClass $field
     *
     * @return string
     */
    public function getPrimaryKey($fields)
    {
        // el valor por defecto de la llave primaria
        $primary_key = 'id';

        foreach ($fields as $field) {
            if ($field->key == 'PRI') {
                $primary_key = $field->name;
            }
        }

        return $primary_key;
    }

    /**
     * Los campos a omitir.
     *
     * @return array
     */
    public function skippedFields()
    {
        return ['id', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * Obtiene los valores enum de la columna indicada en el parámetro $column.
     *
     * @param string $column El nombre de la columna
     *
     * @return string
     */
    public function getMysqlTableColumnEnumValues($column)
    {
        return \DB::connection($this->connectionName)->select(
            \DB::raw(
                "SHOW COLUMNS FROM {$this->getDatabaseTablesPrefix()}$this->table_name WHERE Field = '$column'"
            )
        )[0]->Type;
    }

    /**
     * Devuelve string del prefijo de las tablas de la base de datos.
     *
     * @return string El nombre del driver de la conexión a la base de datos
     */
    public function getDatabaseTablesPrefix()
    {
        return config('database.connections.'.$this->connectionName.'.prefix');
    }
}
