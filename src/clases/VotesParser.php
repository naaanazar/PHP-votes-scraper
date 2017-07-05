<?php

namespace App\clases;



use App\library\QueryToDB;
use App\library\Validate;

use Sunra\PhpSimple\HtmlDomParser;

/**
 * Class VotesParser
 * @package App\clases
 */
class VotesParser
{
    protected $query;
    protected $validation;

    /**
     * VotesParser constructor.
     */
    public function __construct()
    {
        $this->query = new QueryToDB();
    }

    public function ParseVotes()
    {

        $query =  $this->query;
        $validation = $this->validation;

        $dom = HtmlDomParser::file_get_html('http://rada.gov.ua/news/hpz8');

        foreach($dom->find('div#list_archive div.news_item span.details a') as $element) {


            $votes = HtmlDomParser::file_get_html($element->href);

            echo $votes->find('.head_gol', 0)->innertext .  "<br>";

            foreach($votes->find('li[id="00"] ul.fr li ul.frd li') as $element) {

                $name = $this->getValidData($this->getEncodData($element->find('.dep', 0)->innertext));
                $vote = $this->getEncodData($element->find('.golos', 0)->plaintext);
                $vote =  $this->getValidData(str_replace('</font>', '', $vote));

                var_dump($vote);

                $deputatId = getDeputatId($name);

                $sql = "INSERT  INTO votes (deputat_id, bill_id, votes) VALUES ('" . $deputatId . "', '" .'12' . "', '" . $vote . "')";

                $result = $query->queryToDB($sql);

                // var_dump( $element->find('.dep'));

            }

            sleep(30);
//  $votes = HtmlDomParser::file_get_html('http://w1.c1.rada.gov.ua/pls/radan_gs09/ns_golos?g_id=13159');


        }
    }



    protected function getValidData($data)
    {
        $validation = new Validate;
        return $validation->validation('text', $data, null, null);
    }

    protected function getEncodData($data)
    {
        return iconv(mb_detect_encoding($data, mb_detect_order(), true), "UTF-8", $data);
    }

    protected function getDeputatId($name)
    {
        $sql = 'SELECT id FROM deputat WHERE name = \''. $name .'\'';
        $result = $this->query->queryToDB($sql);
        $row = mysqli_fetch_assoc($result);

        if($row == null ){
            $sql = "INSERT  INTO deputat (name) VALUES ('" . $name . "')";

            $result = $this->query->queryToDB($sql);
            $deputatId = $this->query->getConnection()->insert_id;

        } else {
            $deputatId = $row['id'];
        }

        echo 'SELECT * FROM deputat WHERE name = \'' . $name . '\'';
        var_dump($result);
        var_dump($row['id']);

        return $deputatId;
    }
}