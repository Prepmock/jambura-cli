<?php
include __DIR__.'/../../src/Command.php';
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
