<?php

namespace NGFramer\NGFramerPHPBase;

use NGFramer\NGFramerPHPBase\event\EventManager;
use NGFramer\NGFramerPHPBase\middleware\MiddlewareManager;
use NGFramer\NGFramerPHPDbService\Database;

class Application
{
    // Initialization of the variables used across the application.
    public static Application $application;
    public Request $request;
    public Router $router;
    public Controller $controller;
    public ?Database $database;
    public MiddlewareManager $middlewareManager;
    public EventManager $eventManager;

    public Session $session;
    public Response $response;
    public AppRegistry $appRegistry;


    // Instantiation of the __construct function.

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        self::$application = $this;
        $this->request = new Request();
        $this->router = new Router($this, $this->request);
        $this->controller = new Controller($this);
        // Get the database connection here.
        $this->getDatabaseClass();
        $this->middlewareManager = new MiddlewareManager();
        $this->eventManager = new EventManager();
        $this->session = new Session();
        $this->response = new Response();
        $this->appRegistry = new AppRegistry($this);
        // Get all the routes, middlewares, and events.
        $this->getAppRegistry();
    }


    // Create the database connection.
    private function getDatabaseClass(): void
    {
        $databaseFile = ROOT . '/vendor/ngframer/ngframer.php.dbservice/Database.php';
        if (file_exists($databaseFile)) {
            $this->database = new \NGFramer\NGFramerPHPDbService\Database();
        } else $this->database = null;
    }


    // Get the AppRegistry class to get the route, middleware, and event related data.
    /**
     * @throws \Exception
     */
    private function getAppRegistry(): void
    {
        // Check if the default AppRegistry.php file exists.
        if (!file_exists(ROOT . '/vendor/ngframer/ngframer.php.base/defaults/AppRegistry.php')) {
            throw new \Exception('AppRegistry.php file not found.');
        } else {
            require_once ROOT . '/vendor/ngframer/ngframer.php.base/defaults/AppRegistry.php';
        }

        // Check if the custom AppRegistry.php file exists in the root directory.
        if (file_exists(ROOT . '/AppRegistry.php')) {
            require_once ROOT . '/AppRegistry.php';
        }
    }


    // Run the application by first looking are the request.

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        // Route the request to the controller and get the response
        $this->router->handleRoute();
    }
}
