<?php

include('rake.php');

$rake = new Rake('stoplist_smart.txt');
$text = "Criteria of compatibility of a system of linear Diophantine equations, strict inequations, and nonstrict inequations are considered. Upper bounds for components of a minimal set of solutions and algorithms of construction of minimal generating sets of solutions for all types of systems are given.";
$phrases = $rake->extract($text);

print_r($phrases);

?>