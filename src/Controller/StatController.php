<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Source;
use App\Repository\DepartementRepository;
use App\Repository\SourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {
        $id = $request->query->get('stat');
        $departements = $this->getDoctrine()->getRepository(Departement::class)->findAll();
        $sources = $this->getDoctrine()->getRepository(Source::class)->findAll();

        if($request->query->get('stat') != null)
        {
            $departement = $this->getDoctrine()->getRepository(Departement::class)->find($id);
        } else {
            $departement = $departements[0];
        }

        if($departement->getOldRegion() == 'comgend' || is_null($departement->getOldRegion()))
        {
            $ecussonPath = '/images/ecussons/' . $departement->getName() . '.png';
        } else{
            $ecussonPath = '/images/ecussons/' . $departement->getOldRegion() . '.png';
        }

        return $this->render('stats.html.twig', [
            'departements' => $departements,
            'departement' => $departement,
            'ecussonPath' => $ecussonPath,
            'sources' => $sources
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

}
