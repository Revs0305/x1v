<?php
$ch = curl_init();
$videoId = isset($_GET['id']) ? $_GET['id'] : '9';
$base64Image = $_GET['img'] ?? '';
$fileName = $_GET['name'] ?? 'default';

// Decode the base64 image URL
$imageUrl = base64_decode($base64Image);


$url = 'https://vix.com/api/video/token?videoId=' . urlencode($videoId); 

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'authority: vix.com',
    'authority: tkx.mp.lura.live',
    'accept: */*',
    'accept-language: es-ES,es;q=0.9,en;q=0.8',
    'sec-ch-ua: "Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: cross-site',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
    'x-video-pod-serve: ULTRON',
    'x-video-type: Livestream',
    'accept-encoding: gzip',
]);

$response = curl_exec($ch);

curl_close($ch);

$json_response = json_decode($response, true);
$accessKey = $json_response['accessKey'];
$token = $json_response['token'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://tkx.mp.lura.live/rest/v2/mcp/video/' . $videoId . '?anvack=' . $accessKey . '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'authority: tkx.mp.lura.live',
    'accept: */*',
    'accept-language: es-ES,es;q=0.9,en;q=0.8',
    'content-type: application/x-www-form-urlencoded',
    'origin: https://vix.com',
    'referer: https://vix.com/',
    'sec-ch-ua: "Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: cross-site',
    'user-agent: Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36',
    'accept-encoding: gzip',
]);
//curl_setopt($ch, CURLOPT_POSTFIELDS, '{"ads":{"freewheel":{},"dfp":{"ad_platform":"vix","server_url":"https://pubads.g.doubleclick.net/gampad/ads?"}},"content":{"mcp_video_id": "' . $videoId . '","mpx_guid":""},"user":{"glg":"","glt":"","gst":"","gzip":"","hst":"","device":"web","device_id":"50B6C951-4B30-455E-9353-8D89F562231E","sdkver":"3.8.5.1.b2f75b3a","sdkenv":"html5","host":"vix.com","adobepass":{"requestor":null,"resource":null},"mvpd_authorization":{}},"api":{"anvrid":"82890293cf98f599bda63b5f0cc980", "anvstk2": "' . $token . '"}}');
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"ads":{"freewheel":{},"dfp":{"ad_platform":"vix","server_url":"https://pubads.g.doubleclick.net"}},"content":{"mcp_video_id":"' . $videoId . '","mpx_guid":""},"user":{"glg":"","glt":"","gst":"","gzip":"","hst":"","device":"web","device_id":"C254BFEF-E162-458D-B343-525F9F37416E","sdkver":"3.8.5.1.b2f75b3a","sdkenv":"html5","host":"vix.com","adobepass":{"requestor":null,"resource":null},"mvpd_authorization":{}},"api":{"anvrid":"fdf9b7c11e684fa218515df512ecb1","anvts":1729889281,"anvstk2":"' . $token . '"}}');

$response = curl_exec($ch);
curl_close($ch);

$pattern = '/anvatoVideoJSONLoaded\((.*)\)/';
preg_match($pattern, $response, $matches);
$json_response = $matches[1];
echo $json_response;

$data = json_decode($json_response, true);
$published_urls = $data['published_urls'][0];

$embed_url = $published_urls['embed_url'];
$license_url = $published_urls['license_url'];

// Guardar proxy aleatorio
$proxies = array(
    //'https://uty-nfrh.onrender.com/', 
    //'https://uty-zt5g.onrender.com/',
    'https://andi-cors-proxy-service-k8s.andisearch.com/',
    //'https://uty-hb3i.onrender.com' // tigot
    //'https://uty-ub6x.onrender.com/'
);
$random_proxy = $proxies[array_rand($proxies)];

// Guardar en archivo JSON
$jsonData = json_encode(['proxy' => $random_proxy, 'img' => $imageUrl, 'embed_url' => $embed_url, 'license_url' => $license_url], JSON_UNESCAPED_SLASHES);
$filePath = "json/{$fileName}.json";
file_put_contents($filePath, $jsonData);

// Set header for JSON output
header('Content-Type: application/json');
echo "Successful update, see it at: (json/$fileName.json)";
?>
