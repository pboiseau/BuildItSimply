<?php

	require('CreatePDF.php');
   



   $data = array
   (
      "ID_PRESTATAIRE"           => "Bernard VONG",
      "QUALIFICATION"            => "développeur back",
      "P_ADRESSE"                => "32 rue du Progrès, 93100 Montreuil",
      "ID_ENTREPRISE"            => "SYNERG'HETIC",
      "E_ADRESSE"                => "23 rue du Progrès, 93100 Montreuil",
      "REPRESENTANT_ENTREPRISE"     => "Mr.François PUMIR",
      "X_TEMPS"                  => "3 semaines",
      "PRIX_HT"                  => "2000",
      "PRIX_TTC"                 => "3000"
   );


   $data["paragraphe1"] = $data['ID_PRESTATAIRE'].', '.$data['QUALIFICATION'].' indépendant, dont le siège social est situé au '.$data['P_ADRESSE'].', immatriculée au Registre du Commerce et des Sociétés représentée par '.$data['ID_PRESTATAIRE'].' habilité à signer les présentes';
   $data["paragraphe2"] = 'La société '.$data['ID_ENTREPRISE'].', dont le siège social est situé au '.$data['E_ADRESSE'].' immatriculée au Registre du Commerce et des Sociétés, représentée par '.$data['REPRESENTANT_ENTREPRISE'].' habilité à signer les présentes';




	$pdfItem = new CreatePDF();

	// Import PDF
	$pageCount = $pdfItem->pdf->setSourceFile('Contrat_freelance_vierge.pdf');
   // Generer page par page
	for($page = 1; $page<=$pageCount; $page++)
	{	
		$tplId = $pdfItem->pdf->importPage($page);
		$pdfItem->pdf->addPage();
		$pdfItem->pdf->useTemplate($tplId, 0, 0, 200); 

      // Si la page actuel est la première, la seconde, etc...
      if($page == 1)
      {
         $pdfItem->writePage(24, 90, "Entre les SOUSSIGNES");

         $pdfItem->addParagraph(24, 105, $data['paragraphe1']);
         $pdfItem->writePage(100, 125, "Ci-après dénommée « le Concepteur »");

         $pdfItem->writePage(151, 140, "D'UNE PART,");
         $pdfItem->writePage(171, 145, "ET");

         $pdfItem->addParagraph(24, 165, $data['paragraphe2']);
         $pdfItem->writePage(100, 185, "Ci-après dénommée « le Client");

         $pdfItem->writePage(148, 205, "D'AUTRE PART");
      }
      else if($page == 2)
         $pdfItem->writePage(84, 63.5, $data['X_TEMPS']);
      else if($page == 4)
      {
         $pdfItem->writePage(53, 49.5, $data['PRIX_HT']);
         $pdfItem->writePage(86, 49.5, $data['PRIX_TTC']);
      }
	}

	$pdfItem->writePDF();
	$pdfItem->showPDF();

?>
