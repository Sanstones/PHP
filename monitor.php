<?php
  require 'vendor/autoload.php';
  use Elasticsearch\ClientBuilder;
  $client = ClientBuilder::create()->build();
	$body   = 
   '{
	   "query": {
			"constant_score": {
				"filter": {
					"range": {
						"@timestamp": {
							"gt": "now-5m",
							"lt": "now"
						}
					}
				}
			}
		},
		"aggs" : {
			"avg_responsetime": {
				"avg" : { 
					"field" : "responsetime"
				}
			}
		}
    }';
	
	$body1   = 
   '{
	   "size" : 0,
	   "query": {
		   "bool" :{
			   "must" : [
					{"constant_score": {
						"filter": {
							"range": {
								"@timestamp": {
									"gt": "now-5h",
									"lt": "now"
								}
							}
						}
					}},
					{"constant_score": {
						"filter": {
							"range": {
								"status": {
									"gte": 499
								}
							}
						}
					}}
			   ]
		   }
		}
    }';
   $params = [
    'index' => 'logstash-2017-test',
    'type' => 'logs',
    'body' => $body
  ];
  $response = $client->search($params);
  if($response !== false) {
	 print_r($response);
	  $responsetime = $response['aggregations']['avg_responsetime']['value'];
	  print_r($responsetime);
	  if($responsetime >= 0.2) {
		  //要是响应时间大于  发短信
		  
	  }
  }
  $params = [
    'index' => 'logstash-2017-test',
    'type' => 'logs',
    'body' => $body1
  ];
   $response = $client->search($params);
  if($response !== false) {
	  print_r($response);
	  $errortimes = $response['hits']['total'];
	  print_r($errortimes);
	  if($errortimes > 50) {
		  //要是响应时间大于  发短信 
		  
	  }
  }
  exit;
?>
