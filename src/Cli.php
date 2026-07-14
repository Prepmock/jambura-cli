<?php
namespace Jambura;

class Cli {
    private static $instance;
    
    private $argv;
    private $commandsPath;
    private $logHandler;

    private function __construct()
    {
        $this->argv = $_SERVER['argv'];
    }

    public static function init()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setCommandsPath(string $path)
    {
        $this->commandsPath = $path;
        return $this;
    }

    /**
     * Register a handler to receive every log entry emitted by commands,
     * in addition to the built-in colored screen output.
     *
     * @param LogHandlerInterface $logHandler
     *
     * @return static
     */
    public function setLogHandler(LogHandlerInterface $logHandler)
    {
        $this->logHandler = $logHandler;
        return $this;
    }

    public function run()
    {
        $index = 2;
        $commandName = $this->argv[1];
        $action = $this->argv[2] ?? null;
        if ($action !== null && strncmp($action, '-', 1) === 0) {
            $action = null;
        }

        $handler = $action !== null ? 'handle_'.$action : 'handle_default';
        if ($action !== null) {
          $index++;
        }

        include($this->commandsPath.$commandName.'.php');

        $command = new $commandName();
        $command
            ->setLogHandler($this->logHandler)
            ->loadParams(array_slice($this->argv, $index))
            ->$handler();

    }
}
