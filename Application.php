<?php
namespace AzizbekIsmoilov\phpmvc;

use AzizbekIsmoilov\phpmvc\db\Database;

/**
 *
 */
class Application
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';
    protected array $eventListeners = [];
    public static Application $app;
    public static string $ROOT_DIR;
    public string $layout = 'main';
    public string $userClass;
    public Response $response;
    public Session $session;
    public Request $request;
    public ?UserModel $user;
    public Router $router;
    public ?Database $db = null;

    public View $view;
    public Controller $controller;

    /**
     * @param $rootPath
     * @param array $config
     */
    public function __construct($rootPath, array $config)
    {
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue ]);
        }else{
            $this->user = null;
        }
    }

    /**
     * @return bool
     */
    public static function isGuest()
    {
        return !self::$app->user;
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try {
            echo  $this->router->resolve();
        } catch (\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error', [
               'exception' => $e
            ]);
        }
    }

    /**
     * @param $eventName
     * @param $callback
     * @return void
     */
    public function on($eventName, $callback)
    {
        $this->eventListeners[$eventName][] = $callback;
    }

    /**
     * @param $eventName
     * @return void
     */
    public function triggerEvent($eventName)
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback) {
            call_user_func($callback);
        }
    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @param UserModel $user
     * @return void
     */
    public function login(UserModel $user)
    {
        $this->user=$user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }
}