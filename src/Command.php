<?php
namespace Jambura;

abstract class Command
{
    private $options;

    private const LOG_LEVELS = [
        'debug'   => 0,
        'info'    => 1,
        'warning' => 2,
        'error'   => 3,
    ];

    private const LOG_COLORS = [
        'debug'   => "\033[0;36m",
        'info'    => "\033[0;32m",
        'warning' => "\033[0;33m",
        'error'   => "\033[0;31m",
    ];

    private const COLOR_RESET = "\033[0m";

    abstract protected function handle_default();

    public function loadParams($options)
    {
        foreach($options as $key => $arg ) {
            if (preg_match( '@\-\-(.+)=(.+)@', $arg, $matches)) {
                $key   = $matches[1];
                $value = $matches[2];
                $this->options[$key] = $value;
            } else if (preg_match( "@\-(.)(.)@", $arg, $matches)) {
                $key   = $matches[1];
                $value = $matches[2];
                $this->options[$key] = $value;
            }
        }

        return $this;
    }

    protected function param($param)
    {
        if (!isset($this->options[$param])) {
            return null;
        }

        return $this->options[$param];
    }

    protected function debug($message)
    {
        $this->log('debug', $message);
    }

    protected function info($message)
    {
        $this->log('info', $message);
    }

    protected function warning($message)
    {
        $this->log('warning', $message);
    }

    protected function error($message)
    {
        $this->log('error', $message);
    }

    private function log($level, $message)
    {
        if (self::LOG_LEVELS[$level] < self::LOG_LEVELS[$this->getLogLevel()]) {
            return;
        }

        $line = sprintf('[%s] %s: %s', date('H:i:s'), strtoupper($level), $message);

        if ($this->colorsSupported()) {
            $line = self::LOG_COLORS[$level].$line.self::COLOR_RESET;
        }

        echo $line."\n";
    }

    private function getLogLevel()
    {
        $level = strtolower($this->param('log-level') ?? 'debug');

        return isset(self::LOG_LEVELS[$level]) ? $level : 'debug';
    }

    private function colorsSupported()
    {
        return function_exists('stream_isatty') ? stream_isatty(STDOUT) : true;
    }
}
