<?php

namespace Egzaminer;

use AltoRouter;
use Egzaminer\Admin\Auth;
use Exception;
use PDO;
use PDOException;
use Tamtamchik\SimpleFlash\Flash;

class App
{
    private $url;
    private $router;
    private $config;
    private $container;

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (!session_id()) {
            @session_start();
        }

        try {
            $configPath = $this->getRootDir().'/config/site.php';
            if (!file_exists($configPath)) {
                throw new Exception('Config file site.php does not exist');
            }
            $this->config = include $configPath;

            $this->container = [
                'auth'    => new Auth(),
                'config'  => $this->config,
                'dbh'     => $this->dbConnect(include $this->getRootDir().'/config/db.php'),
                'dir'     => $this->getDir(),
                'flash'   => new Flash(),
                'rootDir' => $this->getRootDir(),
            ];
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->terminate();
        }

        $this->router = new AltoRouter();
        $this->setUrl($_SERVER['REQUEST_URI']);
    }

    /**
     * Run app.
     */
    public function invoke()
    {
        $this->router->map('GET', '/', [
            'Egzaminer\Roll\HomePage', 'indexAction', ]);

        $this->router->map('GET', '/group/[i:id]', [
            'Egzaminer\Roll\ExamsGroup', 'indexAction', ]);

        $this->router->map('GET|POST', '/test/[i:id]', [
            'Egzaminer\Exam\Exam', 'showAction', ]);

        $this->router->map('GET', '/admin', [
            'Egzaminer\Admin\Dashboard', 'indexAction', ]);

        $this->router->map('GET', '/admin/login', [
            'Egzaminer\Admin\Login', 'loginAction', ]);
        $this->router->map('POST', '/admin/login', [
            'Egzaminer\Admin\Login', 'postLoginAction', ]);

        $this->router->map('GET', '/admin/logout', [
            'Egzaminer\Admin\Logout', 'logoutAction', ]);

        $this->router->map('GET', '/admin/test/add', [
            'Egzaminer\Exam\ExamAdd', 'addAction', ]);
        $this->router->map('POST', '/admin/test/add', [
            'Egzaminer\Exam\ExamAdd', 'postAddAction', ]);

        $this->router->map('GET', '/admin/test/edit/[i:id]', [
            'Egzaminer\Exam\ExamEdit', 'editAction', ]);
        $this->router->map('POST', '/admin/test/edit/[i:id]', [
            'Egzaminer\Exam\ExamEdit', 'postEditAction', ]);

        $this->router->map('GET', '/admin/test/del/[i:id]', [
            'Egzaminer\Exam\ExamDelete', 'deleteAction', ]);
        $this->router->map('POST', '/admin/test/del/[i:id]', [
            'Egzaminer\Exam\ExamDelete', 'postDeleteAction', ]);

        $this->router->map('GET', '/admin/test/edit/[i:tid]/question/add', [
            'Egzaminer\Question\QuestionAdd', 'addAction', ]);
        $this->router->map('POST', '/admin/test/edit/[i:tid]/question/add', [
            'Egzaminer\Question\QuestionAdd', 'postAddAction', ]);

        $this->router->map('GET', '/admin/test/edit/[i:tid]/question/edit/[i:qid]', [
            'Egzaminer\Question\QuestionEdit', 'editAction', ]);
        $this->router->map('POST', '/admin/test/edit/[i:tid]/question/edit/[i:qid]', [
            'Egzaminer\Question\QuestionEdit', 'postEditAction', ]);

        $this->router->map('GET', '/admin/test/edit/[i:tid]/question/del/[i:qid]', [
            'Egzaminer\Question\QuestionDelete', 'deleteAction', ]);
        $this->router->map('POST', '/admin/test/edit/[i:tid]/question/del/[i:qid]', [
            'Egzaminer\Question\QuestionDelete', 'postDeleteAction', ]);

        $match = $this->router->match($this->url);

        try {
            // call closure or throw 404 status
            if ($match && is_callable($match['target'])) {
                call_user_func_array([
                    new $match['target'][0]($this->container), $match['target'][1],
                ], $match['params']);
            } else {
                throw new Exception('Page not exist! No route match');
            }
        } catch (Exception $e) {
            if ($this->config['debug']) {
                echo $e->getMessage();
            } else {
                (new Error($this->container))->showAction(404);
            }
            $this->terminate();
        }
    }

    private function dbConnect(array $config)
    {
        try {
            $dsn = 'mysql'
            .':dbname='.$config['name']
            .';host='.$config['host']
            .';charset=utf8';

            $user = $config['user'];
            $password = $config['pass'];

            $dbh = new PDO($dsn, $user, $password);

            if ($this->config['debug']) {
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

            return $dbh;
        } catch (PDOException $e) {
            http_response_code(500);

            if ($this->config['debug']) {
                echo $e->getMessage();
            } else {
                echo 'Error 500';
            }
            $this->terminate();
        }
    }

    public function terminate($code = 1)
    {
        exit($code);
    }

    /**
     * Set request url.
     *
     * @param string $request
     */
    public function setUrl($request)
    {
        $basePath = $this->getDir();
        $url = null;

        if ($basePath) {
            $pos = strpos($request, $basePath);

            if (false !== $pos) {
                $url = substr_replace($request, '', $pos, strlen($basePath));
            }
        }
        $this->url = $url;
    }

    /**
     * Get app root dir.
     *
     * @return string
     */
    public static function getRootDir()
    {
        return dirname(__DIR__);
    }

    public function dir()
    {
        return $this->getDir();
    }

    /**
     * Get app dir.
     *
     * @return string
     */
    public static function getDir()
    {
        if (dirname($_SERVER['SCRIPT_NAME']) == '/') {
            return '';
        } else {
            return dirname($_SERVER['SCRIPT_NAME']);
        }
    }
}
