<?php
namespace NifaAppsManager\Traits;

use Cake\ORM\TableRegistry;

trait ApplicationDetailsTrait {

    public function getSystemApp($system_designator) {

        $table = TableRegistry::get('NifaAppsManager.Applications');

        $application = $table->findBySystemDesignator($system_designator)->first();
        //$application->client_url = urldecode($application->client_url);

        return $application;
    }

}