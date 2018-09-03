<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require('fpdf.php');
class Pdf_nohead extends FPDF
{
	function __construct($orientation='P', $unit='mm', $size='A4')
	{
		parent::__construct($orientation,$unit,$size);
	}

}
?>