<?php
include __DIR__.'/../../src/Command.php';
class test extends \Jambura\Command
{
    public function handle_default()
    {
        $this->debug('starting up');
        $this->info('hello world');
        $this->warning('something to watch');
        $this->error('something went wrong');
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
