 <?php
/**
 * Long docblock
 *
 * @with some {@link} inclusions and other ugly stuff
 * User: Paweł
 * Date: 05.12.13
 * Time: 03:31 (yeah a bunch of random comments, why not)
 */

$string = 'foo';
$object = new stdClass(); // why not to comment, when you can?
$object-> token = 'kaboom';
$object -> {$string} = "Ba${string}r{$string}B{$object-> token}ar";
//////////////////////////// hehe
 $object2 = clone $object;
/******* /**** /* ***Now that's nasty!***************************/
 $object2-> difference = $object2->foo = 'is near';

if (true || false) {
	foreach (
						array(array(1,2,),

		         array(3,4)) as $row) {
		list($a,) = $row;
		echo $a;
		list ($a,
			$b) = $row;
		echo ($b);
		print ($b);
	}
}

?>Q
Not necessairly html, but with nasty characters: zażółćgęśląjaźńZAŻÓŁĆGĘŚLĄJAŹŃ
S<?php

 class string {

	 private static $val;

	 function __construct ($s) {
	 self::$val = $s;
 }

	 function __toString() {
		 return self::$val;
	 }
 }

function whyBother(array $arr, string $veryUglyTypeDefinition) {
	$tt = 3;
	while ($tt--) print_r($tt . $veryUglyTypeDefinition);
	return (false) or $arr;
}

echo serialize(whyBother(array($object, $object2), new string("Lorem Ipsum!")));
