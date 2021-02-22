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

        var_dump($viewStat->getGroupement());

        //$ecussonFile = ($viewStat->getGroupement ? true : false); // returns true

        //$ecussonFile = 'DisplayBundle/Resources/public/images/ecussons/' . $viewStat->getOldRegion() . '.png';
        /*$finder = new Finder();
        $finder->files()->in('C:\laragon\www\Statistiques\src\DisplayBundle\Resources\public\images\ecussons')->name($viewStat->getOldRegion() . 'png');
        //var_dump($finder);
        foreach ($finder as $file) {
            // Dump the absolute path
            var_dump($file->getRealPath());
        }
        var_dump($ecussonFile);
        if(file_exists($ecussonFile))
        {
            var_dump('yes the file exists');
        }
        else{
              var_dump('file doesnt exists');
        }*/

        return $this->render('stats.html.twig', [
            'stats' => $stats,
            'viewStat' => $viewStat,
            'ecussonPath' => $ecussonPath
        ]);
    }
}
