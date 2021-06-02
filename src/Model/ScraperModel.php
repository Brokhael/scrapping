<?php


namespace App\Model;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ScraperModel
{
    public function __construct()
    {
    }

    /**
     * @param string $html
     *
     * @return mixed[]
     */
    public function webCounter(string $html): array
    {
        $links = [];
        if (preg_match_all('/<a href=\"([^\"]*)\">(.*)<\/a>/iU', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                array_push($links, array($match[1], $match[2]));
            }
        }
        $urls = [];
        foreach ($links as $link) {
            $temp = explode(' ', strstr($link[0], 'https://'));
            if ('' !== $temp[0]) {
                $firstClean = substr(explode('&amp', $temp[0])[0], 8);
                $secondClean = explode('/', $firstClean)[0];
                $urls[] = $secondClean;
            }
        }
        $urlsFinal = [];
        foreach ($urls as $item) {
            $temp = explode('.', $item);
            if (3 <=count($temp)) {
                $web = $temp[1];
            } else {
                $web = $temp[0];
            }
            if ('google' !== $web) {
                if (array_key_exists($web, $urlsFinal)) {
                    $urlsFinal[$web]['count']++;
                } else {
                    $urlsFinal[$web] = [
                        'web' => $web,
                        'count' => 1,
                    ];
                }
            }
        }
        uasort($urlsFinal, function ($a, $b) {
            if ($a['count'] == $b['count']) {
                return 0;
            }
            return ($a['count'] > $b['count']) ? -1 : 1;
        });

        return $urlsFinal;
    }

    public function guardaRegistros(array $links, SessionInterface $session)
    {
        foreach ($links as $link) {
            if ('' !== $session->get($link['web'])) {
                $session->set($link['web'], $session->get($link['web']) + $link['count']);
            } else {
                $session->set($link['web'], $link['count']);
            }
        }
    }

}