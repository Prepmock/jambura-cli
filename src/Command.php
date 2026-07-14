<?php
namespace Jambura;

/**
 * Base class for CLI commands.
 *
 * Subclasses implement handle_default() plus any number of handle_<action>()
 * methods, and get parsed CLI options and leveled, colored logging for free.
 */
abstract class Command
{
    /**
     * Parsed CLI options, keyed by option name.
     *
     * @var array<string, string>
     */
    private $options;

    /**
     * Optional handler that receives every log entry, alongside the
     * built-in colored screen output.
     *
     * @var LogHandlerInterface|null
     */
    private $logHandler;

    /**
     * Numeric severity, lowest to highest, for each supported log level.
     *
     * @var array<string, int>
     */
    private const LOG_LEVELS = [
        'debug'   => 0,
        'info'    => 1,
        'warning' => 2,
        'error'   => 3,
    ];

    /**
     * ANSI color escape codes for each supported log level.
     *
     * @var array<string, string>
     */
    private const LOG_COLORS = [
        'debug'   => "\033[0;36m",
        'info'    => "\033[0;32m",
        'warning' => "\033[0;33m",
        'error'   => "\033[0;31m",
    ];

    /**
     * ANSI escape code to reset terminal color after a log line.
     *
     * @var string
     */
    private const COLOR_RESET = "\033[0m";

    /**
     * Handle the command when no sub-action was given on the command line.
     *
     * @return void
     */
    abstract protected function handle_default();

    /**
     * Parse raw CLI arguments into the options map read by param().
     *
     * Supports two forms: `--key=value` and the two-character short
     * form `-xy`, where `x` is treated as the key and `y` as the value.
     *
     * @param string[] $options Raw argv entries following the command/action.
     *
     * @return static Returns $this for method chaining.
     */
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

    /**
     * Register a handler to receive log entries emitted via
     * debug()/info()/warning()/error().
     *
     * @param LogHandlerInterface|null $logHandler
     *
     * @return static Returns $this for method chaining.
     */
    public function setLogHandler(?LogHandlerInterface $logHandler)
    {
        $this->logHandler = $logHandler;
        return $this;
    }

    /**
     * Get a parsed CLI option value by key.
     *
     * @param string $param Option key, as parsed by loadParams().
     *
     * @return string|null The option value, or null if it was not provided.
     */
    protected function param($param)
    {
        if (!isset($this->options[$param])) {
            return null;
        }

        return $this->options[$param];
    }

    /**
     * Log a debug-level message.
     *
     * @param string $message Message to print.
     *
     * @return void
     */
    protected function debug($message)
    {
        $this->log('debug', $message);
    }

    /**
     * Log an info-level message.
     *
     * @param string $message Message to print.
     *
     * @return void
     */
    protected function info($message)
    {
        $this->log('info', $message);
    }

    /**
     * Log a warning-level message.
     *
     * @param string $message Message to print.
     *
     * @return void
     */
    protected function warning($message)
    {
        $this->log('warning', $message);
    }

    /**
     * Log an error-level message.
     *
     * @param string $message Message to print.
     *
     * @return void
     */
    protected function error($message)
    {
        $this->log('error', $message);
    }

    /**
     * Print a log line if its level meets the configured --log-level threshold,
     * and forward it to the registered log handler, if any.
     *
     * @param string $level   One of the keys in LOG_LEVELS ('debug', 'info', 'warning', 'error').
     * @param string $message Message to print.
     *
     * @return void
     */
    private function log($level, $message)
    {
        if (self::LOG_LEVELS[$level] < self::LOG_LEVELS[$this->getLogLevel()]) {
            return;
        }

        $time = date('H:i:s');
        $line = sprintf('[%s] %s: %s', $time, strtoupper($level), $message);

        if ($this->colorsSupported()) {
            $line = self::LOG_COLORS[$level].$line.self::COLOR_RESET;
        }

        echo $line."\n";

        if ($this->logHandler !== null) {
            $this->logHandler->handle($level, $message, $time);
        }
    }

    /**
     * Get the minimum log level to print, from the --log-level CLI option.
     *
     * Falls back to 'debug' (show everything) if the option is absent
     * or set to an unrecognized level.
     *
     * @return string One of the keys in LOG_LEVELS.
     */
    private function getLogLevel()
    {
        $level = strtolower($this->param('log-level') ?? 'debug');

        return isset(self::LOG_LEVELS[$level]) ? $level : 'debug';
    }

    /**
     * Determine whether ANSI colors should be written to stdout.
     *
     * @return bool True when stdout is an interactive terminal.
     */
    private function colorsSupported()
    {
        return function_exists('stream_isatty') ? stream_isatty(STDOUT) : true;
    }
}
