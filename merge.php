<?php
    include_once('autoloader.php');

$news_rss = array(
    'https://www.dailyrecord.co.uk/all-about/celtic-fc/?service=rss',
    'https://www.scotsman.com/sport/football/celtic/rss',
    'https://www.glasgowworld.com/sport/football/celtic/rss',
    'https://www.glasgowlive.co.uk/all-about/celtic-fc/?service=rss'
);

$news_feeds = [];
$items = [];

foreach ($news_rss as $rss_url) {
    $feed = new \SimplePie();
    $feed->set_feed_url($rss_url);
    $feed->enable_cache(false); // Optional: Disable caching for fresh results
    $feed->init();
    $feed->handle_content_type();
    
    if ($feed->error()) {
        echo "Error fetching feed: " . $feed->error() . "<br>";
    } else {
        $news_feeds[] = $feed;
        $items = array_merge($items, $feed->get_items());
    }
}

// Sort items by date (optional)
usort($items, function ($a, $b) {
    return $b->get_date('U') <=> $a->get_date('U');
});

// Generate HTML content
$html_content = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>RSS Feed Results</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        h3 { margin-bottom: 5px; }
        a { text-decoration: none; color: blue; }
        p { margin-bottom: 10px; }
        hr { border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Latest RSS Feed News</h1>";

foreach ($items as $item) {
    $html_content .= "<h3><a href='" . $item->get_permalink() . "'>" . $item->get_title() . "</a></h3>";
    $html_content .= "<p>" . $item->get_description() . "</p>";
    $html_content .= "<hr>";
}

$html_content .= "</body></html>";

// Save to file
file_put_contents('public/index.html', $html_content);

echo "Results saved to <a href='index.html'>results.html</a>";
