<?php

/**
 * BaseProject
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $projectId
 * @property integer $customerId
 * @property integer $is_deleted
 * @property string $name
 * @property string $description
 * @property Customer $Customer
 * @property Doctrine_Collection $ProjectActivity
 * @property Doctrine_Collection $ProjectAdmin
 * @property Doctrine_Collection $TimesheetItem
 * 
 * @method integer             getProjectId()       Returns the current record's "projectId" value
 * @method integer             getCustomerId()      Returns the current record's "customerId" value
 * @method integer             getIsDeleted()       Returns the current record's "is_deleted" value
 * @method string              getName()            Returns the current record's "name" value
 * @method string              getDescription()     Returns the current record's "description" value
 * @method Customer            getCustomer()        Returns the current record's "Customer" value
 * @method Doctrine_Collection getProjectActivity() Returns the current record's "ProjectActivity" collection
 * @method Doctrine_Collection getProjectAdmin()    Returns the current record's "ProjectAdmin" collection
 * @method Doctrine_Collection getTimesheetItem()   Returns the current record's "TimesheetItem" collection
 * @method Project             setProjectId()       Sets the current record's "projectId" value
 * @method Project             setCustomerId()      Sets the current record's "customerId" value
 * @method Project             setIsDeleted()       Sets the current record's "is_deleted" value
 * @method Project             setName()            Sets the current record's "name" value
 * @method Project             setDescription()     Sets the current record's "description" value
 * @method Project             setCustomer()        Sets the current record's "Customer" value
 * @method Project             setProjectActivity() Sets the current record's "ProjectActivity" collection
 * @method Project             setProjectAdmin()    Sets the current record's "ProjectAdmin" collection
 * @method Project             setTimesheetItem()   Sets the current record's "TimesheetItem" collection
 * 
 * @package    orangehrm
 * @subpackage model\admin\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseProject extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_project');
        $this->hasColumn('project_id as projectId', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('customer_id as customerId', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('is_deleted', 'integer', 1, array(
             'type' => 'integer',
             'default' => '0',
             'length' => 1,
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             ));
        $this->hasColumn('description', 'string', 256, array(
             'type' => 'string',
             'length' => 256,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Customer', array(
             'local' => 'customer_id',
             'foreign' => 'customer_id'));

        $this->hasMany('ProjectActivity', array(
             'local' => 'project_id',
             'foreign' => 'project_id'));
        
// kartik new created for diu        
        $this->hasMany('ProjectDiu', array(
             'local' => 'project_id',
             'foreign' => 'project_id'));
// kartik new created for diu        

        $this->hasMany('ProjectAdmin', array(
             'local' => 'project_id',
             'foreign' => 'project_id'));

        $this->hasMany('TimesheetItem', array(
             'local' => 'project_id',
             'foreign' => 'projectId'));
    }
}