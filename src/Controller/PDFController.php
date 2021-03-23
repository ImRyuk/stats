<?php

namespace App\Controller;

use App\Entity\Departement;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PDFController extends AbstractController
{
    /**
     * @Route("/Generate_PDF/{id}", name="generate_PDF")
     * @param $id
     */
    public function DomPDF($id)
    {
        $departement = $this->getDoctrine()->getRepository(Departement::class)->find($id);

        if($departement->getOldRegion() == 'comgend' || is_null($departement->getOldRegion()))
        {
            $ecussonPath = 'C:\laragon\www\Statistiques\public\images\ecussons\\' . $departement->getName() . '.png';
        } else{
            $ecussonPath = 'C:\laragon\www\Statistiques\public\images\ecussons\\' . $departement->getOldRegion() . '.png';
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->getOptions()->setChroot("C:\\laragon\\www\\Statistiques\\public\\");

        $logoMarianne = 'C:\laragon\www\Statistiques\public\images\logo-sp-plus.png';


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf/generator.html.twig', [
            'ecussonPath' => $ecussonPath,
            'departement' => $departement,
            'logoMarianne' => $logoMarianne
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }
}
