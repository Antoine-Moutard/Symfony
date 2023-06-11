<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }               





    #[Route('/profile', name: 'app_profile')]
    public function profile(ChartBuilderInterface $chartBuilder): Response
    {
        $user = $this->getUser();
        $likedSongs = $user->getLikedSongs();
        $genres = array();
        $label = array();
        $data = array();
        $colors = array();
        foreach ($likedSongs as $likedSong) {
            $genre = $likedSong->getGenre();
            array_push($genres, $genre->getName());
            array_push($colors, $genre->getColor());
        }
        $iteration = array_count_values($genres);
        $colors = array_unique($colors);
        $colors = array_values(array_filter($colors));
        $genres = array_unique($genres);

        foreach ($genres as $genre) {
            array_push($label,$genre);
            array_push($data, $iteration[$genre]);
        }


        

        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

    $chart->setData([
        'labels' => $label,
        'datasets' => [
            [
                'label' => 'Most liked genres', 
                'data' => $data,
                'backgroundColor' => $colors,
                'borderColor' => 'rgba(255, 0, 0, 0)',
            ],
        ],
    ]);

        return $this->render('user/index.html.twig', [
            'chart' => $chart
        ]);
    }

}
