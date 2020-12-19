<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', TRUE);
/*
|--------------------------------------------------------------------------
| Check AJAX
|--------------------------------------------------------------------------
|
| Set the server request for ajax calling
|
*/
defined('IS_AJAX') or define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
defined('DIRECT_ACCESS_MSG') or define('DIRECT_ACCESS_MSG', 'No direct script access allowed.');
/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish ( or even need ) to change the values in
| certain environments ( Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc. ).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb');
// truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b');
// truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library ( stdlibc ):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       ( This link also contains other GNU-specific conventions )
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section = 3&topic = sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/

defined('ADMIN_NAME')      or define('ADMIN_NAME', 'admin');
// highest automatically-assigned error code
defined('ADMIN_EMAIL')     or define('ADMIN_EMAIL', 'parrotdipankarweb@gmail.com');
//srvbera@gmail.com
defined('SUPPORTEMAIL')    or define('SUPPORTEMAIL', 'info@parrotdipankar.com');
//defined( 'SITEMOBILE' )    OR define( 'SITEMOBILE', '9903638848' );
defined('SITEMOBILE')    or define('SITEMOBILE', '9635928755');
defined('THEME') or define('THEME', 'theme1');
defined('SITENAME') or define('SITENAME', 'parrotdipankar.com');
defined('UPLOAD_CAT_PATH') or define('UPLOAD_CAT_PATH', 'uploads/category/');
defined('UPLOAD_PROD_PATH') or define('UPLOAD_PROD_PATH', 'uploads/animal/');
defined('UPLOAD_ABOUT_US_USER') or define('UPLOAD_ABOUT_US_USER', 'uploads/about_us_user/');
defined('UPLOAD_BLOG_PATH') or define('UPLOAD_BLOG_PATH', 'uploads/blog/');
defined('UPLOAD_PROG_PATH') or define('UPLOAD_PROG_PATH', 'uploads/programme/');
defined('UPLOAD_EVENT_PATH') or define('UPLOAD_EVENT_PATH', 'uploads/event/');
defined('UPLOAD_GALLERY_PATH') or define('UPLOAD_GALLERY_PATH', 'uploads/gallery/');