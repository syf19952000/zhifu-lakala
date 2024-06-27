<?php
$control = file_get_contents('copy.json');

$control += 1;

file_put_contents('copy.json', $control);