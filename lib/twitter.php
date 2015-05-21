<?php

class last_tweet{

// The Base URL to Use
protected $base_url = "http://twitter.com/";

// Grab the URL
protected function get_url($uri)
{
// Start the cURL Connection
$connection = curl_init();

// Set Options
curl_setopt($connection, CURLOPT_HEADER, 1);
curl_setopt($connection, CURLOPT_RETURNTRANSFER,1);

// Set the URL to Grab
$fetch_url = $this->base_url . $uri;
curl_setopt($connection, CURLOPT_URL, $fetch_url);

// Get the Page
$page = curl_exec($connection);

// Close the Connection
curl_close($connection);

return $page;
}

// Parses the XML
protected function parse_xml($xml_string)
{
$xml_string = strstr($xml_string, '<?');
$xml = new SimpleXMLElement($xml_string);
return $xml;
}

public function get_tweet($username, $num)
{
// Build the API URI
$api_call = 'statuses/user_timeline.xml?screen_name='.$username.'&count='.$num;

// Get the XML Object
$page = $this->get_url($api_call);
$xml = $this->parse_xml($page);

// Return the XML Object
return $xml;
}

public function show_tweet($username, $num)
{
// Grab the tweet
$tweet = $this->get_tweet($username, $num);

//all your display code can go here if you want, Im just returning the array from the simplexmlelement

return $tweet;
}

}

?>