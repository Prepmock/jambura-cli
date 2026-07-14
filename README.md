# jambura-cli - simple php cli framework
## Usage
1. Create a separate directory for the command classes (eg. applications/commands)
2. Create runner script (eg runner.php)
```
#!/usr/bin/env php
<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

\Jambura\Cli::init()
    ->setCommandsPath('path/to/my/commands/direcoty')
    ->run();
```
3. Add my first command -
```
<?php

class test extends \Jambura\Command
{
    public function handle_default()
    {
        echo "hello world\n";
    }

    public function handle_hello()
    {
      echo "hey";

      if ($user = $this->param('im')) {
        if ($user == 'girl') {
            echo " beautiful\n";
            return;
        }

        echo " handsome\n";
        return;
      }

      echo "\n";
    }
}
```
4. Run command
```
> php runner.php test 
hello world
> php runner.php test hello
hey
> php runner.php test hello --im=girl
hey beautiful
```

## Logging

Every command has `debug()`, `info()`, `warning()` and `error()` available, printing a colored, timestamped line to the screen (colors are skipped automatically when output isn't a terminal, e.g. when piped to a file):

```php
public function handle_default()
{
    $this->debug('starting up');
    $this->info('hello world');
    $this->warning('something to watch');
    $this->error('something went wrong');
}
```

By default all levels are shown. Restrict what's printed at runtime with `--log-level=`:

```
> php runner.php test --log-level=error
[10:32:01] ERROR: something went wrong
```

Levels, from least to most severe: `debug`, `info`, `warning`, `error`. Only messages at or above the chosen level are printed.

### Custom log handler

To forward log entries somewhere else (a file, a remote service, etc.) in addition to the screen, implement `\Jambura\LogHandlerInterface` and register it on the runner with `setLogHandler()`:

```php
class FileLogHandler implements \Jambura\LogHandlerInterface
{
    public function handle(string $level, string $message, string $time): void
    {
        file_put_contents('cli.log', "[$time] ".strtoupper($level).": $message\n", FILE_APPEND);
    }
}

\Jambura\Cli::init()
    ->setCommandsPath('path/to/my/commands/direcoty')
    ->setLogHandler(new FileLogHandler())
    ->run();
```

`setLogHandler()` is optional and only accepts objects implementing `LogHandlerInterface`; every entry that passes the `--log-level` threshold is passed to it alongside the built-in screen output.
