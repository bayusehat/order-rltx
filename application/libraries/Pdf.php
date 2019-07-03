<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Dompdf\Dompdf;

class Pdf
{
	protected $ci;

	public function __construct()
	{
		require_once dirname(__FILE__).'/dompdf/autoload.inc.php';

		$pdf = new DOMPDF();

        $this->ci =& get_instance();
        $this->ci->dompdf = $pdf;
	}

	

}

/* End of file pdf.php */
/* Location: ./application/libraries/pdf.php */
