<?php


/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
/* For logging PHP errors */
include_once('lib/confs/log_settings.php');

$installed = true;

/**
 * This if case checks whether the user is logged in. If so it will decorate User object with the user's user role.
 * This decorated user object is only used to determine menu accessibility. This decorated user object should not be
 * used for any other purposess. This if case will be dicarded when the whole system is converted to symfony.
 */
if (file_exists('symfony/config/databases.yml')) {

    define('SF_APP_NAME', 'orangehrm');
    define('SF_ENV', 'prod');
    define('SF_CONN', 'doctrine');


    require_once(dirname(__FILE__) . '/symfony/config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration(SF_APP_NAME, 'prod', true);
    new sfDatabaseManager($configuration);
    $context = sfContext::createInstance($configuration);
    
    if (isset($_SESSION['user'])) {
        
        if ($_SESSION['isAdmin'] == "Yes") {
            $userRoleArray['isAdmin'] = true;
        } else {
            $userRoleArray['isAdmin'] = false;
        }

        $userRoleArray['isSupervisor'] = $_SESSION['isSupervisor'];
        $userRoleArray['isProjectAdmin'] = $_SESSION['isProjectAdmin'];
        $userRoleArray['isHiringManager'] = $_SESSION['isHiringManager'];
        $userRoleArray['isInterviewer'] = $_SESSION['isInterviewer'];

        if ($_SESSION['empNumber'] == null) {
            $userRoleArray['isEssUser'] = false;
        } else {
            $userRoleArray['isEssUser'] = true;
        }

        $userObj = new User();

        $simpleUserRoleFactory = new SimpleUserRoleFactory();
        $decoratedUser = $simpleUserRoleFactory->decorateUserRole($userObj, $userRoleArray);
        $decoratedUser->setEmployeeNumber($_SESSION['empNumber']);
        $decoratedUser->setUserId($_SESSION['user']);

        $accessibleTimeMenuItems = $decoratedUser->getAccessibleTimeMenus();
        $accessibleTimeSubMenuItems = $decoratedUser->getAccessibleTimeSubMenus();
        $accessibleRecruitmentMenuItems = $decoratedUser->getAccessibleRecruitmentMenus();
        $attendanceMenus = $decoratedUser->getAccessibleAttendanceSubMenus();
        $reportsMenus = $decoratedUser->getAccessibleReportSubMenus();
        $recruitHomePage = './symfony/web/index.php/recruitment/viewCandidates';
        
        $i18n = $context->getI18N();
        $cultureElements = explode('_', $context->getUser()->getCulture()); // Used in <html> tag
        
        /* For checking TimesheetPeriodStartDaySet status : Begins */
        $timesheetPeriodService = new TimesheetPeriodService();
        if ($timesheetPeriodService->isTimesheetPeriodDefined() == 'Yes') {
            $_SESSION['timePeriodSet'] = 'Yes';
        } else {
            $_SESSION['timePeriodSet'] = 'No';
        }
        /* For checking TimesheetPeriodStartDaySet status : Ends */    
        // Check if a user defined user role (isPredefined = false)
        $isPredefinedUserRole = !UserRoleManagerFactory::getUserRoleManager()->userHasNonPredefinedRole();
        
        $allowedToAddEmployee = UserRoleManagerFactory::getUserRoleManager()->isActionAllowed(PluginWorkflowStateMachine::FLOW_EMPLOYEE,
                Employee::STATE_NOT_EXIST, PluginWorkflowStateMachine::EMPLOYEE_ACTION_ADD);        
    }
} else {
    $installed = false;
}

define('ROOT_PATH', dirname(__FILE__));

if (!is_file(ROOT_PATH . '/lib/confs/Conf.php')) {
    $installed = false;
}

if (!$installed) {
    header('Location: ./install.php');
    exit();    
}

ob_start();

if (!isset($_SESSION['user'])) {

    header("Location: ./symfony/web/index.php/auth/login");
    exit();
}

if (isset($_GET['ACT']) && $_GET['ACT'] == 'logout') {
    session_destroy();
    setcookie('Loggedin', '', time() - 3600, '/');
    header("Location: ./symfony/web/index.php/auth/login");
    exit();
}

/* Sanitising $_GET parameters: Begins */

if (!empty($_GET)) {
    
    $a = array();
    
    foreach ($_GET as $key => $value) {
        $a[$key] = htmlspecialchars($value);
    }
    
    $_GET = $a;
    
}

/* Sanitising $_GET parameters: Ends */

/* Loading disabled modules: Begins */

require_once ROOT_PATH . '/lib/common/ModuleManager.php';

$disabledModules = array();

if (isset($_SESSION['admin.disabledModules'])) {
    
    $disabledModules = $_SESSION['admin.disabledModules'];
    
} else {
    
    $moduleManager = new ModuleManager();    
    $disabledModules = $moduleManager->getDisabledModuleList();
    $_SESSION['admin.disabledModules'] = $disabledModules;    
    
}

/* Loading disabled modules: Ends */

define('Admin', 'MOD001');
define('PIM', 'MOD002');
define('MT', 'MOD003');
define('Report', 'MOD004');
define('Leave', 'MOD005');
define('TimeM', 'MOD006');
define('Benefits', 'MOD007');
define('Recruit', 'MOD008');
define('Perform', 'MOD009');

$arrRights = array('add' => false, 'edit' => false, 'delete' => false, 'view' => false);
$arrAllRights = array(Admin => $arrRights,
    PIM => $arrRights,
    MT => $arrRights,
    Report => $arrRights,
    Leave => $arrRights,
    TimeM => $arrRights,
    Benefits => $arrRights,
    Recruit => $arrRights,
    Perform => $arrRights);

require_once ROOT_PATH . '/lib/models/maintenance/Rights.php';
require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
//require_once ROOT_PATH . '/lib/common/Config.php'; //using ConfigService instead
require_once ROOT_PATH . '/lib/common/authorize.php';

$_SESSION['path'] = ROOT_PATH;
?>
<?php
/* Default modules */
$showingDefaultPage = false;

if (!isset($_GET['menu_no_top'])) {
    
    $showingDefaultPage = true;
    
    if ($_SESSION['isAdmin'] == 'Yes') {
        $_GET['menu_no_top'] = "hr";
    } else if ($_SESSION['isSupervisor']) {
        $_GET['menu_no_top'] = "ess";
    } else {
        $_GET['menu_no_top'] = "ess";
    }
}

/** Clean Get variables that are used in URLs in page */
$varsToClean = array('uniqcode', 'isAdmin', 'pageNo', 'id', 'repcode', 'reqcode', 'menu_no_top');

foreach ($varsToClean as $var) {
    if (isset($_GET[$var])) {
        $_GET[$var] = CommonFunctions::cleanAlphaNumericIdField($_GET[$var]);
    }
}

if ($_SESSION['isAdmin'] == 'Yes') {
    $rights = new Rights();

    foreach ($arrAllRights as $moduleCode => $currRights) {
        $arrAllRights[$moduleCode] = $rights->getRights($_SESSION['userGroup'], $moduleCode);
    }

    $ugroup = new UserGroups();
    $ugDet = $ugroup->filterUserGroups($_SESSION['userGroup']);

    $arrRights['repDef'] = $ugDet[0][2] == '1' ? true : false;
} else {

    /* Assign supervisors edit and view rights to the PIM
     * They have PIM rights over their subordinates, but they cannot add/delete
     * employees. But they have add/delete rights in the employee details page.
     */
    if ($_SESSION['isSupervisor']) {
        $arrAllRights[PIM] = array('add' => false, 'edit' => true, 'delete' => false, 'view' => true);
    }

    /*
     * Assign Manager's access to recruitment module
     */
    if ($_SESSION['isHiringManager'] || $_SESSION['isInterviewer']) {
        $arrAllRights[Recruit] = array('view' => true);
    }
    if (!$isPredefinedUserRole) {
        $arrAllRights[PIM]['view'] = true;
    }
}

switch ($_GET['menu_no_top']) {
    case "eim":
        $arrRights = $arrAllRights[Admin];
        break;
    case "hr" :
        $arrRights = $arrAllRights[PIM];
        break;
    case "mt" :
        $arrRights = $arrAllRights[MT];
        break;
    case "rep" :
        $arrRights = $arrAllRights[Report];
        break;
    case "leave" :
        $arrRights = $arrAllRights[Leave];
        break;
    case "time" :
        $arrRights = $arrAllRights[TimeM];
        break;
    case "recruit" :
        $arrRights = $arrAllRights[Recruit];
        break;
    case "perform" :
        $arrRights = $arrAllRights[Perform];
        break;
}
$_SESSION['localRights'] = $arrRights;

$styleSheet = CommonFunctions::getTheme();

$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

// Default leave home page
$configService = new ConfigService();
$leavePeriodDefined = $configService->isLeavePeriodDefined();
if (!$leavePeriodDefined) {
    if ($authorizeObj->isAdmin()) {
        $leaveHomePage = './symfony/web/index.php/leave/defineLeavePeriod';
    } else {
        $leaveHomePage = './symfony/web/index.php/leave/showLeavePeriodNotDefinedWarning';
    }
} else {
    if ($authorizeObj->isAdmin()) {
        $leaveHomePage = './symfony/web/index.php/leave/viewLeaveList/reset/1';
    } else if ($authorizeObj->isSupervisor()) {
        if ($authorizeObj->isAdmin()) {
            $leaveHomePage = './symfony/web/index.php/leave/viewLeaveList/reset/1';
        } else {
            $leaveHomePage = './symfony/web/index.php/leave/viewLeaveList/reset/1';
        }
    } else if ($authorizeObj->isESS()) {
        $leaveHomePage = './symfony/web/index.php/leave/viewMyLeaveList/reset/1';
    }
}

// Time module default pages
if (!$authorizeObj->isAdmin() && $authorizeObj->isESS()) {
    if ($_SESSION['timePeriodSet'] == 'Yes') {
        $timeHomePage = './symfony/web/index.php/time/viewMyTimeTimesheet';
    } else {
        $timeHomePage = './symfony/web/index.php/time/defineTimesheetPeriod';
    }

} else {
    if ($_SESSION['timePeriodSet'] == 'Yes') {
        $timeHomePage = './symfony/web/index.php/time/viewEmployeeTimesheet';
    } else {
        $timeHomePage = './symfony/web/index.php/time/defineTimesheetPeriod';
    }

}






/* Disabling Benefits module: Begins 
if (!$authorizeObj->isAdmin() && $authorizeObj->isESS()) {
    $beneftisHomePage = 'benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Benefits_Schedule_Select_Year';
    $empId = $_SESSION['empID'];
    $year = date('Y');
    $personalHspSummary = "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Search_Hsp_Summary&empId=$empId&year=$year";
} else {
    $beneftisHomePage = 'benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Benefits_Schedule_Select_Year';
    $personalHspSummary = 'benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Summary_Select_Year_Employee_Admin';
}
   Disabling Benefits module: Ends */







if ($authorizeObj->isESS()) {
    if ($_SESSION['timePeriodSet'] == 'Yes') {
        $timeHomePage = './symfony/web/index.php/attendance/punchIn';
    } else {
        $timeHomePage = './symfony/web/index.php/time/defineTimesheetPeriod';
    }
}

// Default page in admin module is the Company general info page.
$defaultAdminView = "GEN";
$allowAdminView = false;

if ($_SESSION['isAdmin'] == 'No') {
    if ($_SESSION['isProjectAdmin']) {

        // Default page for project admins is the Project Activity page
        $defaultAdminView = "PAC";

        // Allow project admins to view PAC (Project Activity) page only (in the admin module)
        // If uniqcode is not set, the default view is Project activity
        if ((!isset($_GET['uniqcode'])) || ($_GET['uniqcode'] == 'PAC')) {
            $allowAdminView = true;
        }
    }
}
$arrAllRights[PIM]['add'] = $allowedToAddEmployee;

require_once ROOT_PATH . '/lib/common/Language.php';
require_once ROOT_PATH . '/lib/common/menu/MenuItem.php';

$lan = new Language();

require_once ROOT_PATH . '/language/default/lang_default_full.php';
require_once($lan->getLangPath("full.php"));

require_once ROOT_PATH . '/themes/' . $styleSheet . '/menu/Menu.php';
$menuObj = new Menu();

/* Create menu items */
/* TODO: Extract to separate class */
$menu = array();

/* View for Admin users */
if ($_SESSION['isAdmin'] == 'Yes' || $arrAllRights[Admin]['view']) {
    $menuItem = new MenuItem("admin", $i18n->__("Admin"), "./index.php?menu_no_top=eim");
    $menuItem->setCurrent($_GET['menu_no_top'] == "eim");

    $subs = array();

    $sub = new MenuItem("companyinfo", $i18n->__("Organization"), "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("companyinfo", $i18n->__("General Information"), "./symfony/web/index.php/admin/viewOrganizationGeneralInformation");
    $subsubs[] = new MenuItem("companyinfo", $i18n->__("Locations"), "./symfony/web/index.php/admin/viewLocations");
    $subsubs[] = new MenuItem("companyinfo", $i18n->__("Structure"), "./symfony/web/index.php/admin/viewCompanyStructure");

    $sub->setSubMenuItems($subsubs);


    $subs[] = $sub;

    $sub = new MenuItem("job", $i18n->__("Job"), "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("job", $i18n->__("Job Titles"), "./symfony/web/index.php/admin/viewJobTitleList");
    $subsubs[] = new MenuItem("job", $i18n->__("Pay Grades"), "./symfony/web/index.php/admin/viewPayGrades");
    $subsubs[] = new MenuItem("job", $i18n->__("Employment Status"), "./symfony/web/index.php/admin/employmentStatus");
    $subsubs[] = new MenuItem("job", $i18n->__("Job Categories"), "./symfony/web/index.php/admin/jobCategory");
    $subsubs[] = new MenuItem("job", $i18n->__("Work Shifts"), "./symfony/web/index.php/admin/workShift");
    
//    kartik
    
    $subsubs[] = new MenuItem("job", $i18n->__("OT Configure"), "./inextrix/index.php?action=otconfig",'rightMenu');  
   
//    kartik
    
    
    
    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    $sub = new MenuItem("qualifications", $i18n->__("Qualification"), "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("qualifications", $i18n->__("Skills"), "./symfony/web/index.php/admin/viewSkills");
    $subsubs[] = new MenuItem("qualifications", $i18n->__("Education"), "./symfony/web/index.php/admin/viewEducation");
    $subsubs[] = new MenuItem("qualifications", $i18n->__("Licenses"), "./symfony/web/index.php/admin/viewLicenses");
    $subsubs[] = new MenuItem("qualifications", $i18n->__("Languages"), "./symfony/web/index.php/admin/viewLanguages");
    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    $sub = new MenuItem("memberships", $i18n->__("Memberships"), "./symfony/web/index.php/admin/membership", "rightMenu");
    $subs[] = $sub;

    $sub = new MenuItem("nationalities", $i18n->__("Nationalities"), "./symfony/web/index.php/admin/nationality", "rightMenu");
    $subs[] = $sub;

    $sub = new MenuItem("users", $i18n->__("Users"), "./symfony/web/index.php/admin/viewSystemUsers", "rightMenu");
    $subsubs = array();

    if (is_dir(ROOT_PATH . '/symfony/plugins/orangehrmSecurityAuthenticationPlugin') && $arrAllRights[Admin]['edit']) {
        $subsubs[] = new MenuItem('users', $i18n->__("Configure Security Authentication"), './symfony/web/index.php/securityAuthentication/securityAuthenticationConfigure', 'rightMenu');
    }

    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    if (is_dir(ROOT_PATH . '/symfony/plugins/orangehrmBaseAuthorizationPlugin')) {
        $authMenuHelper = new AuthMenuHelper();
        $authMenuItem = $authMenuHelper->getMenuItem();
        
        if (!empty($authMenuItem)) {
            $subs[] = $authMenuItem;
        }

    }
    
    $sub = new MenuItem("email", $i18n->__("Email Notifications"), "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("email", $i18n->__("Configuration"), "./symfony/web/index.php/admin/listMailConfiguration");
    $subsubs[] = new MenuItem("email", $i18n->__("Subscribe"), "./symfony/web/index.php/admin/viewEmailNotification");
    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    $sub = new MenuItem("project", $i18n->__("Project Info"), "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("project", $i18n->__("Customers"), "./symfony/web/index.php/admin/viewCustomers");
    $subsubs[] = new MenuItem("project", $i18n->__("Projects"), "./symfony/web/index.php/admin/viewProjects");

    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    $sub = new MenuItem("configuration", $i18n->__("Configuration"), "#");
    $subsubs = array();
    $subsubs[] = new MenuItem("configuration", $i18n->__("Localization"), "./symfony/web/index.php/admin/localization");
    $subsubs[] = new MenuItem("configuration", $i18n->__("Modules"), "./symfony/web/index.php/admin/viewModules");
    $sub->setSubMenuItems($subsubs);
    $subs[] = $sub;

    if (is_dir(ROOT_PATH . '/symfony/plugins/orangehrmAuditTrailPlugin') && $arrAllRights[Admin]['view']) {
        $subs[] = new MenuItem('audittrail', $i18n->__("Audit Trail"), './symfony/web/index.php/audittrail/viewAuditTrail', 'rightMenu');
    }

    if (is_dir(ROOT_PATH . '/symfony/plugins/orangehrmLDAPAuthenticationPlugin') && $arrAllRights[Admin]['edit']) {
        $subs[] = new MenuItem('ldap', $i18n->__("LDAP Configuration"), './symfony/web/index.php/ldapAuthentication/configureLDAPAuthentication', 'rightMenu');
    }

    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
} else {
    
    $subs = array();
    
    if ($_SESSION['isProjectAdmin']) {
        $subs[] = new MenuItem("project", $i18n->__("Projects"), "./symfony/web/index.php/admin/viewProjects", 'rightMenu');
    }
    
    if (count($subs) > 0) {
        $menuItem = new MenuItem("admin", $i18n->__("Admin"), '#', 'rightMenu');
        $menuItem->setCurrent($_GET['menu_no_top'] == "eim");
        $menuItem->setSubMenuItems($subs);
        $menu[] = $menuItem;        
    }
}

define('PIM_MENU_TYPE', 'left');
$_SESSION['PIM_MENU_TYPE'] = PIM_MENU_TYPE;

/* PIM menu start */
if ((($_SESSION['isAdmin'] == 'Yes' || $_SESSION['isSupervisor']) && $arrAllRights[PIM]['view']) || !$isPredefinedUserRole) {

    $menuItem = new MenuItem("pim", $i18n->__("PIM"), "./index.php?menu_no_top=hr&reset=1");
    $menuItem->setCurrent($_GET['menu_no_top'] == "hr");
    $enablePimMenu = false;
    if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top'] == "hr") && isset($_GET['reqcode']) && $arrRights['view']) {
        $enablePimMenu = true;
    }
    $subs = array();
    if ($_SESSION['isAdmin'] == 'Yes') {

        $sub = new MenuItem("configure", __("Configuration"), "#");
        $subsubs = array();
        $subsubs[] = new MenuItem("pimconfig", $i18n->__("Optional Fields"), "./symfony/web/index.php/pim/configurePim", "rightMenu");
        $subsubs[] = new MenuItem("customfields", $i18n->__("Custom Fields"), "./symfony/web/index.php/pim/listCustomFields");
        $subsubs[] = new MenuItem("customfields", $i18n->__("Data Import"), "./symfony/web/index.php/admin/pimCsvImport");
        $subsubs[] = new MenuItem("customfields", $i18n->__("Reporting Methods"), "./symfony/web/index.php/pim/viewReportingMethods");
        $subsubs[] = new MenuItem("customfields", $i18n->__("Termination Reasons"), "./symfony/web/index.php/pim/viewTerminationReasons");
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;
    }

    $subs[] = new MenuItem("emplist", $i18n->__("Employee List"), "./symfony/web/index.php/pim/viewEmployeeList/reset/1", "rightMenu");
    if ($arrAllRights[PIM]['add']) {
            $subs[] = new MenuItem("empadd", $i18n->__("Add Employee"), "./symfony/web/index.php/pim/addEmployee", "rightMenu");
    }

    if ($_SESSION['isAdmin'] == 'Yes') {
        $subs[] = new MenuItem("reports", $i18n->__("Reports"), "./symfony/web/index.php/core/viewDefinedPredefinedReports/reportGroup/3/reportType/PIM_DEFINED", "rightMenu");
    }

    $menuItem->setSubMenuItems($subs);

    $menu[] = $menuItem;
}

/* Start leave menu */
if (($_SESSION['empID'] != null) || $arrAllRights[Leave]['view']) {
    $menuItem = new MenuItem("leave", $i18n->__("Leave"), "./index.php?menu_no_top=leave&reset=1");
    $menuItem->setCurrent($_GET['menu_no_top'] == "leave");

    $subs = array();
    $subsubs = array();

    if ($authorizeObj->isAdmin() && $arrAllRights[Leave]['view']) {

        $sub = new MenuItem("leavesummary", $i18n->__("Configure"), "#");

        $subsubs[] = new MenuItem("leaveperiod", $i18n->__("Leave Period"), './symfony/web/index.php/leave/defineLeavePeriod', 'rightMenu');
        $subsubs[] = new MenuItem("leavetypes", $i18n->__("Leave Types"), './symfony/web/index.php/leave/leaveTypeList');
        $subsubs[] = new MenuItem("daysoff", $i18n->__("Work Week"), "./symfony/web/index.php/leave/defineWorkWeek");
        $subsubs[] = new MenuItem("daysoff", $i18n->__("Holidays"), "./symfony/web/index.php/leave/viewHolidayList");

        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;
    }

    $subs[] = new MenuItem("leavesummary", $i18n->__("Leave Summary"), "./symfony/web/index.php/leave/viewLeaveSummary", 'rightMenu');

    if ($authorizeObj->isSupervisor() && !$authorizeObj->isAdmin()) {
        $subs[] = new MenuItem("leavelist", $i18n->__("Leave List"), './symfony/web/index.php/leave/viewLeaveList/reset/1', 'rightMenu');
    }
    if ($authorizeObj->isAdmin() && $arrAllRights[Leave]['view']) {
        $subs[] = new MenuItem("leavelist", $i18n->__("Leave List"), './symfony/web/index.php/leave/viewLeaveList/reset/1', 'rightMenu');
    }

    if (($authorizeObj->isAdmin() && $arrAllRights[Leave]['add']) || $authorizeObj->isSupervisor()) {
        $subs[] = new MenuItem("assignleave", $i18n->__("Assign Leave"), "./symfony/web/index.php/leave/assignLeave", 'rightMenu');
        
//        kartik
//        if ($authorizeObj->isAdmin() && $arrAllRights[Leave]['view']) {
//          $subs[] = new MenuItem("Job", $i18n->__("OT Configure"), "./inextrix/index.php?action=otconfig",'rightMenu');  
//        }
//        kartik
        
    }

    if ($authorizeObj->isESS()) {
        $subs[] = new MenuItem("leavelist", $i18n->__("My Leave"), './symfony/web/index.php/leave/viewMyLeaveList/reset/1', 'rightMenu');
        $subs[] = new MenuItem("applyLeave", $i18n->__("Apply"), "./symfony/web/index.php/leave/applyLeave", 'rightMenu');
    }

    if (file_exists('symfony/plugins/orangehrmLeaveCalendarPlugin/config/orangehrmLeaveCalendarPluginConfiguration.class.php')) {//if plugin is installed
        $subs[] = new MenuItem("leavelist", $i18n->__("Leave Calendar"), './symfony/web/index.php/leavecalendar/showLeaveCalendar', 'rightMenu');
    }
    /* Emptying the leave menu items if leave period is not defined */
    if (!$leavePeriodDefined) {
        $subs = array();
    }

    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
}

/* Start time menu */
if (($_SESSION['empID'] != null) || $arrAllRights[TimeM]['view']) {
    $menuItem = new MenuItem("time", $i18n->__("Time"), "./index.php?menu_no_top=time");
    $menuItem->setCurrent($_GET['menu_no_top'] == "time");

    /* Only show rest of menu if time period set */
    if ($_SESSION['timePeriodSet'] == "Yes" && file_exists('symfony/config/databases.yml')) {
        $subs = array();

        // modified under restructure time menu story

        $subsubs = array();
        $subsubs0 = array();
        $subsubs1 = array();
        if ($accessibleTimeMenuItems != null) {
            foreach ($accessibleTimeMenuItems as $ttt) {

                $sub = new MenuItem("timesheets", __($ttt->getDisplayName()), $ttt->getLink(), 'rightMenu');

                if ($ttt->getDisplayName() == "Timesheets") {

                    foreach ($accessibleTimeSubMenuItems as $ctm) {

                        $subsubs[] = new MenuItem("timesheets", __($ctm->getDisplayName()), $ctm->getLink());
                    }

                    $sub->setSubMenuItems($subsubs);
                }
                if ($ttt->getDisplayName() == "Attendance") {

                    foreach ($attendanceMenus as $ptm) {
                        $subsubs0[] = new MenuItem("timesheets", __($ptm->getDisplayName()), $ptm->getLink());
                    }

                    $sub->setSubMenuItems($subsubs0);
                }

                if ($ttt->getDisplayName() == "Reports") {

                    foreach ($reportsMenus as $ptm) {
                        $subsubs1[] = new MenuItem("timesheets", __($ptm->getDisplayName()), $ptm->getLink());
                    }

                    $sub->setSubMenuItems($subsubs1);
                }

                $subs[] = $sub;
            }
        }

        $menuItem->setSubMenuItems($subs);
    }
    $menu[] = $menuItem;
}

/* Start recruitment menu */

if ($arrAllRights[Recruit]['view']) {


    $menuItem = new MenuItem("recruit", $i18n->__("Recruitment"), "./index.php?menu_no_top=recruit");
    $menuItem->setCurrent($_GET['menu_no_top'] == "recruit");

    if (file_exists('symfony/config/databases.yml')) {
        $subs = array();
        foreach ($accessibleRecruitmentMenuItems as $tttt) {
            $subs[] = new MenuItem("recruit", $tttt->getDisplayName(), $tttt->getLink(), "rightMenu");
        }
    }
    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
}

/* Performance menu start */

$menuItem = new MenuItem("perform", $i18n->__("Performance"), "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/viewReview/mode/new");
$menuItem->setCurrent($_GET['menu_no_top'] == "perform");
$enablePerformMenu = false;
if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top'] == "perform") && isset($_GET['reqcode']) && $arrRights['view']) {
    $enablePerformMenu = true;
}
$subs = array();

if ($arrAllRights[Perform]['add'] && ($_SESSION['isAdmin'] == 'Yes')) {
    $subs[] = new MenuItem('definekpi', $i18n->__("KPI List"), "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/listDefineKpi");
    $subs[] = new MenuItem('definekpi', $i18n->__("Add KPI"), "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/saveKpi");
    $subs[] = new MenuItem('definekpi', $i18n->__("Copy KPI"), "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/copyKpi");
    $subs[] = new MenuItem('definekpi', $i18n->__("Add Review"), "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/saveReview");
}

$subs[] = new MenuItem('definekpi', $i18n->__("Reviews"), "index.php?uniqcode=KPI&menu_no_top=performance&uri=performance/viewReview/mode/new");

$menuItem->setSubMenuItems($subs);

$menu[] = $menuItem;

/* Start ESS menu */
if ($_SESSION['isAdmin'] != 'Yes' && $isPredefinedUserRole) {
    $menuItem = new MenuItem("ess", $i18n->__('My Info'), './symfony/web/index.php/pim/viewPersonalDetails?empNumber=' . $_SESSION['empID'], "rightMenu");

    $menuItem->setCurrent($_GET['menu_no_top'] == "ess");
    $enableEssMenu = false;
    if ($_GET['menu_no_top'] == "ess") {
        $enableEssMenu = true;
    }

    $menu[] = $menuItem;
}







/* Disabling Benefits module: Begins
if (($_SESSION['empID'] != null) || $arrAllRights[Benefits]['view']) {
    $menuItem = new MenuItem("benefits", $lang_Menu_Benefits, "./index.php?menu_no_top=benefits");
    $menuItem->setCurrent($_GET['menu_no_top'] == "benefits");

    $subs = array();

    if ($_SESSION['isAdmin'] == "Yes" && $arrAllRights[Benefits]['view']) {
        $yearVal = date('Y');
        $sub = new MenuItem("hsp", $lang_Menu_Benefits_HealthSavingsPlan, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Summary&year={$yearVal}");
        $subsubs = array();
        $subsubs[] = new MenuItem("hsp", $lang_Menu_Benefits_Define_Health_savings_plans, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Define_Health_Savings_Plans");
        $subsubs[] = new MenuItem("hsp", $lang_Menu_Benefits_EmployeeHspSummary, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Summary&year={$yearVal}");
        $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspPaymentsDue, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=List_Hsp_Due");
        $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspExpenditures, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Expenditures_Select_Year_And_Employee");
        $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspUsed, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Used_Select_Year&year={$yearVal}");
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;
    } else {

        if (Config::getHspCurrentPlan() > 0) {
            $sub = new MenuItem("hsp", $lang_Menu_Benefits_HealthSavingsPlan, $personalHspSummary);
        } else {
            $sub = new MenuItem("hsp", $lang_Menu_Benefits_HealthSavingsPlan, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Not_Defined");
        }
        $subsubs = array();

        if ($authorizeObj->isESS()) {
            $yearVal = date('Y');
            $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspExpenditures, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Expenditures&year={$yearVal}&employeeId={$_SESSION['empID']}");

            if (Config::getHspCurrentPlan() > 0) { // Show only when Admin has defined a HSP plan
                $subsubs[] = new MenuItem("hsp", $lang_Benefits_HspRequest, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Hsp_Request_Add_View");
                $subsubs[] = new MenuItem("hsp", $lang_Menu_Benefits_PersonalHspSummary, $personalHspSummary);
            }
        }
        $sub->setSubMenuItems($subsubs);
        $subs[] = $sub;
    }

    if ($_SESSION['isAdmin'] == "Yes" && $arrAllRights[Benefits]['view']) {
        $sub = new MenuItem("payrollschedule", $lang_Menu_Benefits_PayrollSchedule, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Benefits_Schedule_Select_Year");

        $subsubs = array();
        $subsubs[] = new MenuItem("payrollschedule", $lang_Benefits_ViewPayrollSchedule, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=Benefits_Schedule_Select_Year");
        if ($arrAllRights[Benefits]['add']) {
            $subsubs[] = new MenuItem("payrollschedule", $lang_Benefits_AddPayPeriod, "benefitscode/lib/controllers/CentralController.php?benefitcode=Benefits&action=View_Add_Pay_Period");
        }
        $sub->setSubMenuItems($subsubs);

        $subs[] = $sub;
    }

    $menuItem->setSubMenuItems($subs);
    $menu[] = $menuItem;
}
   Disabling Benefits module: Ends */

/** Asset Tracker Menu items */
if (file_exists('symfony/plugins/orangehrmAssetTrackerPlugin/lib/menu/asset_tracker_menu.php')) {    
    include_once('symfony/plugins/orangehrmAssetTrackerPlugin/lib/menu/asset_tracker_menu.php');
}

/** Dashboard Menu items */
if (file_exists('symfony/plugins/orangehrmDashboardPlugin/lib/menu/dashboard_menu.php')) {    
    include_once('symfony/plugins/orangehrmDashboardPlugin/lib/menu/dashboard_menu.php');
}
    





/* Start help menu */
$menuItem = new MenuItem("help", $i18n->__("Help"), '#');
$subs = array();
// $subs[] = new MenuItem("support", $i18n->__("Support"), "http://www.orangehrm.com/support-plans.php?utm_source=application_support&utm_medium=app_url&utm_campaign=orangeapp", '_blank');
// $subs[] = new MenuItem("forum", $i18n->__("Forum"), "http://www.orangehrm.com/forum/", '_blank');
// $subs[] = new MenuItem("blog", $i18n->__("Blog"), "http://www.orangehrm.com/blog/", '_blank');
// $subs[] = new MenuItem("support", $i18n->__("Training"), "http://www.orangehrm.com/training.php?utm_source=application_traning&utm_medium=app_url&utm_campaign=orangeapp", '_blank');
// $subs[] = new MenuItem("support", $i18n->__("Add-Ons"), "http://www.orangehrm.com/addon-plans.shtml?utm_source=application_addons&utm_medium=app_url&utm_campaign=orangeapp", '_blank');
// $subs[] = new MenuItem("support", $i18n->__("Customizations"), "http://www.orangehrm.com/customizations.php?utm_source=application_cus&utm_medium=app_url&utm_campaign=orangeapp", '_blank');
// $subs[] = new MenuItem("bug", $i18n->__("Bug Tracker"), "http://sourceforge.net/apps/mantisbt/orangehrm/view_all_bug_page.php", '_blank');

$menuItem->setSubMenuItems($subs);
$menu[] = $menuItem;
/* End of main menu definition */

/* Checking for disabled modules: Begins */

$count = count($menu);
foreach ($disabledModules as $key => $module) {
        $disabledModules[$key] = __(ucwords($module));
}
for ($i=0; $i<$count; $i++) {

    if (in_array($menu[$i]->getMenuText(), $disabledModules)) {
        unset($menu[$i]);
    }
    
}

/* Checking for disabled modules: Ends */

$welcomeMessage = preg_replace('/#username/', ((isset($_SESSION['fname'])) ? $_SESSION['fname'] : ''), $i18n->__($lang_index_WelcomeMes));

if (isset($_SESSION['ladpUser']) && $_SESSION['ladpUser']) {
    $optionMenu = array();
} else {
    $optionMenu[] = new MenuItem("changepassword", $i18n->__($lang_index_ChangePassword),
                    "./symfony/web/index.php/admin/changeUserPassword");
}

$optionMenu[] = new MenuItem("logout", __($lang_index_Logout), './symfony/web/index.php/auth/logout', '_parent');

if (!isset($home)) {
    
    // Decide on home page
    if (($_GET['menu_no_top'] == "eim") && ($arrRights['view'] || $allowAdminView)) {
        $uniqcode = isset($_GET['uniqcode']) ? $_GET['uniqcode'] : $defaultAdminView;
        $isAdmin = isset($_GET['isAdmin']) ? ('&amp;isAdmin=' . $_GET['isAdmin']) : '';

        /* TODO: Remove this pageNo variable */
        $pageNo = isset($_GET['pageNo']) ? '&amp;pageNo=1' : '';
        if (isset($_GET['uri'])) {
            $uri = (substr($_GET['uri'], 0, 11) == 'performance') ? $_GET['uri'] : 'performance/viewReview/mode/new';
            $home = './symfony/web/index.php/' . $uri;
        } else {
            $home = "./symfony/web/index.php/admin/viewOrganizationGeneralInformation"; //TODO: Use this after fully converted to Symfony
        }
    } elseif (($_GET['menu_no_top'] == "hr") && $arrRights['view']) {

        $home = "./symfony/web/index.php/pim/viewEmployeeList/reset/1";
        if (isset($_GET['uri'])) {
            $home = $_GET['uri'];
        } elseif (isset($_GET['id'])) {
            $home = "./symfony/web/index.php/pim/viewPersonalDetails?empNumber=" . $_GET['id'];
        }
    } elseif ($_GET['menu_no_top'] == "ess") {
        $home = './symfony/web/index.php/pim/viewPersonalDetails?empNumber=' . $_SESSION['empID'];
    } elseif ($_GET['menu_no_top'] == "leave") {
        $home = $leaveHomePage;
    } elseif ($_GET['menu_no_top'] == "time") {
        $home = $timeHomePage;
    } elseif ($_GET['menu_no_top'] == "benefits") {
        $home = $beneftisHomePage;
    } elseif ($_GET['menu_no_top'] == "recruit") {
        $home = $recruitHomePage;
    } elseif ($_GET['menu_no_top'] == "performance") {
        $uri = (substr($_GET['uri'], 0, 11) == 'performance') ? $_GET['uri'] : 'performance/viewReview/mode/new';
        $home = './symfony/web/index.php/' . $uri;
    } else {
        $rightsCount = 0;
        foreach ($arrAllRights as $moduleRights) {
            foreach ($moduleRights as $right) {
                if ($right) {
                    $rightsCount++;
                }
            }
        }

        if ($rightsCount === 0) {
            $home = 'message.php?case=no-rights&type=notice';
        } else {
            $home = "";
        }
    }
}

if (isset($_SESSION['load.admin.viewModules'])) {
    $home = "./symfony/web/index.php/admin/viewModules";
    unset($_SESSION['load.admin.viewModules']);
}

if (isset($_SESSION['load.admin.localization'])) {
    $home = "./symfony/web/index.php/admin/localization";
    unset($_SESSION['load.admin.localization']);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $cultureElements[0]; ?>" lang="<?php echo $cultureElements[0]; ?>">
    <head>
        <title>HRM System</title>
        <!-- Mimic Internet Explorer 8 -->  
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" >         
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link href="themes/<?php echo $styleSheet; ?>/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="favicon.ico" rel="icon" type="image/gif"/>
        <script type="text/javaScript" src="scripts/archive.js"></script>
<?php
$menuObj->getCSS();
$menuObj->getJavascript($menu);
?>
    </head>

    <body>
        <div id="companyLogoHeader"></div>

        
<!--jagruti?-->
<!--<link rel="stylesheet" type="text/css" href="./autocomplete_js/jquery.ajaxcomplete.css" />
<script type="text/javascript" src="./autocomplete_js/jquery.js"></script>
<script type="text/javascript" src="./autocomplete_js/jquery.ajaxcomplete.js"></script>-->
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="scripts/jquery/jquery-1.7.1.js"> </script>
<script type="text/javascript" src="scripts/jquery/jquery-ui-1.10.2.custom.min.js"></script> 
<script type="text/javascript" src="scripts/jquery/jquery-ui-1.10.2.custom.js"></script> 

<link href="themes/<?php echo $styleSheet; ?>/css/jquery/jquery.autocomplete.css" rel="stylesheet" type="text/css"/>
<link href="themes/<?php echo $styleSheet; ?>/css/ui-lightness/jquery-ui-1.10.2.custom.css" rel="stylesheet" />  

<div id="divmsg" style="text-align: center;color:red"><span id="msg"></span></div>

<script type="text/javascript">
    var lang_not_numeric = '<?php echo 'Should Be Less Than 24 and in HH:MM or Decimal Format'; ?>';
    var incorrect_total="<?php echo 'Total Should Be Less Than 24 Hours'; ?>";
</script>  

<!--<link rel="stylesheet" type="text/css" href="jquery.ajaxcomplete.css" />-->

<script type="text/javascript" src="jquery.ajaxcomplete.js"></script>
<script>
$(document).ready(function(){
    
    //alert("ready");
    $('.submitflag').val("0");
 $("#prjname").autocomplete("ajaxcomplete.php", {
		selectFirst: true,
                matchContains:true
	}).result(function(event, item) {
//            alert(item);
                 var id=item; 
                    $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "db_data.php",
                            data: "id="+id,
                               success: function(data)
                                {
//                                    alert(data);
                                    $('.act').empty();
                                    $('#actname').append($('<option/>').attr("value","").text("Select Activity"));
                                    for (var i = 0, len = data.length; i < len; ++i) 
                                    {
                                        var prj = data[i];
                                        $('#actname').append($('<option/>').attr("value", prj.id).text(prj.name));
                                        $('.project_id').val(prj.project_id);
                                        
                                    }
                                }
                             })
                  
                });
});
</script>
<!--jagruti div start        -->
<?

    require_once ROOT_PATH . '/lib/confs/Conf.php';
    $config = new Conf();
        $db_host	= $config->dbhost;
        $db_user        = $config->dbuser;
        $db_pwd         = $config->dbpass;
        $db_name        = $config->dbname;
        $working_hours  = $config->emp_work_hours;
        $otallow=0;
        $error=0;
//        $workstation=$config->emp_work_station;
//        echo $workstation;
        
        
        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
        mysql_select_db($db_name) or die(mysql_error());
       
       
        if(isset($_SESSION['error']))
        {
            $error=$_SESSION['error'];
            $_SESSION['error']=0;
        }
//          echo $error;
        
$final_holiday=array();
$query="SELECT * FROM ohrm_holiday where compensate!=1";  
$res = mysql_query($query);
if($res)
{    
    while($row= mysql_fetch_object($res)){
        $holiday[]=$row->date;
        $yr= date('Y',strtotime($row->date));
        $current_yr= date('Y');
        if(($current_yr > $yr) && ($row->recurring==1))
        {
            $exp_holiday=  explode("-",$row->date);
            $exp_holiday[0]=$current_yr;                           
            $holiday_impload=  implode("-", $exp_holiday);
            $final_holiday[]= $holiday_impload;                                
        }                                                        
    } 
    $final_holiday=array_merge($holiday,$final_holiday);
//    $_SESSION['holiday']=$final_holiday;
    
    
}        
  
                        



    
?>
<script type="text/javascript">

      var error="<?=$error;?>";
      if(error==1)
         document.getElementById('msg').innerHTML = "Can not Update Data....!!"; 
       else if(error==2)
           {
            $('#divmsg').css('color','green').css('text-align','center');
            document.getElementById('msg').innerHTML = "Successfully data updated...!!";
           }
           else if(error==3)
           {
              document.getElementById('msg').innerHTML = "Can not Update Data Please contact to Sysytem Administrator....!!"; 
           }
       else
         document.getElementById('msg').innerHTML = ""; 
      
        
        window.setTimeout( closeHelpDiv, 2000 );
        function closeHelpDiv()
             {
                document.getElementById("msg").innerHTML ="";
                 $('#divmsg').css('color','red').css('text-align','center');
             }   
       
</script> 
 
<?
$_SESSION['error']=0;
//echo $_SESSION['error'];
function converthourtoduration($time)
{
   $final=0;
    if(strrpos($time, "."))
    {
        $fnlmn=0;
        $arr=explode(".",$time);
        $hr=$arr[0];
        $mn=$arr[1];
        
        if(strlen($mn)==1)
        {
            $mn=$mn*10;
        }
        if($mn!=0)
        $fnlmn=intval(($mn*60)/100);
        $tm=$hr.".".$fnlmn;
        $arr=explode(".",$tm);
        $hr=$arr[0];
        $mn=$arr[1];
        $hours = intval(intval($hr)* 3600);
         
        if(strlen($mn)==1)
        {
            $mn="0".$mn;
        }
//        $mn= str_pad($mn, 2, "0", STR_PAD_LEFT);
        $minutes = intval(intval($mn)* 60);
        $final=$hours+$minutes;
    }
    else
    {
       $hours = intval(intval($time)* 3600);
       $final=$hours; 
    }
    return $final;    
}

function convertDurationToHours($durationInSecs)
{
//    require_once ROOT_PATH . '/lib/confs/Conf.php';
//    $config = new Conf();
            $padHours = false;
            $hms = "";
            $hours = intval(intval($durationInSecs) / 3600);
            $hms .= ( $padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':' : $hours . ':';
            $minutes = intval(($durationInSecs / 60) % 60);
            $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
         
            $arr=explode(":",$hms);
            $hr=$arr[0];
            $mn=$arr[1];
            $fnlmn=intval(($mn*100)/60);
            $final=$hr.".".$fnlmn;
            return $final; 
}
//print_r($_POST);exit;
if(isset($_POST['submitflag']) && ($_POST['submitflag']=="1"))
{
   
    require_once ROOT_PATH . '/lib/confs/Conf.php';
    $config = new Conf();

    $empid=$_POST['empname'];
    $working_hours=$_POST['txtworkhours'];
    $date=$_POST['datepicker'];
    $prjid=$_POST['project_id'];
    $actid=$_POST['actname'];
    $st=$_POST['hidetxtst'];
    $ot=$_POST['hidetxtot'];
    $stnew=0;
    $otnew=0;
    $tid=0;
    $flag=0;
    $dbflag=0;
    $error=0;
    $dbst=0;
    $dbot=0;
    $dbtotal=0;
    $insflag=0;

//    echo "<pre>";
//    print_r($_POST);exit;
    
    unset($_POST['empname']);
    unset($_POST['actname']);
    unset($_POST['project_id']);
    unset($_POST['hidetxtst']);
    unset($_POST['hidetxtot']);
    unset($_POST);
    
    
    


    
//   rushika and jugni 20130626
    $dd=date("d",strtotime($date));
    $mm=date("m",strtotime($date));
    $yy=date("Y",strtotime($date));
    $newday=0;
    $newendday=0;
    //echo $date;
  
    if($dd >=1 && $dd <=7)
    {
        $newday=1;
        $newendday=7;
    }
    else if($dd >=8 && $dd <=14)
    {
         $newday=8;
         $newendday=14;
    }
    else if($dd >=15 && $dd <=21)
    {
         $newday=15;
         $newendday=21;
    }
    else if($dd >=22 && $dd <=28)
    {
         $newday=22;
         $newendday=28;
    }
    else
    {
         $newday=29;
//         $newendday=29;
//jagruti 20131113
$newendday=date("t",strtotime($date));

    } 
    
    $final_start_date=$yy."-".$mm."-".$newday;
    $final_end_date=$yy."-".$mm."-".$newendday;
    
    
    
    $final_start_date=date('Y-m-d',strtotime($final_start_date));
    $final_end_date=date('Y-m-d',strtotime($final_end_date));
//Rushika made changes 20130705    
      $query="SELECT start_date
FROM `ohrm_timesheet`
WHERE employee_id = '".$empid."'
AND start_date <= '".$date."'
AND end_date >= '".$date."'";

     $main_result =mysql_query($query);
//     $laststartdate="0000-00-00";
//jagruti 20131113
    $laststartdate=$final_start_date;
     if($main_result)
            {
                while($lastrow= mysql_fetch_object($main_result ))
                { 
		   $laststartdate=$lastrow->start_date;
                }
            }
  
  
     $_SESSION['index_start_date']=$laststartdate;
     $_SESSION['selected_index_date']=$date;
     //20130705 completed
     $_SESSION['index_empid']=$empid;
//     ==================
    
    
    
    
    $check="SELECT sum(duration) as ST,sum(duration_ot) as OT FROM `ohrm_timesheet_item` WHERE employee_id='".$empid."' and date='".$date."'group by employee_id";
    $main_result1 =mysql_query($check);
    if($main_result1)
    {
        while($row= mysql_fetch_object($main_result1))
        { 
           $dbflag=1;
           $dbst=$row->ST;
           $dbot=$row->OT;
        }
    }
    if($dbflag==1)
    {
   
        $dbst=convertDurationToHours($dbst);
        $dbot=convertDurationToHours($dbot);
        $dbtotal=$st+$ot+$dbst+$dbot;
        $dbwork_hrs=$st+$dbst;
        
        if($dbwork_hrs > $working_hours || $dbtotal > 24)
        {
            $error=1;
         $_SESSION['error']=1;
        }

    }
  
    if($error!=1)
    {
         
           $st=converthourtoduration($st);
           $ot=converthourtoduration($ot);
            $sql="SELECT timesheet_item_id,duration,duration_ot FROM `ohrm_timesheet_item` WHERE project_id='".$prjid."' and activity_id='".$actid."' and employee_id='".$empid."' and date='".$date."'";
            $main_result =mysql_query($sql);
            if($main_result)
            {
                while($lastrow= mysql_fetch_object($main_result ))
                { 
                   $flag=1;
                   $tid=$lastrow->timesheet_item_id;
                   $stnew=$lastrow->duration;
                   $otnew=$lastrow->duration_ot;
                }
            }
            if($flag==1)
            {

                $dur=(int)$stnew+(int)$st;
                $durot=(int)$otnew+(int)$ot;
                $total=$dur+$durot;

                $working_hours1=converthourtoduration($working_hours);
                $total24=converthourtoduration(24);
                
//                echo $dur.">".$working_hours1."||".$total.">".$total24;
                if($dur > $working_hours1 || $total > $total24)
                {
                    $error=1; 
                    $_SESSION['error']=1; 
                }
                else
                {
                    $update="UPDATE ohrm_timesheet_item SET duration='".$dur."',duration_ot='".$durot."' WHERE timesheet_item_id='".$tid."'";
                    $uresult =mysql_query($update);
                    $error=2;
                    $_SESSION['error']=2;
                }

            }
            else
            {
                $last_row_id=0;  
                $lasid_sql = "SELECT MAX(timesheet_id) as id FROM ohrm_timesheet";
                $last_result =mysql_query($lasid_sql);
                while($lastrow= mysql_fetch_object($last_result ))
                {
                  $last_row_id=$lastrow->id;
                }  
                if($last_row_id==NULL)
                {
                 $last_row_id=0;
                }
                $last_row_id++;
                $timesheet_id=0;
                $query = "SELECT timesheet_id FROM ohrm_timesheet WHERE employee_id='$empid' AND start_date <='$date' AND end_date >='$date'";
                $sel_result =mysql_query($query);
                while($row= mysql_fetch_object( $sel_result )) 
                {
                  $timesheet_id=$row->timesheet_id;
                }
                $start=$final_start_date;
                $end=$final_end_date;
                    
                if($timesheet_id==0 || $timesheet_id==NULL || $timesheet_id=="")
                {
                    if($mm > 4 && $mm < 7 && $year <= 2013)
                    {
                        $error=3;
                        $_SESSION['error']=3;
                        $insflag=1;
                        
                    }
                    else
                    {  
                        $insert_query = "INSERT INTO ohrm_timesheet(timesheet_id,state,start_date,end_date,employee_id) values($last_row_id,'NOT SUBMITTED','$start','$end','$empid')";
                        mysql_query($insert_query);
                        $timesheet_id=$last_row_id;
                        $insflag=0;
                    }   
                }

                if($insflag==0)
                {
                        $lasid_sql1 = "SELECT MAX(timesheet_item_id) as id FROM ohrm_timesheet_item";
                        $last_result1 =mysql_query($lasid_sql1);
                        while($lastrow1= mysql_fetch_object($last_result1 ))
                        {
                          $last_row_id1=$lastrow1->id;
                        }  
                        if($last_row_id1==NULL)
                        {
                         $last_row_id1=0;
                        }
                        for($k=0;$k<7;$k++)
                        {
                            $last_row_id1++;
                            $newst=0;
                            $newot=0;
                            if($start==$date)
                            {
                                $newst=$st;
                                $newot=$ot;
                            }
                            else
                            {
                                $newst=0;
                                $newot=0;
                            }

                            $ins_query1="INSERT INTO ohrm_timesheet_item(timesheet_item_id,timesheet_id,date,duration,duration_ot,project_id,employee_id,activity_id) VALUES ($last_row_id1,$timesheet_id,'$start','$newst','$newot',$prjid,$empid,$actid)";
                            $insert_query = mysql_query($ins_query1);
                            $start=date('Y-m-d',strtotime("+1 day",strtotime($start)));
                        }
                        $_SESSION['error']=2;
                }
            } 
    }  
    
$_POST=array();    
//   rushika and jugni 20130626
$_SESSION['index_patch']=1;
$PHP_SELF=$_SERVER['PHP_SELF'];
 header("Location: $PHP_SELF");
}
else
{
  $_SESSION['error']=0;
}


$emp_arr=array();
$prj_arr=array();
$query="SELECT t1.emp_number,CONCAT(t1.emp_firstname,' ',t1.emp_lastname) as name FROM hs_hr_emp_reportto t, hs_hr_employee t1 WHERE t.erep_sup_emp_number='".$_SESSION['empID']."' AND t.erep_sub_emp_number=t1.emp_number or t1.emp_number ='".$_SESSION['empID']."' group by t1.emp_number";
                    $main_result =mysql_query($query);
                    if($main_result)
		    {
                        while($lastrow= mysql_fetch_object($main_result ))
                        { 
                         $emp_arr[]=array("emp_number"=>$lastrow->emp_number,"name"=>$lastrow->name);   
                        }
		    } 
	    
if ($_SESSION['isAdmin'] == 'No') 
    //echo $_SESSION['index_empid'];
{
                    ?>

        <div align='center'>
            <form name="theForm" id="theForm" method="post" action="<?$_SERVER['PHP_SELF']?>" onSubmit="return validate()">
           
                <table style="border: 2px solid black;border-color: orange">
  <tr>
  
      <tr><td><b><div class="divtxtemp">Employee Name</b></div></td><td><b>Select Date</b></td><td><b>Project Name</b></td><td><b>Project Activity</b></td><td><b>ST</b></td><td><div class="divtxtot"style="display:none"><b>OT</b></div></td></tr>
  <tr>
      <td>
          <div class="divemp">
          
          <select name="empname" id="empname" onchange="getothours($(this).val());">
              <? if(count($emp_arr) > 1) {?>
              <option value="">Select Employee Name</option> 
              
 <?}
    foreach($emp_arr as $val)
    {
  
     if(isset($_SESSION['index_empid']) && $val['emp_number']==$_SESSION['index_empid'])
     {
   	    echo '<option value='.$val['emp_number'].' selected="selected">'.$val['name'].'</option>';  
     }
     else
     {
	    echo "<option value=".$val['emp_number'].">".$val['name']."</option>";  
     }
    }
    ?>
    </select></td>
      <td>
      <?php if(isset($_SESSION['selected_index_date'])){?>
	<input readonly="true"  size="8" margin="0" type="text" name="datepicker" id="datepicker" placeholder="Select date" onchange="validate_date($(this).val());" value="<?php echo $_SESSION['selected_index_date']?>">  
      <?php }
      else
      {
      ?>
      <input readonly="true"  size="8" margin="0" type="text" name="datepicker" id="datepicker" placeholder="Select date" onchange="validate_date($(this).val());" value="<?=date('Y-m-d')?>">  
      <?php }?>
      </td>
    
    <td>
     <input name="prjname" type="text" id="prjname" size="20" value=""/>
      <input name="project_id" type="hidden" id="project_id" class="project_id" size="20" value=""/>
    </td>
    <td><select name="actname" class="act" id="actname" style="width: 150px;" value="">
     <option value="">Select Activity Name</option>    
   
    </select></td>
    <td><input size="2"  class="txtst" type="text" name="txtst" id="txtst" onchange="checkst();" value="0.0"/>
    <input type="hidden" name="hidetxtst" id="hidetxtst" class="hidetxtst" value="0.0" />
    </td>
    <td><div class="divot" style="display: none;">
            <input size="2" class="txtot" type="text" name="txtot" id="txtot"  onchange="checkot();" value="0.0"/>
            <input type="hidden" name="hidetxtot" id="hidetxtot" class="hidetxtot" value="0.0" />
        </div></td>
    <td><input  class="txtworkhours" type="hidden" name="txtworkhours" id="txtworkhours" readonly="true" value="<?echo $working_hours;?>"/></td>
    <td><input class="otallow" type="hidden" name="otallow" id="otallow" readonly="true" value="<?echo $otallow;?>"/></td>
    <td><input class="holiday" type="hidden" name="holiday" id="holiday" />
        <input class="submitflag" type="hidden" name="submitflag" id="submitflag" value="0"/>
    </td>
     <script type="text/javascript">
         var dt=$('#datepicker').val();
//                        alert(dt);
        $.ajax({
                   url: "db_data.php",
                   async: false,
                   data:"cdate="+dt,
                   success: function(data)
                    {
//                        alert(data);
                        $('.holiday').val(data);
                        if(data==0)
                        {
                            $('.txtst').attr('readonly','readonly');
                            $('.txtst').val("0.0");
                            $('.txtst').removeAttr('style');
                        }
                        else
                        {
                            $('.txtst').removeAttr("readonly");
                        }   
                    }
                 })
     </script> 
    <?
    
    
             if(count($emp_arr) < 2) 
             {
               ?>
                  <script type="text/javascript">
                       $('.divtxtemp').hide();
                       $('.divemp').hide();
                       
                        var id="<?=$emp_arr[0]['emp_number']?>";
//                        var workstation="<?//=$workstation;?>";
                        
                        if(id!="")
                            {
                              $.ajax({
                                   url: "db_data.php",
                                   async: false,
                                   data:"empid="+id,
                                   success: function(data)
                                    {
//                                        alert(data);
                                        var hrot=data.split("/");
                                        $('.txtworkhours').val(hrot[0]);
                                        if(hrot[1]==1)
                                            { 
                                                $('.otallow').val(1);
                                                $('.divtxtot').show();
                                                $('.divot').show();
                                            }
                                        else
                                            {  
                                                $('.otallow').val(0);
                                                $('.txtot').val("0.0");
                                                $('.divtxtot').hide();
                                                $('.divot').hide();

                                            }
                                    }
                                 })
                            }
                            else
                            {
                              $('.otallow').val(0);
                             
                            }
                        
                  </script>
               <?
              }
              else
              {
                  ?><script type="text/javascript"> 
                  $('.divtxtemp').show();
                   $('.divemp').show();
                   </script> 
              <?}?>
<!--Changed By = Rushika
    Changes = Change the value of submit button from Submit to Save
-->
   <td> <input type="submit" class="submitbutton"  value="Save" name="save"/>
       <input type="reset" class="submitbutton" value="Reset" name="reset" onclick="resetact();"/></td></tr>
</table>
</form>
</div>
<?}?>

        <div align='right' style="height:0px; width:100%;" id="orangehrm_updates"></div>	

        
<!--	jagruti <div id="rightHeaderImage"></div>-->
        
        
<?php $menuObj->getMenu($menu, $optionMenu, $welcomeMessage); ?>

        <div id="main-content" style="float:left;height:640px;text-align:center;padding-left:0px;">
            <iframe style="display:block;margin-left:auto;margin-right:auto;width:100%;" src="<?php echo $home; ?>" id="rightMenu" name="rightMenu" height="100%;" frameborder="0"></iframe>

        </div>

        <div id="main-footer" style="clear:both;text-align:center;height:20px;">
            <a href="http://www.inextrix.com" target="_blank">HRM System</a> ver 1.0 &copy; iNextrix Technologies 2010 - 2012 All rights reserved.
        </div>
        <script type="text/javascript">
            //<![CDATA[
            function exploitSpace() {
                dimensions = windowDimensions();
                if (document.getElementById("main-content")) {
                    document.getElementById("main-content").style.height = (dimensions[1]  - 100 - <?php echo $menuObj->getMenuHeight(); ?>) + 'px';
                }

                if (document.getElementById("main-content")) {
                    if (dimensions[0] < 940) {
                        dimensions[0] = 940;
                    }

                    document.getElementById("main-content").style.width = (dimensions[0] - <?php echo $menuObj->getMenuWidth(); ?>) + 'px';
                }
            }

            exploitSpace();
            window.onresize = exploitSpace;
            
//             var xhReq = new XMLHttpRequest();
//             xhReq.open("GET", "orangehrm_updates.php", false);
//             xhReq.send(null);
//             document.getElementById("orangehrm_updates").innerHTML = xhReq.responseText;

            //]]>
        </script>

    
<!--   jagruti script start-->
<script>
function validate_date(dt)
{
    $.ajax({
           url: "db_data.php",
           async: false,
           data:"cdate="+dt,
           success: function(data)
            {
//                alert(data);
                $('.holiday').val(data);
                if(data==0)
                {
                    $('.txtst').attr('readonly','readonly');
                    $('.txtst').val("0.0");
                    $('.txtst').removeAttr('style');
                }
                else
                {
                    $('.txtst').removeAttr("readonly");
                } 
            }
         })
    
}
    
    
function getothours(id)
{
//    var workstation="<?//=$workstation;?>";
if(id!="")
    {
      $.ajax({
           url: "db_data.php",
           async: false,
           data:"empid="+id,
           success: function(data)
            {
                var hrot=data.split("/");
                $('.txtworkhours').val(hrot[0]);
                
                if(hrot[1]==1)
                    {
                        $('.otallow').val(1);
                        $('.divtxtot').show();
                        $('.divot').show();
                    }
                else
                    {
                        $('.otallow').val(0);
                        $('.txtot').val("0.0");
                        $('.divtxtot').hide();
                        $('.divot').hide();
                        
                    }
            }
         })
    }
    else
    {
      $('.otallow').val(0);
    }
}
function converttime(tm)
{
    var stot= tm.split(":");
    var hr=stot[0];
    var mn=stot[1];
    //Rushika made changes 20130705 
    if(mn.length==1)
    {
	mn=mn*10;
    }
    //20130705
    var fnlmn=Math.round((mn*100)/60)+"";
    
    if(fnlmn.length==1)
    {
	fnlmn="0"+fnlmn;
    }
    return hr+"."+fnlmn;
}
function checkst()
{
     var errorStyle = "background-color:#FFDFDF;";
    $('#msg').removeAttr('class');
    $('.txtst').removeAttr('style');
    $('#msg').html("");
    
    var flag=true;
    var working_hours=$('.txtworkhours').val();
    working_hours=parseFloat(working_hours);
//    alert(working_hours);
    
    var txtst=$('.txtst').val();
    var txtot=$('.txtot').val();
    if(txtst.indexOf(":") > 0)
     {
       txtst=converttime(txtst);
     }
    else if(txtst.indexOf(".") > 0)
    {
        var stot= txtst.split(".");
        //Rushika made changes 20130705 
        if(stot[1].length==1)
	{
	  stot[1]=stot[1]*10;
	}
        if(stot[0] > working_hours || stot[1] >= 100)
        {
          flag=false;
          $('#msg').html(lang_not_numeric);
          $('.txtst').attr('style', errorStyle);
        }
    }
    else
        {
            txtst=parseFloat(txtst);
            if(txtst > working_hours)
            {
              flag=false;
              $('#msg').html(lang_not_numeric);
              $('.txtst').attr('style', errorStyle);    
            }
        }
    
        if(flag)
        {
            if(txtot.indexOf(":") > 0)
             {
               txtot=converttime(txtot);
             }
            else if(txtot.indexOf(".") > 0)
            {
                var stot= txtot.split(".");
                //Rushika made changes 20130705 
                if(stot[1].length==1)
		{
		    stot[1]=stot[1]*10;
		}
                if(stot[0] > 24 || stot[1] >= 100)
                {
                  flag=false;
                  $('#msg').html(lang_not_numeric);
                  $('.txtot').attr('style', errorStyle);
                }
            }
            else
                {
                    txtot=parseFloat(txtot);
                    if(txtot > 24)
                    {
                      flag=false;
                      $('#msg').html(lang_not_numeric);
                      $('.txtot').attr('style', errorStyle);    
                    }
                }
        }
//        txtot= txtot.replace(/:\s*/g,".");
     
    var st=parseFloat(txtst);
    var ot=parseFloat(txtot);
    var otallow=$('.otallow').val();
    
    
    var dt=$('#datepicker').val();
    var empid=$('#empname').val();
    var dbst=0;
    var dbot=0;
    
    $.ajax({
           url: "db_data.php",
           async: false,
           data:"emp_id="+empid+"&date="+dt,
           success: function(data)
            {
//                alert(data);
                var hrot=data.split("/");
                dbst=parseFloat(hrot[0]);
                dbot=parseFloat(hrot[1]);
            }
         })
    
    st=st+dbst;
    ot=ot+dbot;
//    alert(st+"->"+working_hours+"->"+otallow);
    if(flag)
    { 
//        alert("inn");
        if(st >= working_hours && otallow==1)
        {
            $('.txtot').removeAttr("readonly");
        }
        else
        {
             $('.txtot').attr('readonly','readonly');
             $('.txtot').val("0.0");
             $('.txtot').removeAttr('style');
             ot=0;
        }
    }
    
    var total=st+ot;
//    alert(total);
    if(flag)
    {
         if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test(txtst))
         {
            var temp = $('.txtst').val().split(":");
            if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[0]) || !(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[1])){
                $('#msg').html(lang_not_numeric);
                $('.txtst').attr('style', errorStyle);
                flag=false

            }else if(temp[0]>23 || temp[1]>59){
                $('#msg').html(lang_not_numeric);
                $('.txtst').attr('style', errorStyle);
                flag=false
            }
        }
    }
    if(flag)
    {
        if(st > working_hours) {
            flag=false
            $('#msg').html(lang_not_numeric);
            var errorStyle = "background-color:#FFDFDF;";
            $('.txtst').attr('style', errorStyle);
            $('.txtot').attr('readonly','readonly');
            $('.txtot').val("0.0");
            $('.txtot').removeAttr('style');
            ot=0;
        }
    }
    if(flag)
    {
       if(total > 24)
            {
                flag=false
               $('#msg').html(incorrect_total);
                var errorStyle = "background-color:#FFDFDF;";
                $('.txtst').attr('style', errorStyle);
                 
            } 
    }
    if(flag)
        {
           $('.hidetxtst').val(txtst);
        }
}
function checkot()
{
    var working_hours=$('.txtworkhours').val();
    var errorStyle = "background-color:#FFDFDF;";
    $('#msg').removeAttr('class');
    $('.txtot').removeAttr('style');
    $('#msg').html("");
    var flag=true;
    
    var working_hours=$('.txtworkhours').val();
    working_hours=parseFloat(working_hours);
//    alert(working_hours);
    
    var txtst=$('.txtst').val();
    var txtot=$('.txtot').val();
    if(txtot.indexOf(":") > 0)
     {
       txtot=converttime(txtot);
     }
    else if(txtot.indexOf(".") > 0)
    {
        var stot= txtot.split(".");
        //Rushika made changes 20130705 
        if(stot[1].length==1)
	{
	  stot[1]=stot[1]*10;
	}
        if(stot[0] > 24 || stot[1] >= 100)
        {
          flag=false;
          $('#msg').html(lang_not_numeric);
          $('.txtot').attr('style', errorStyle);
        }
    }
    else
        {
            txtot=parseFloat(txtot);
            if(txtot > 24)
            {
              flag=false;
              $('#msg').html(lang_not_numeric);
              $('.txtot').attr('style', errorStyle);    
            }
        }
    
    if(flag)
    {
        if(txtst.indexOf(":") > 0)
         {
           txtst=converttime(txtst);
         }
        else if(txtst.indexOf(".") > 0)
        {
            var stot= txtst.split(".");
            //Rushika made changes 20130705 
            if(stot[1].length==1)
	    {
	      stot[1]=stot[1]*10;
	    }
            if(stot[0] > working_hours || stot[1] >= 100)
            {
              flag=false;
              $('#msg').html(lang_not_numeric);
              $('.txtst').attr('style', errorStyle);
            }
        }
        else
            {
                txtst=parseFloat(txtst);
                if(txtst > working_hours)
                {
                  flag=false;
                  $('#msg').html(lang_not_numeric);
                  $('.txtst').attr('style', errorStyle);    
                }
            }
    }
    
     
    var st=parseFloat(txtst);
    var ot=parseFloat(txtot);
    
   
    
    
    var dt=$('#datepicker').val();
    var empid=$('#empname').val();
    var dbst=0;
    var dbot=0;
    
    $.ajax({
           url: "db_data.php",
           async: false,
           data:"emp_id="+empid+"&date="+dt,
           success: function(data)
            {
//                alert(data);
                var hrot=data.split("/");
                dbst=parseFloat(hrot[0]);
                dbot=parseFloat(hrot[1]);
            }
         })
    
    
        
    st=st+dbst;
    ot=ot+dbot;
  
    
 
    var otallow=$('.otallow').val();
    var holiday=$('.holiday').val();
    if(flag)
       {
           if(holiday==0)
           {
//                   alert("holiday->"+holiday);
                     $('.txtst').attr('readonly','readonly');
                     $('.txtst').val("0.0");
                     $('.txtst').removeAttr('style');
                     st=0;
           }
           else
           {
                if(st >= working_hours && otallow==1)
                {
                    $('.txtot').removeAttr("readonly");
                }
                else
                {
                     $('.txtot').attr('readonly','readonly');
                     $('.txtot').val("0.0");
                     $('.txtot').removeAttr('style');
                     ot=0;
                }
           }
       }  
     
      var total=st+ot;
    if(flag)
    {
         if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test(txtot))
         {
            var temp = $('.txtot').val().split(":");
            if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[0]) || !(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[1])){
                $('#msg').html(lang_not_numeric);
                $('.txtot').attr('style', errorStyle);
                flag=false

            }else if(temp[0]>23 || temp[1]>59){
                $('#msg').html(lang_not_numeric);
                $('.txtot').attr('style', errorStyle);
                flag=false
            }
        }
    }
    if(flag)
    {
        if(ot > 24) {
            flag=false
            $('#msg').html(lang_not_numeric);
            var errorStyle = "background-color:#FFDFDF;";
            $('.txtot').attr('style', errorStyle);
            ot=0;
        }
    }
    if(flag)
    {
       if(total > 24)
            {
                flag=false
               $('#msg').html(incorrect_total);
                var errorStyle = "background-color:#FFDFDF;";
                $('.txtot').attr('style', errorStyle);
                 
            } 
    }
    if(flag)
        {
           $('.hidetxtot').val(txtot);
        }
    
}
function resetact()
{
    $('.act').empty();
    $('#actname').append($('<option/>').attr("value","").text("Select Activity"));
    
}

