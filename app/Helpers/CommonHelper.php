<?php

if (!function_exists('prArr')) {
    function prArr($array, $status = false)
    {

        echo "<pre>";
        print_r($array);
        echo "</pre>";

        if ($status) {
            die;
        }
    }
}

if (!function_exists('getClientLocation')) {
    function getClientLocation($latitude, $longitude, $useDescription = false)
    {
        if ($useDescription) {
            return [
                'ip_address' => 'The IP address of the client.',
                'location' => [
                    'latitude' => 'Latitude coordinate of the location.',
                    'longitude' => 'Longitude coordinate of the location.'
                ],
                'isp_info' => [
                    'isp' => 'The name of the Internet Service Provider.',
                    'organization' => 'The name of the organization associated with the IP address.',
                    'as' => 'The Autonomous System Number (ASN) of the ISP.',
                    'accuracy_range' => 'Indicates the accuracy of the location data (approximate in this case).'
                ],
                'device_info' => [
                    'device_type' => 'The type of device (\'Mobile\', \'Tablet\', or \'Desktop\') based on the user agent string.',
                    'browser_name' => 'The name of the browser used by the client based on the user agent string.',
                    'os' => 'The operating system of the client based on the user agent string.',
                ],
                'other_info' => [
                    'timezone' => 'The timezone based on the latitude and longitude coordinates.',
                    'proxy' => 'Indicates whether the client is using a VPN or proxy (true for VPN/proxy detected, false otherwise).'
                ]
            ];
        }

        // Initialize an empty array to store location data
        $location = [];

        // Determine client's IP address
        $ipv4 = null;
        $ipv6 = null;

        // Check HTTP_CLIENT_IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            if (filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ipv6 = $_SERVER['HTTP_CLIENT_IP'];
            }
            if (filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipv4 = $_SERVER['HTTP_CLIENT_IP'];
            }
        }

        // Check HTTP_X_FORWARDED_FOR (Handles multiple IPs)
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (!$ipv6 && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                    $ipv6 = $ip;
                }
                if (!$ipv4 && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $ipv4 = $ip;
                }
            }
        }

        // Check REMOTE_ADDR (Fallback)
        if (!$ipv6 && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ipv6 = $_SERVER['REMOTE_ADDR'];
        }
        if (!$ipv4 && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipv4 = $_SERVER['REMOTE_ADDR'];
        }

        // Example IP for testing
        // $ip = '2409:40d6:2d:185f:dccf:99e3:99aa:b220';

        // Get user agent information
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // 🌐 Detect Device Type
        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone|opera mini|opera mobi|avantgo|palmos|maemo|kindle|webos|silk/i', $userAgent)) {
            $deviceType = 'Mobile';
        } elseif (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            $deviceType = 'Tablet';
        } else {
            $deviceType = 'Desktop';
        }

        // 🖥️ Detect Browser (Global Coverage)
        $browserArray = [
            'Edge' => 'Edge',
            'MSIE|Trident' => 'Internet Explorer',
            'Firefox' => 'Firefox',
            'Chrome' => 'Chrome',
            'Safari' => 'Safari',
            'Opera|OPR' => 'Opera',
            'Brave' => 'Brave',
            'Vivaldi' => 'Vivaldi',
            'YaBrowser' => 'Yandex Browser',
            'UCBrowser' => 'UC Browser',
            'SamsungBrowser' => 'Samsung Browser',
            'Maxthon' => 'Maxthon',
            'Sogou' => 'Sogou',
            'Baidu' => 'Baidu',
            'QQBrowser' => 'QQ Browser',
            'Naver Whale' => 'Naver Whale'
        ];

        $browserName = 'Unknown Browser';
        foreach ($browserArray as $regex => $name) {
            if (preg_match('/' . $regex . '/i', $userAgent)) {
                $browserName = $name;
                break;
            }
        }

        // 💻 Detect OS (Expanded for Global Coverage)
        $osArray = [
            'Windows' => 'Windows',
            'Macintosh|Mac OS X' => 'MacOS',
            'Linux' => 'Linux',
            'Ubuntu' => 'Ubuntu',
            'Android' => 'Android',
            'iPhone|iPad|iOS' => 'iOS',
            'BlackBerry' => 'BlackBerry',
            'Windows Phone' => 'Windows Phone',
            'Chrome OS' => 'ChromeOS',
            'Symbian' => 'Symbian',
            'webOS' => 'webOS',
            'Bada' => 'Bada',
            'Tizen' => 'Tizen',
            'KaiOS' => 'KaiOS'
        ];

        $osName = 'Unknown OS';
        foreach ($osArray as $regex => $name) {
            if (preg_match('/' . $regex . '/i', $userAgent)) {
                $osName = $name;
                break;
            }
        }

        // Create API request URL
        $apiUrl = "http://ip-api.com/json/$ipv6?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,offset,currency,isp,org,as,asname,reverse,mobile,proxy,hosting,query";

        // Fetch location data
        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);

        // Check if API call was successful
        if ($data && $data['status'] == 'success') {
            $isProxy = $data['proxy'];
            $timezone = $data['timezone'];
            // Fill the $location array with retrieved data
            $location = [
                'ip_address' => ['ipv4' => $ipv4, 'ipv6' => $ipv6],
                'location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
                'isp_info' => [
                    'isp' => $data['isp'],  // can be filter need
                    'organization' => $data['org'],  // can be filter need
                    'as' => $data['as'],  // can be filter need
                    'accuracy_range' => 'Approximate'
                ],
                'device_info' => [
                    'device_type' => $deviceType,
                    'browser_name' => $browserName,
                    'os' => $osName
                ],
                'other_info' => [
                    'timezone' => $timezone,
                    'proxy' => $isProxy ? true : false
                ]
            ];
            session([
                'user_preferences' => [
                    'timezone' => $timezone,
                ],
            ]);
        } else {
            $location = [
                'ip_address' => $ip,
                'location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
                'isp_info' => [
                    'isp' => 'Unknown',
                    'organization' => 'Unknown',
                    'as' => 'Unknown',
                    'accuracy_range' => 'Unknown'
                ],
                'device_info' => [
                    'device_type' => $deviceType,
                    'browser_name' => $browserName,
                    'os' => $osName
                ],
                'other_info' => [
                    'timezone' => 'Unknown',
                    'proxy' => false
                ]
            ];
        }
        return $location;
    }
}
