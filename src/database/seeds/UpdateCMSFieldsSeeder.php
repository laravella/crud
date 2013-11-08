<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class UpdateCMSFields extends CrudSeeder {

    public function run()
    {
        $this->setWidgetType('contents', 'content', 'ckeditor');
        $this->setWidgetType('contents', 'excerpt', 'textarea');
        $this->setWidgetType('medias', 'thumbnail', 'thumbnail');
    }

}

?>