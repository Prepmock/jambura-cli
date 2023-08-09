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
hey beutiful
```
