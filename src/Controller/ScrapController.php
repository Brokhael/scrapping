<?php

namespace App\Controller;

use App\Model\ScraperModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class ScrapController extends AbstractController
{
    /**
     * @Route("/", name="scrap")
     */
    public function index(): Response
    {
        return $this->render('scrap/index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @Route("/buscar", name="scrap_buscar")
     */
    public function buscarGoogle(Request $request, SessionInterface $session)
    {
        $string = $request->get('string');
        $url = 'https://www.google.com/search?q=';
        $searchString = str_replace(' ', '+', $string);
        $html = file_get_contents($url.$searchString);
        $scrapper = new ScraperModel();
        $urlsFinal = $scrapper->webCounter($html);
        $scrapper->guardaRegistros($urlsFinal, $session);

        return $this->render(
            'scrap/result.html.twig',
            [
                'lista' => $urlsFinal,
                'string' => $string,
            ]
        );
    }
}
