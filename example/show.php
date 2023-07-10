<?
	$read = $_GET['d'];
	header("Content-type: application/pdf");
	header("Content-Disposition: inline; filename=filename.pdf");
	@readfile($read);
?>