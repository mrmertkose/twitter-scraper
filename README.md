# Twitter Scraper
A PHP scraper for twitter. Without authentication.
# Installing
Run the command:
```
composer require mertkose/twitter-scraper
```
# Quick start
```
use TwitterNoAuth\Twitter;

$data = new Twitter();

//Recent Tweets
var_dump($data->getTweets("#hashtag"))
```

# Methods
### → **getTweets()** → array

| Key       | Description                                                      |
|-----------|------------------------------------------------------------------|
| tweet_id  | Tweet's identifier                                               |
| username  | Username                                                         |
| photos    | (array) Tweet photos or Null                                     |
| tweet_text| Content of tweet                                                 |
| time      | Tweet time                                                       |
| hashtags  | (array) Tweet hashtags or Null                                   |
| replies   | Replies count of tweet                                           |
| likes     | Like count of tweet                                              |
| retweets  | Retweet count of tweet                                           |


### → **getTrends()** → array
Trend lists

### → **getProfile()** → array

| Key       | Description                                                      |
|-----------|------------------------------------------------------------------|
| username  | Username                                                         |
| page_title| Page Title or Name                                               |
| biography | Biography or Null                                                |
| photo     | Photo                                                            |
| location  | Location or Null                                                 |
| birthday  | Birthday or Null                                                 |
| website   | Website or Null                                                  |
