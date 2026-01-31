<?php

namespace App\Security;

use App\Service\Environment;
use Symfony\Component\HttpFoundation\Request;

class SafeLogin
{
    private $request;
    private string $safeLogin;
    private string $requestUri;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->requestUri = $this->request->getRequestUri();

        $safeLogin = Environment::get('WP_LOGIN_URL', '');

        if($safeLogin) {
            $this->safeLogin = $safeLogin;

            add_action('init', [$this, 'LoginUrl']);
            add_action('login_url', [$this, 'changeLoginUrl']);
        }

        add_action('init', [$this, 'adminLoginRedirect']);
        add_action('template_redirect', [$this, 'templateRedirect']);
    }

    public function loginUrl(): void
    {
        add_rewrite_rule('^'. $this->safeLogin .'/?$', 'core/wp-login.php');

        if ($this->request->isMethod('GET')
            && str_contains($this->requestUri, 'wp-login.php')
            && !is_user_logged_in()
        ) { $this->set404(); }
    }

    public function changeLoginUrl(): string
    {
        return home_url($this->safeLogin);
    }

    public function adminLoginRedirect(): void
    {
        if(str_contains($this->requestUri, 'wp-admin')
            && !is_user_logged_in()
        ) { $this->set404(); }
    }

    public function templateRedirect(): void
    {
        if(preg_match('#^/admin/?$#', $this->requestUri)) {
            $this->set404();
        }
    }

    private function set404()
    {
        global $wp_query;

        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        exit;
    }
}
