<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Repository\DepartementRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatController extends AbstractController
{

    /**
     * @Route("/", name="view_stat")
     */
    public function index(Request $request): Response
    {
        $id = $request->query->get('stat');
        $departements = $this->getDoctrine()->getRepository(Departement::class)->findAll();

        if($request->query->get('stat') != null)
        {
            $departement = $this->getDoctrine()->getRepository(Departement::class)->find($id);
        } else {
            $departement = $departements[0];
        }

        if($departement->getOldRegion() == 'comgend' || is_null($departement->getOldRegion()))
        {
            $ecussonPath = '/ecussons/' . $departement->getName() . '.png';
        } else{
            $ecussonPath = '/ecussons/' . $departement->getOldRegion() . '.png';
        }

        return $this->render('stats.html.twig', [
            'departements' => $departements,
            'departement' => $departement,
            'ecussonPath' => $ecussonPath
        ]);
    }

    /**
     * @param DepartementRepository $repo
     * @return Response
     * @Route("/departements", name="departements", methods={"GET"})
     */
    public function getDepartements(DepartementRepository $repo)
    {
        $departements = $repo->findAll();

        $response = new JsonResponse();

        if (!$departements){
            $data = [
                'status' => 404,
                'errors' => "No departements found",
            ];
            $response->setStatusCode(404);
            $response->setData($data);
            return $response;
        }
        $response->setData($departements);
        return $response;
    }

    /**
     * @param DepartementRepository $repo
     * @param $code
     * @return JsonResponse
     * @Route("/departement/{code}", name="departement", methods={"GET"})
     */
    public function getDepartement(DepartementRepository $repo, $code){

        $response = new JsonResponse();

        $departement = ($code == 0 ? $departement = $repo->findOneBy([
            'name' => 'National'
        ]) : $departement = $repo->findOneBy([
            'code' => $code
        ]));

        if (!$departement){
            $data = [
                'status' => 404,
                'errors' => "Departement not found",
            ];
            $response->setStatusCode(404);
            $response->setData($data);
            return $response;
        }
        $response->setData($departement);

        return $response;
    }

    /**
     * @Route("/Generate_PDF/{id}", name="generate_PDF")
     * @param $id
     */
    public function DomPDF($id)
    {
        $departement = $this->getDoctrine()->getRepository(Departement::class)->find($id);

        if($departement->getOldRegion() == 'comgend' || is_null($departement->getOldRegion()))
        {
            $ecussonPath = 'C:\laragon\www\Statistiques\public\ecussons\\' . $departement->getName() . '.png';
        } else{
            $ecussonPath = 'C:\laragon\www\Statistiques\public\ecussons\\' . $departement->getOldRegion() . '.png';
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->getOptions()->setChroot("C:\\laragon\\www\\Statistiques\\public");


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('index.html.twig', [
            'title' => "Welcome to our PDF Test",
            'ecussonPath' => $ecussonPath,
            'departement' => $departement
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
