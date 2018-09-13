$midFile = dirname(__FILE__)."/mids";
$json = json_decode(file_get_contents("php://input"), true);
$mids = explode(PHP_EOL, trim(file_get_contents($midFile)));
$newMids = array();
if (!isset($json["result"])) {
    exit(0);
}
foreach ($json["result"] as $result) {
    $newMids[] = $result["content"]["from"];
}
$messages = array();
foreach ($json["result"] as $result) {
    if (!isset($result["content"]["text"])) {
        continue;
    }
    if (1 > strlen($result["content"]["text"])) {
        continue;
    }
    $messages[] = array(
        "contentType" => 1,
        "text" => $result["content"]["text"],
    );
}
if (0 == count($messages)) {
    exit(0);
}
$mids = array_merge($newMids, $mids);
$mids = array_unique($mids);
file_put_contents($midFile, implode(PHP_EOL, $mids));

$body = json_encode(
array(
    "to" => array_values($mids),
    "toChannel" => 1383378250,
    "eventType" => "140177271400161403",
    "content" => array(
        "messageNotified" => 0,
        "messages" => $messages,
    ),
)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://trialbot-api.line.me/v1/events");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
"Content-Type: application/json; charset=UTF-8",
"X-Line-ChannelID: 1607264057",
"X-Line-ChannelSecret: 51f6f641a0fa156eea2ed7faa36c13f3
",
"X-Line-Trusted-User-With-ACL: xNqP/18XS1eEHyWEOk9kmz2PfjMf/1v9eQWdB/yVFBitRW5Wic7hf3u1k/kZE5vJGqgnwzYCBVh9fC6NQhzkccE/4r5FXMWv9CCNOTWZ22B0oa2u/7hzvIsrn/2q7m5xf+LoJ8VPP16fdAgbnpKSjgdB04t89/1O/w1cDnyilFU=",
));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
$result = curl_exec($ch);