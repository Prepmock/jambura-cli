<?php

$short_options = "f:gc::b::";
$long_options = ["filename:", "grayscale", "contrast::", "brightness::"];
$options = getopt($short_options, $long_options);

print_r($options);
