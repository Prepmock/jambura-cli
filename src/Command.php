<?php
namespace Jambura;

abstract class Command
{
    private $options;

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
}
