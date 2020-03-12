<?php

namespace TwitterNoAuth;

use Curl\Curl;
use DiDom\Document;

class Twitter
{

    private $baseUrl = "https://twitter.com/";
    private $urlTweets = "i/search/timeline?";
    private $urlTrends = "i/trends";
    private $finalUrl;
    private $curl;
    private $document;
    private $headers;
    private $tweets = array();
    private $hashtag = array();
    private $photos = array();
    private $replies = 0;
    private $likes = 0;
    private $retweets = 0;
    private $trends = array();


    public function __construct()
    {
        $this->curl = new Curl();
        $this->document = new Document();
        $this->headers = array(
            "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-Twitter-Active-User" => "yes",
            "X-Requested-With" => "XMLHttpRequest",
            "Accept-Language" => "en-US"
        );
    }

    public function getTweets($query = "#twitter"): array
    {
        $urlData = array(
            "f" => "tweets",
            "q" => "$query",
            "src" => "typd",
        );
        $this->finalUrl = $this->baseUrl . $this->urlTweets . http_build_query($urlData);
        $this->curl->setReferrer("https://twitter.com/$query");
        $this->curl->setHeaders($this->headers);
        $this->curl->get($this->finalUrl);
        $jsonDecode = json_decode($this->curl->response)->items_html;
        $data = $this->document->loadHtml($jsonDecode)->find(".stream-item");

        foreach ($data as $item) {

            $tweetText = $item->find(".tweet-text")[0]->text();
            $tweetId = $item->getAttribute("data-item-id");
            $time = date("Y-m-d H:i:s", $item->find("._timestamp")[0]->attr("data-time-ms") / 1000);
            preg_match('/@(.*)/', $item->find("span[class=username u-dir u-textTruncate]")[0]->text(), $output);
            $username = $output[1];


            if (count($elementsTag = $item->find(".twitter-hashtag")) > 0) {
                foreach ($elementsTag as $tag) {
                    $this->hashtag[] = $tag->text();
                }
            } else {
                $this->hashtag = null;
            }


            if (count($elementsPhoto = $item->find(".AdaptiveMedia-photoContainer")) > 0) {
                foreach ($elementsPhoto as $photo) {
                    $this->photos[] = $photo->getAttribute("data-image-url");
                }
            } else {
                $this->photos = null;
            }

            foreach ($item->find(".ProfileTweet-actionCount") as $tweetAction) {
                if (preg_match('/(.*)\sreplie\w?/', $tweetAction->text(), $outputReplies))
                    $this->replies = trim($outputReplies[1]);

                if (preg_match('/(.*)\slike\w?/', $tweetAction->text(), $outputLikes))
                    $this->likes = trim($outputLikes[1]);

                if (preg_match('/(.*)\sretweet\w?/', $tweetAction->text(), $outputRetweet))
                    $this->retweets = trim($outputRetweet[1]);
            }

            $this->tweets[] = [
                "tweet_id" => $tweetId,
                "username" => $username,
                "photos" => $this->photos,
                "tweet_text" => $tweetText,
                "time" => $time,
                "hashtags" => $this->hashtag,
                "replies" => (int)$this->replies,
                "likes" => (int)$this->likes,
                "retweets" => (int)$this->retweets

            ];
            unset($this->hashtag, $this->photos);
        }

        return $this->tweets;
    }

    public function getTrends(): array
    {
        $this->finalUrl = $this->baseUrl . $this->urlTrends;
        $this->curl->setHeaders($this->headers);
        $this->curl->get($this->finalUrl);
        $jsonDecode = json_decode($this->curl->response)->module_html;
        $data = $this->document->loadHtml($jsonDecode)->find("li");
        foreach ($data as $item)
            $this->trends[] = $item->getAttribute("data-trend-name");

        return $this->trends;
    }

    public function getProfile($username = "jack"): array
    {
        $this->curl->setReferrer($this->baseUrl . $username);
        $this->curl->setHeaders($this->headers);
        $this->curl->get($this->baseUrl . $username);
        $data = $this->document->loadHtml($this->curl->response);
        !empty($elementsLocation = trim($data->find(".ProfileHeaderCard-locationText")[0]->text())) ? $location = $elementsLocation : $location = null;
        !empty($elementsBirthday = trim($data->find(".ProfileHeaderCard-birthdateText")[0]->text())) ? $birthday = str_replace("Born ", "", $elementsBirthday) : $birthday = null;
        !empty($elementsBiography = trim($data->find(".ProfileHeaderCard-bio")[0]->text())) ? $biography = $elementsBiography : $biography = null;
        !empty($elementsWebsite = trim($data->find(".ProfileHeaderCard-urlText")[0]->text())) ? $website = $elementsWebsite : $website = null;
        $photo = $data->find(".ProfileAvatar-image")[0]->getAttribute("src");
        $pageTitle = preg_split('/\s?\((.*)\)\s?\|\s?Twitter/', $data->find("title")[0]->text())[0];
        return array(
            "username" => $username,
            "page_title" => $pageTitle,
            "biography" => $biography,
            "photo" => $photo,
            "location" => $location,
            "birthday" => $birthday,
            "website" => $website
        );
    }

}