function validate()
{
//  if(document.getElementById('datepicker').value < "2013-07-01")
//   {
//     alert( "Please select date after July...!" );
//     return false;
//   }
   if($('#msg').html()!="")
       {
       return false; 
       }
   var drp_val=$('#empname').val();

   if(drp_val=="")
   {
     alert( "Please select employee name!" );
     return false;
   }
   if(document.getElementById('datepicker').value == "" )
   {
     alert( "Please select date!" );
     return false;
   }
   if(document.getElementById('prjname').value=="")
   {
     alert( "Please select project name!" );

     return false;
   }
   if(document.getElementById('actname').selectedIndex == 0 )
   {
     alert( "Please select activity name!" );
     return false;
   }
   if((document.getElementById('txtst').value == "0" || document.getElementById('txtst').value == "" || document.getElementById('txtst').value == "0.0" || document.getElementById('txtst').value == "0:0")  && (document.getElementById('txtot').value == "0" || document.getElementById('txtot').value == "" || document.getElementById('txtot').value == "0.0" || document.getElementById('txtot').value == "0:0"))  {
     alert( "Please Enter ST or OT value!" );
     return false;
   }
   
 $('.submitflag').val("1");
     return true;
}
  
$( "#datepicker" ).datepicker({
    dateFormat: 'yy-mm-dd',
    maxDate: 0
});
</script>


    </body>
</html>
<?php ob_end_flush(); ?>

    