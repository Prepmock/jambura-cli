<?php
namespace Jambura;

/**
 * Receives every log entry emitted by a command via debug()/info()/warning()/error(),
 * alongside the built-in colored screen output.
 */
interface LogHandlerInterface
{
    /**
     * Handle a single log entry.
     *
     * @param string $level   One of 'debug', 'info', 'warning', 'error'.
     * @param string $message The log message.
     * @param string $time    Time the entry was logged, formatted as H:i:s.
     *
     * @return void
     */
    public function handle(string $level, string $message, string $time): void;
}
