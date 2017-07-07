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
    /**
     * @var QueryToDB
     */
    protected $query;
    protected $validation;


    /**
     * Init parser
     */
    public function ParseVotes()
    {

        $page = 1;
        while (true) {

            echo 'PAGE #' . $page;
            $dom = HtmlDomParser::file_get_html('http://rada.gov.ua/news/hpz8/page/' . $page);

            $links = $dom->find('div#list_archive div.news_item span.details a');

            if ($links) {

                foreach ($links as $element) {

                    $votes = HtmlDomParser::file_get_html($element->href);
                    $bill = $this->parseHeadBillBlock($votes, $element->href);
                    $billId = $this->insertBillToDb($bill);

                    foreach ($votes->find('li[id="00"] ul.fr li ul.frd li') as $element) {

                        $name = $this->getValidData($this->getEncodData($element->find('.dep', 0)->innertext));
                        $vote = $this->getEncodData($element->find('.golos', 0)->plaintext);
                        $vote = mb_strtolower($this->getValidData(str_replace('</font>', '', $vote)));

                        $description = null;
                        if(stristr($vote, '*') !== FALSE) {
                            $description = $this->getEncodData($votes->find('li[id="00"] table tbody tr', 2)->children(0)->innertext);
                        }

                        /**
                        const voteMap = {
                        noVote   : ['не голосував', 'не голосувала'],
                        no       : ['проти'],
                        yes      : ['за'],
                        absent   : ['відсутній', 'відсутня'],
                        abstained: ['утримався', 'утрималась']
                        };
                         *
                         * mb_strtolower($str);
                         */


                        $deputatId = $this->getDeputatId($name);

                        $this->insertVotesToDB($deputatId, $vote, $billId, $description);
                    }

                    sleep(20);
                }
            } else {
                echo 'END SCRUB';
                exit;
            }

            $page++;
            sleep(30);
        }
    }

    public function test()
    {
        $votes = HtmlDomParser::file_get_html('http://w1.c1.rada.gov.ua/pls/radan_gs09/ns_golos?g_id=13100');

        foreach ($votes->find('li[id="00"] ul.fr li ul.frd li') as $element) {
            $name = $this->getValidData($this->getEncodData($element->find('.dep', 0)->innertext));
            $vote = $this->getEncodData($element->find('.golos', 0)->plaintext);
            $vote = mb_strtolower($this->getValidData(str_replace('</font>', '', $vote)));

            if(stristr($vote, '*') !== FALSE) {
                var_dump($this->getEncodData($votes->find('li[id="00"] table tbody tr', 2)->children(0)->innertext));
                echo $vote;
            }
        }
    }

/**
     * Validation data
     * @param $data
     * @return string
     */
    protected function getValidData($data)
    {
        $validation = new Validate;
        return $validation->validation('text', $data, null, null);
    }


    /**
     * Encote data to UTF8
     * @param $data
     * @return string
     */
    protected function getEncodData($data)
    {
        return iconv(mb_detect_encoding($data, mb_detect_order(), true), "UTF-8", $data);
    }


    /**
     * Get deputatId if exist, else insert new Deputat to DB
     * @param $name
     * @return mixed
     */
    protected function getDeputatId($name)
    {

        $query = new QueryToDB();
        $sql = 'SELECT id FROM deputat WHERE name = \''. $name .'\'';
        $result = $query->queryToDB($sql);
        $row = mysqli_fetch_assoc($result);

        if($row == null ){
            $sql = "INSERT  INTO deputat (name) VALUES ('" . $name . "')";

            $result = $query->queryToDB($sql);
            $deputatId = $query->getConnection()->insert_id;

        } else {
            $deputatId = $row['id'];
        }

        return $deputatId;
    }


    /**
     * Save votes to DB
     * @param $deputatId
     * @param $vote
     * @param $bill
     * @return bool|\mysqli_result
     */
    protected function insertVotesToDB($deputatId, $vote, $bill, $description)
    {
        $query = new QueryToDB();

        $sql = "INSERT  INTO votes (deputat_id, bill_id, votes, description) VALUES ('" . $deputatId . "', '" . $bill . "', '" . $vote . "', '" . $description . "')";

        return $query->queryToDB($sql);
    }


    /**
     * Save Bill rusuls to DB
     * @param $data
     * @return mixed
     */
    protected function insertBillToDb($data)
    {
        $query = new QueryToDB();
        $sql = "INSERT  INTO bill (
            bill_name, bill_status, date, yes, no, abstained, not_vote, all_deputats, bill_id_rada) 
            VALUES ('" . $this->getValidData($data['billName']) . "', '" . $data['billStatus'] . "', '" . $data['date'] . "', '" . $data['za'] . "', '" . $data['opposite'] . "', '" . $data['refrained'] . "', '" . $data['notVote'] . "', '" . $data['all'] . "', '" . $data['billIdRada'] . "')";
        $result = $query->queryToDB($sql);

        return $query->getConnection()->insert_id;
    }

    /**
     * Parse heading block of bill
     * @param $votes
     * @param $link
     * @return mixed
     */
    protected function parseHeadBillBlock($votes, $link)
    {
        $data['billName'] = $this->getEncodData($votes->find('div.head_gol', 0)->children(0)->innertext());
        $status = ($this->getValidData($this->getEncodData($votes->find('div.head_gol', 0)->childNodes(4)->innertext)));
        if ($status == 'Рішення прийнято'){
            $data['billStatus'] = 1;
        } else {
            $data['billStatus'] = 0;
        }

        $blockContent = $this->getEncodData($votes->find('div.head_gol', 0)->innertext);

        preg_match("/\d{2}.\d{2}.\d{4} \d{2}:\d{2}/", $blockContent, $date);
        $data['date'] = date_format(date_create_from_format('d.m.Y H:i', $date['0']), "Y-m-d H:i:s");

        preg_match("/За:(\d+)/", $blockContent, $za);
        preg_match("/Проти:(\d+)/", $blockContent, $opposite);
        preg_match("/Утрималися:(\d+)/", $blockContent, $refrained);
        preg_match("/голосували:(\d+)/", $blockContent, $notVote);
        preg_match("/Всього:(\d+)/", $blockContent, $all);
        preg_match("/=(\d+)/", $link, $billIdRada);

        $data['za'] = $za[1];
        $data['opposite'] = $opposite[1];
        $data['refrained'] = $refrained[1];
        $data['notVote'] = $notVote[1];
        $data['all'] = $all[1];
        $data['billIdRada'] = $billIdRada[1];

        //var_dump($data);

        return $data;
    }
}