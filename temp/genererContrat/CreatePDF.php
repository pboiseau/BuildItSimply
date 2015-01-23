<?php 

	require('lib/fpdf.php');
	require('lib/fpdi.php');

	class CreatePDF
	{

		var $pdf;

		// Instancier la classe
		function __construct()
		{
			$this->pdf = new FPDI();
			$this->pdf->SetTextColor(0,0,0);
		}

		// Ecrire du texte sur la page actuelle
		function writePage($cursorX, $cursorY, $text)
		{
			$this->pdf->SetFont('Times','B',11);
			$this->pdf->SetXY($cursorX, $cursorY);
			$this->pdf->Write(0, utf8_decode($text));
		}

		// Ecrire un paragraphe sur la page actuelle
		function addParagraph($X, $Y, $txt)
		{
			$this->pdf->SetFont('Times','',11);
			$this->pdf->SetRightMargin('30');
			$this->pdf->setXY($X, $Y);
		    // Times 12
		    $this->pdf->SetFont('Times','',11);
		    // Sortie du texte justifié
		    $this->pdf->MultiCell(0,5,utf8_decode($txt));
		    // Saut de ligne
		    $this->pdf->Ln();
		}

		// Sauvegarder le fichier PDF en local
		function writePDF($name='Contrat_freelance_complet.pdf', $src='')
		{
			$this->pdf->Output($name, $src);
		}

		// Afficher le PDF sur le navigateur
		function showPDF()
		{
			$this->pdf->Output();
		}

	}

?>