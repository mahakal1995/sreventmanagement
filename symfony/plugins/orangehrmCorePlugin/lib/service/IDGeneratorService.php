<?php

class IDGeneratorService extends BaseService {
    const MIN_LENGTH = 3;

    private $entity;

    /**
     * Constructor of Generator Service class
     * @return unknown_type
     */
    public function __construct() {
        
    }

    /**
     * Get the entity
     * @return unknown_type
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     * Set the entity
     * @param $entity
     * @return unknown_type
     */
    public function setEntity($entity) {
        $this->entity = $entity;
    }

    /**
     * get the prefix
     * @return unknown_type
     */
    private function getEntityPrefix() {
        $prefix = '';

        switch (get_class($this->entity)) {
            case 'Location':
                $prefix = 'LOC';
                break;

            case 'JobCategory':
                $prefix = 'EEC';
                break;

            case 'CompanyProperty':
                $prefix = 'EEC';
                break;

            case 'SalaryGrade':
                $prefix = 'SAL';
                break;

            case 'EmployeeStatus':
                $prefix = 'EST';
                break;

            case 'JobTitle':
                $prefix = 'JOB';
                break;

            case 'Education':
                $prefix = 'EDU';
                break;

            case 'Licenses':
                $prefix = 'LIC';
                break;

            case 'Skill':
                $prefix = 'SKI';
                break;

            case 'Skill':
                $prefix = 'SKI';
                break;

            case 'Language':
                $prefix = 'LAN';
                break;

            case 'MembershipType':
                $prefix = 'MEM';
                break;

            case 'Membership':
                $prefix = 'MME';
                break;

            case 'UserGroup':
                $prefix = 'USG';
                break;

            case 'Users':
                $prefix = 'USR';
                break;

            case 'JobSpecifications':
                $prefix = '';
                break;
// kartik new created for diu
            case 'ProjectDiu':
                $prefix = '';
                break;
// kartik new created for diu

            
            case 'ProjectActivity':
                $prefix = '';
                break;
            
            case 'EmployeeReport':
                $prefix = 'REP';
                break;

            case 'LeaveType':
                $prefix = 'LTY';
                break;

            case 'LeaveRequest':
                $prefix = '';
                break;

            case 'Leave':
                $prefix = '';
                break;

            case 'EmployeeLeaveEntitlement':
                $prefix = '';
                break;
        }

        return $prefix;
    }

    /**
     * Get next auto increment ID
     *
     * @param bool update - Update id in the database - defaults to true.
     *
     * @return next auto increment ID
     */
    public function getNextID($update = true) {
//jugni have made changes 20130622        
        require_once ROOT_PATH . '/lib/confs/Conf.php';
        $config = new Conf();
        $db_host	= $config->dbhost;
        $db_user        = $config->dbuser;
        $db_pwd         = $config->dbpass;
        $db_name        = $config->dbname;
        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
        mysql_select_db($db_name) or die(mysql_error());

        $maxid=0;
        $tableName = $this->entity->getTable()->tableName;
        if($tableName=="ohrm_timesheet")
        {   
            $sel_sql="Select MAX(timesheet_id) as id from ohrm_timesheet";
            $sel_result = mysql_query($sel_sql);
            while($row= mysql_fetch_object($sel_result))
            {
                $maxid=$row->id;    
            }
             $nextId=$maxid+1;
        }
        else if($tableName=="ohrm_timesheet_item")
        {
            $sel_sql="Select MAX(timesheet_item_id) as id from ohrm_timesheet_item";
            $sel_result = mysql_query($sel_sql);
            while($row= mysql_fetch_object($sel_result))
            {
                $maxid=$row->id;    
            }
              $nextId=$maxid+1;
        }
        else
        {
            $prefix = $this->getEntityPrefix();
            $currentId = $this->getCurrentID();
            if ($update) {
                $this->updateNextId($currentId + 1);
            }
            $minLength = self::MIN_LENGTH;
            if (get_class($this->entity) == "Employee") {
                $minLength = 4;
            }
            $nextId = $prefix . str_pad($currentId + 1, $minLength, "0", STR_PAD_LEFT);
        }   
        return $nextId;
    }

    /**
     * Get Current ID
     */
    private function getCurrentID() {
        $tableName = $this->entity->getTable()->tableName;
        $q = Doctrine_Query::create()
                ->from('UniqueId')
                ->where('table_name = ' . "'$tableName'");

        $uniqueId = $q->fetchOne();
        if ($uniqueId instanceof UniqueId) {
            return $uniqueId->getLastId();
        } else {
            return 0;
        }
    }

    /**
     * Update next ID
     * @param int $nextId
     */
    private function updateNextId($nextId) {
        try {
            $tableName = $this->entity->getTable()->tableName;
            $q = Doctrine_Query::create()
                    ->update('UniqueId')
                    ->set("last_id", "'" . $nextId . "'")
                    ->where('table_name = ' . "'$tableName'");

            $q->execute();
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

}