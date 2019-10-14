<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler as Crawler;
use Goutte\Client;
use URLify;

class ProTiming
{
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var array
     */
    private $matches = [
        0 => 0, // rank
        1 => 1, // time
        2 => 2, // name
        3 => 5, // club
        5 => 3, // category
    ];

    private $results = [
        [
            'rank',
            'time',
            'name',
            'category',
            'sex',
            'club',
        ]
    ];

    public function __construct(string $url)
    {
        $this->client = new Client();
        $this->crawler = $this->client->request('GET', $url);
        $this->fileName = (($titleComponents = $this->crawler->filter('h2#page_title span')) && $titleComponents->count() > 2) ? URLify::filter($titleComponents->getNode(2)->textContent . '.csv', 100, "", true) : uniqid() . '.csv';
    }

    public function extract()
    {
        $this->getPageResult();

        $links = $this->crawler->filter('div.pagination_results_top span.next a[rel="next"]');
        if ($links->count() > 0){
            $this->crawler = $this->client->click($links->first()->link());
            $this->extract();
        }

        $this->save();

        return $this->fileName;
    }

    private function save()
    {
        if (!file_exists(getcwd() . DIRECTORY_SEPARATOR . 'out')){
            mkdir(getcwd() . DIRECTORY_SEPARATOR . 'out');
        }

        $fp = fopen(getcwd() . DIRECTORY_SEPARATOR . 'out' . DIRECTORY_SEPARATOR .$this->fileName, 'w');
        foreach ($this->results as $result) {
            fputcsv($fp, $result, ';', '"');
        }
        fclose($fp);
    }

    private function getPageResult()
    {
        /*
         * kikourou pattern
         * ranking;time;LASTNAME Firstname;CAT;SEX;CLUB
         * e.g. 2;32'07;MULEKI SEYA Patrick;ES;M;Asvel Villeurbanne*
         */
        $this->crawler->filter('table#results tbody tr')->each(function (Crawler $node) {
            $result = [];
            $node->filter('td')->each(function (Crawler $node, $column) use (&$result) {
                if (in_array($column, array_keys($this->matches))){
                    $result[$this->matches[$column]] = trim($node->text());

                    if ($column == 5){
                        $result[$this->matches[$column]] = strtoupper(substr($result[$this->matches[$column]], 0, 2));
                    }
                    if ($column == 2){
                        $result[4] = ($node->filter('a.women-runner')->count()) ? 'F' : 'H';
                    }
                }
            });
            ksort($result);
            $this->results[] = $result;
        });
    }
}