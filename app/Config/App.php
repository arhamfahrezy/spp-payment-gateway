<?php 
// C:\xampp\htdocs\payment-gateway\app\Config\App.php 
namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    public string $baseURL = 'http://localhost:8080/';

    /**
     * @var list<string>
     */
    public array $allowedHostnames = [];

    public string $indexPage = '';

    public string $uriProtocol = 'REQUEST_URI';

    public string $permittedURIChars = 'a-z 0-9~%.:_\-';

    public string $defaultLocale = 'id';

    public bool $negotiateLocale = false;

    /** * @var list<string> */
    public array $supportedLocales = ['en'];

    /** * @see https://www.php.net/manual/en/timezones.php for list of timezones * */
    public string $appTimezone = 'Asia/Jakarta';

    /** * @see http://php.net/htmlspecialchars for a list of supported charsets. */
    public string $charset = 'UTF-8';

    public bool $forceGlobalSecureRequests = false;

    /** * @var array<string, string> */
    public array $proxyIPs = [];

    /** * @see http://www.html5rocks.com/en/tutorials/security/content-security-policy/
     * @see http://www.w3.org/TR/CSP/ */
    public bool $CSPEnabled = false;
}
