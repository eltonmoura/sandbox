#!/usr/bin/php
<?php
function utf8_chr($ascii)
{
    return mb_convert_encoding(pack("n", $ascii), "UTF-8", "UTF-16BE") ;
}
function utf8_ord($ascii)
{
    $char = utf8_chr($ascii) ;
    $code = "";
    for ($i=0; $i < strlen($char); $i++) {
        $code .= sprintf("%02X ", ord($char{$i}));
    }
    return trim($code);
}
function print_utf8($ascii)
{
    $char = utf8_chr($ascii) ;
    if (preg_match("#[\\x00-\\x1F]|\\x7F|(?:\\xC2[\\x80-\\xA0])#", $char)) {
        return htmlentities("<control>");
    }
    return $char ;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Encoding Table</title>
<style type="text/css">
table {
	display: table;
	border-collapse: separate;
	border-spacing: 2px;
	border-color: #f9f9f9;
}
th {
	width: 100px;
}
.row0 {
	background-color: #f9f9f9;
}
.row1 {
	background-color: #ffffff;
}
</style>
</head>
<body>
<table>
<tr>
	<th>Decimal</th>
	<th>Unicode</th>
	<th>htmlentities</th>
	<th>UTF8 Code</th>
	<th>UTF8</th>
</tr>
<?php for ($i=0; $i < 0xFFF; $i++) {
    ?>
<tr class="row<?=$i%2; ?>" >
	<td><?=sprintf("%04d", $i); ?></td>
	<td><?=sprintf("U+%04X", $i); ?></td>
	<td><?=sprintf("&#x%02X;", $i); ?></td>
	<td><?=utf8_ord($i); ?></td>
	<td><?=print_utf8($i); ?></td>
</tr>
<?php
} ?>
</table>
</body>
</html>
