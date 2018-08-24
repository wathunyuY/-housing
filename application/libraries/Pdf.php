<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require('fpdf.php');
class Pdf extends FPDF
{
	function __construct($orientation='P', $unit='mm', $size='A4')
	{
		parent::__construct($orientation,$unit,$size);
	}
	function Header()
	{
		if($this->PageNo() > 1){
		    $this->AddFont('angsa','','angsa.php');
			$this->AddFont('angsa','B','angsab.php');
			$this->AddFont('angsa','I','angsai.php');
			$this->AddFont('angsa','U','angsaz.php');
			$this->SetFont('angsa','',16);
		    $w = array(10, 20, 30, 40,30,50);
			$header = ["ลำดับ","บ้านเลขที่","หมายเลขอาคาร","รายชื่อเข้าพักอาศัย","สังกัด","หมายเหตุ"];
			for($i=0;$i<count($header);$i++)
			    $this->Cell($w[$i],7,iconv( 'UTF-8','TIS-620',$header[$i]),1,0,'C');
			$this->Ln();
		}
	}
	function Footer()
	{
	    // Position at 1.5 cm from bottom
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Page number
	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
?>