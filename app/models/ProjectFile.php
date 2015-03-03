<?php

class ProjectFile extends AppModel
{

    public $errors;

    protected $table = 'project_files';
    protected $guarded = array('id');

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Project', 'project_id', 'id');
    }

    /**
     * @param $files
     * @param $filesList
     * @param $project_id
     */
    public function addFiles($files, $filesList, $project_id)
    {
        // add files to project_files
        foreach ($files as $key => $file) {
            if (!empty($filesList['name'][$key])) {
                $this->create([
                    'name' => $filesList['name'][$key],
                    'file' => $file,
                    'project_id' => $project_id
                ]);
            }
        }

    }
}
