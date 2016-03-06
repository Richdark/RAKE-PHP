# RAKE PHP

PHP implementation of Rapid Automatic Keyword Exraction algorithm (RAKE) for extracting multi-word phrases from text.

As described in:

ROSE, Stuart, et al. Automatic keyword extraction from individual documents. Text Mining, 2010, 1-20.

With help of Python implementation - [RAKE](https://github.com/aneesha/RAKE)

## Version

0.1

## Example

Example use of Rake class.

### Code
```
<?php

include('rake.php');

$rake = new Rake('stoplist_smart.txt');
$text = "Criteria of compatibility of a system of linear Diophantine equations, strict inequations, and nonstrict inequations are considered. Upper bounds for components of a minimal set of solutions and algorithms of construction of minimal generating sets of solutions for all types of systems are given.";
$phrases = $rake->extract($text);

print_r($phrases);

?>
```
### Output
```
Array
(
    [linear diophantine equations] => 9
    [minimal generating sets] => 8.5
    [minimal set] => 4.5
    [nonstrict inequations] => 4
    [upper bounds] => 4
    [strict inequations] => 4
    [construction] => 1
    [compatibility] => 1
    [types] => 1
    [systems] => 1
    [algorithms] => 1
    [solutions] => 1
    [considered] => 1
    [criteria] => 1
    [components] => 1
    [system] => 1
)
```

License
-------

Released under MIT license (read license.txt).
