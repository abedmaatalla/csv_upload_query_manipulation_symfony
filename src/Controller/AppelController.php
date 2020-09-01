<?php

namespace App\Controller;

use App\Entity\Appel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class AppelController extends AbstractController
{
    /**
     * @Route("/", name="appel")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {

        $appel_repository = $this->getDoctrine()->getRepository(Appel::class);

        $findAllWithoutDureeTotal = $appel_repository->findAllWithoutDureeTotal('15/02/2012');

        $findAllWithoutDureeTotalPaginator = $paginator->paginate(
            $findAllWithoutDureeTotal,
            $request->query->getInt('page', 1),
            15
        );

        $top_volumes_factures = $appel_repository->top_volumes_factures('08:00', '18:00', 10);

        $top_volumes_facturesPaginator = $paginator->paginate(
            $top_volumes_factures,
            $request->query->getInt('page', 1),
            15
        );
        $totale_sms = $appel_repository->totale_sms();

        $totaleSMSPaginator = $paginator->paginate(
            $totale_sms,
            $request->query->getInt('page', 1),
            15
        );

        return $this->json([
            'duree_total_15_02_2012' => $findAllWithoutDureeTotalPaginator->getItems(),
            'top_volumes_factures-08:00/18:00' => $top_volumes_facturesPaginator->getItems(),
            'totale_sms' => $totaleSMSPaginator->getItems(),
        ]);
    }
}
