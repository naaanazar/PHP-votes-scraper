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

        $dom = HtmlDomParser::file_get_html('http://rada.gov.ua/news/hpz8');

        foreach($dom->find('div#list_archive div.news_item span.details a') as $element) {

            $votes = HtmlDomParser::file_get_html($element->href);

            $nameBill = $this->getEncodData($votes->find('div.head_gol', 0)->children(0)->innertext());

            $billStatus = ($this->getEncodData($votes->find('div.head_gol', 0)->childNodes(4)->innertext));

            var_dump($this->getEncodData($votes->find('div.head_gol', 0)->innertext));
            $blockContent = $this->getEncodData($votes->find('div.head_gol', 0)->innertext);

            preg_match("/\d{2}.\d{2}.\d{4} \d{2}:\d{2}/", $blockContent, $date);

            var_dump($date);

            preg_match("/За:(\d+)/", $blockContent, $match);
            var_dump($match[1]);

           // preg_match_all("/(За:(\d+))|(Проти:(\d+))|(Утрималися:(\d+))|(голосували:(\d+))|(Всього:(\d+))/", $blockContent, $match);
          //  preg_match_all("/(За:\d+)|(Проти:\d+)|(Утрималися:\d*)|(голосували:\d+)|(Всього:\d+)/", $blockContent, $match);
            preg_match_all("/(За:\d+)|(Проти:\d+)|(Утрималися:\d*)|(голосували:\d+)|(Всього:\d+)/", $blockContent, $match);

           var_dump($match);


            foreach($votes->find('li[id="00"] ul.fr li ul.frd li') as $element) {

                $name = $this->getValidData($this->getEncodData($element->find('.dep', 0)->innertext));
                $vote = $this->getEncodData($element->find('.golos', 0)->plaintext);
                $vote =  $this->getValidData(str_replace('</font>', '', $vote));

                $deputatId = $this->getDeputatId($name);

              //  $this->insertBillToDb();

                $bill = '12';

                $this->insertVotesToDB($deputatId, $vote, $bill);

                echo($vote . '  ' . $deputatId . '   ' . $bill . '<br>');

            }

            sleep(30);

        }
    }


    /**
     * @param $data
     * @return string
     */
    protected function getValidData($data)
    {
        $validation = new Validate;
        return $validation->validation('text', $data, null, null);
    }

    /**
     * @param $data
     * @return string
     */
    protected function getEncodData($data)
    {
        return iconv(mb_detect_encoding($data, mb_detect_order(), true), "UTF-8", $data);
    }

    /**
     * @param $name
     * @return mixed
     */
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

     /*   echo 'SELECT * FROM deputat WHERE name = \'' . $name . '\'';
        var_dump($row['id']);
        var_dump($result);*/

        return $deputatId;
    }

    /**
     * @param $deputatId
     * @param $vote
     * @param $bill
     * @return bool|\mysqli_result
     */
    protected function insertVotesToDB($deputatId, $vote, $bill)
    {

        $sql = "INSERT  INTO votes (deputat_id, bill_id, votes) VALUES ('" . $deputatId . "', '" . $bill . "', '" . $vote . "')";
        return $this->query->queryToDB($sql);
    }

    protected function insertBillToDb(){

    }
}