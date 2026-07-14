<?php
namespace Jambura;

class Cli {
    private static $instance;
    
    private $argv;
    private $commandsPath;

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
            ->loadParams(array_slice($this->argv, $index))
            ->$handler();

    }
}
