<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\Stat;
use App\Entity\StatUnite;
use Endroid\QrCode\QrCode;
use Symfony\Component\Finder\Finder;
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
        $stats = $this->getDoctrine()->getRepository(Stat::class)->findAll();
        $ecussonPath = '';

        if($request->query->get('stat') != null)
        {
            $viewStat = $this->getDoctrine()->getRepository(Stat::class)->find($id);
        } else {
            $viewStat = $stats[0];
        }

        if(empty($viewStat->getGroupement()))
        {
            $ecussonPath = '/ecussons/' . $viewStat->getRegion() . '.png';
        } else if($viewStat->getOldRegion() == 'comgend')
        {
            $ecussonPath = '/ecussons/' . $viewStat->getGroupement() . '.png';
        } else{
            $ecussonPath = '/ecussons/' . $viewStat->getOldRegion() . '.png';
        }

        return $this->render('stats.html.twig', [
            'stats' => $stats,
            'viewStat' => $viewStat,
            'ecussonPath' => $ecussonPath
        ]);
    }
}
