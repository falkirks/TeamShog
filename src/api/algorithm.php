<?php
/**
 * SUMMARIZER
 * A keystone extraction based in-house summarization script
 * Some code extracted from online sources.
 * Last updated: December 6th 2014
 * TODO:Test algorithm
 */
namespace water\api;


class algorithm {

    var $wordstat;
    var $stopwords;

    public function Summary(){
        global $stopwords;
        $this->wordstat = array();
        $this->stopwords = $stopwords;
    }

    public static function Summarize($text,$percent=0.25,$minimum_sent=1,$maximum_sent=0){
        $lines = self::sentence_splitter($text);
        $line_array = array();

        for($i=0;$i<count($lines);$i++){
            $words = self::sentence_splitter($text);
            $wordstat = array();
            foreach ($words as $word){

                if(in_array($word, self::stopwords)) continue;
                //TODO:Match stems

                if(!isset($wordstat[$word])){
                    $wordstat[$word]=1;
                }else{
                    $wordstat[$word]++;
                }

                if(!isset(self::$wordstat[$word])) {
                    self::$wordstat[$word] = 1;
                }else {
                    self::$wordstat[$word]++;
                }
            }
            $line_array[] = array(
                'sentnece' => $lines[$i],
                'wordstat' => $wordstat,
                'ord' => $i
            );

        }
        arsort(self::$wordstat);
        //Take top 25 only
        self::$wordstat = array_slice(self::wordstat,0,25);

        for($i=0;$i<count($line_array);$i++){
            $importance = self::calculate_importance($line_array[$i]['wordstat']);
            $line_array[$i]['importance'] = $importance;
        }
        //Sort according to importance
        usort($line_array,array(self, 'arraycomparison_rating'));
        if ($maximum_sent===0) $maximum_sent = count($line_array);
        $summary_count = min(
            $maximum_sent,
            max(
                min($minimum_sent, count($line_array)),
                round($percent*count($line_array))
            )
        );
        if($summary_count<1){
            $summary_count = 1;
        }
        $line_array = array_slice($line_array, -$summary_count);
        usort($line_array,array(self, 'arraycomparsion_ord'));
        $line_array = array();
        foreach($line_array as $sentence){
            $summary[] = $sentence['sentence'];
        }
        return $summary;
    }

    public function arraycomparison_rating($vara, $varb){
        return $this->arraycomparison($vara, $varb, 'rating');
    }

    public function arraycomparison_order($vara, $varb){
        return $this->arraycomparison($vara, $varb, 'ord');
    }

    public function arraycomparison($vara, $varb, $key){
        if (is_int($vara[$key]) || is_float($vara[$key])){
            return floatval($vara[$key])-floatval($varb[$key]);
        } else {
            return strcmp(strval($vara[$key]), strval($varb[$key]));
        }
    }


    public function calculate_importance($text){
        //TODO:Improve rating
        $rating = 0;
        foreach ($text as $word=>$count){
            if(!isset($this->wordstat[$word])) continue;
            $wordrate = $count * $this->wordstat[$word];
            $rating += $wordrate;
        }
        return $rating;
    }

    public static function word_splitter($text){
        //Splits the block of text into individual words
        $words = preg_split('/[\'\s\r\n\t$]+/', $text);
        $array = array();
        foreach ($words as $word){
            //Cleanup and push to array
            $word = strtolower($word);
            $word = preg_replace('/(^[^a-z0-9]+|[^a-z0-9]$)/i','', $word);
            if (strlen($word)>0)
                array_push($array, $word);
        }
        return $array;
    }

    public static function sentence_splitter($text){
        //Splits block of text into sentences. Newlines are also splitting points
        if (preg_match_all('/["\']*.+?([.?!\n\r]+["\']*\s+|$)/si', $text, $hits, PREG_SET_ORDER)){
            $array = array();
            foreach ($hits as $hit){
                //Push to array
                array_push($array, trim($hit[0]));
            }
            return $array;
        } else {
            return array($text);
        }
    }
}
//List of common words from Open Text Summarizer
$stopwords = array(
    '--', '-', 'a', 'about', 'again', 'all', 'along', 'almost', 'also', 'always', 'am', 'among', 'an', 'and',
    'another', 'any', 'anybody', 'anything', 'anywhere', 'apart', 'are', 'around', 'as', 'at', 'be', 'because',
    'been', 'before', 'being', 'between', 'both', 'but', 'by', 'can', 'cannot', 'comes', 'could', 'couldn', 'did',
    'didn','different', 'do', 'does', 'doesn', 'done', 'don', 'down', 'during', 'each', 'either', 'enough', 'etc',
    'even', 'every', 'everybody', 'everything', 'everywhere', 'except', 'few', 'final', 'first', 'for', 'from',
    'get', 'go', 'goes', 'gone', 'good', 'got', 'had', 'has', 'have', 'having', 'he', 'hence', 'her', 'him', 'his',
    'how', 'however', 'i', 'i.e', 'if', 'in', 'initial', 'into', 'is', 'isn', 'it', 'its', 'it', 'itself', 'just',
    'last','least', 'less', 'let', 'lets', 'let\'s', 'like', 'lot', 'made', 'make', 'many', 'may', 'maybe', 'me',
    'might', 'mine', 'more', 'most', 'Mr', 'much', 'must', 'my', 'near', 'need', 'next', 'niether', 'no', 'nobody',
    'nor', 'not', 'nothing', 'now', 'nowhere', 'of', 'off', 'often', 'oh', 'ok', 'okay', 'on', 'once', 'one',
    'only', 'onto', 'or', 'other', 'our', 'ours', 'out', 'over', 'own', 'perhaps', 'previous', 'quite', 'rather',
    're', 'really', 's', 'said', 'same', 'say', 'see', 'seems', 'several', 'shall', 'she', 'should',
    'shouldn\'t', 'since', 'so', 'some', 'somebody', 'something', 'somewhere', 'still', 'stuff', 'such', 'than',
    't', 'that', 'the', 'their', 'theirs', 'them', 'then', 'there', 'these', 'they', 'thing', 'things', 'this',
    'those', 'through', 'thus', 'to', 'too', 'top', 'two', 'under', 'unless', 'until', 'up', 'upon', 'us',
    'use', 'v', 've', 'very', 'want', 'was', 'we', 'well', 'went', 'were', 'what', 'when', 'where', 'which',
    'while', 'who', 'whom', 'why', 'will', 'with', 'without', 'won', 'would', 'x', 'yes', 'yet', 'you', 'you',
    'your', 'yours', 'll', 'm', 'shouldn', 'won\'t', 'hadn'
);