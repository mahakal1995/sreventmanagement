<?php
// kartik new created for diu
/**
 * ProjectDiuTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ProjectDiuTable extends PluginProjectDiuTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object ProjectActivityTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('ProjectDiu');
    }
}