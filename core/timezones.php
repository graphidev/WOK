<?php
    
	define('TIMEZONES', serialize(array (
		'Africa' => array(
			'Abidjan', 'Accra', 'Addis_Ababa', 'Algiers', 'Asmara', 'Bamako', 'Bangui', 'Banjul', 'Bissau', 'Blantyre', 'Brazzaville', 'Bujumbura', 'Casablanca', 'Ceuta', 'Conakry', 'Dakar',  'Dar_es_Salaam', 'Djibouti', 'Douala', 'Freetown', 'Gaborone', 'Harare', 'Johannesburg', 'Juba', 'Kampala', 'Khartoum', 'Kigali', 'Kinshasa', 'Lagos', 'El_Aaiun', 'Cairo', 'Libreville', 'Lome', 'Luanda', 'Lubumbashi', 'Lusaka', 'Malabo', 'Maputo', 'Maseru', 'Mbabane', 'Mogadishu', 'Monrovia', 'Ndjamena', 'Nairobi', 'Niamey', 'Nouakchott', 'Ouagadougou', 'Porto-Novo', 'Sao_Tome', 'Tripoli', 'Tunis', 'Windhoek'
		),
		
		'America' => array(
			'Adak', 'Anchorage', 'Anguilla', 'Antigua', 'Antigua', 'Araguaina', 'Argentina/Buenos_Aires', 'Argentina/Catamarca', 'Argentina/Cordoba', 'Argentina/La_Rioja', 'Argentina/Mendoza', 'Argentina/Rio_Gallegos', 'Argentina/Salta', 'Argentina/San_Juan', 'Argentina/San_Luis', 'Argentina/Tucuman', 'Argentina/Jujuy', 'Argentina/Ushuaia', 'Aruba', 'Asuncion', 'Atikokan', 'Bahia', 'Bahia_Banderas', 'Barbados', 'Belize', 'Belem', 'Blanc-Sablon', 'Boa_Vista', 'Bogota', 'Boise', 'Campo_Grande', 'Cancun', 'Caracas', 'Cayenne', 'Chicago', 'Costa_Rica', 'Creston', 'Cuiaba', 'Curacao', 'North_Dakota/Beulah', 'North_Dakota/Center', 'North_Dakota/New_Salem', 'Danmarkshavn', 'Dawson', 'Dawson_Creek', 'Denver', 'Dominica', 'Edmonton', 'Eirunepe', 'Chihuahua', 'Fortaleza', 'Glace_Bay', 'Goose_Bay', 'Grand_Turk', 'Grenada', 'Guadeloupe', 'Guatemala', 'Guayaquil', 'Guyana', 'Halifax', 'Hermosillo', 'Cambridge_Bay', 'Indiana/Indianapolis', 'Indiana/Knox', 'Indiana/Marengo', 'Indiana/Petersburg,', 'Indiana/Tell_City', 'Indiana/Vevay', 'Indiana/Vincennes', 'Indiana/Winamac', 'Inuvik', 'Iqaluit', 'Scoresbysund', 'Jamaica', 'Juneau', 'Rankin_Inlet', 'Kentucky/Louisville', 'Kentucky/Monticello', 'Kralendijk', 'Havana', 'La_Paz', 'Lima', 'Los_Angeles', 'Lower_Princes', 'Maceio', 'Managua', 'Manaus', 'Marigot', 'Martinique', 'Matamoros', 'Mazatlan', 'Menominee', 'Merida', 'Metlakatla', 'Mexico_City', 'Miquelon', 'Moncton', 'Monterrey', 'Montevideo', 'Montreal', 'Montserrat', 'Nassau', 'New_York', 'Nipigon', 'Nome', 'Noronha', 'Godthab', 'Ojinaga', 'Panama', 'Pangnirtung', 'Paramaribo', 'Phoenix', 'Port-au-Prince', 'Port_of_Spain', 'Puerto_Rico', 'Porto_Velho', 'Rainy_River', 'Recife', 'Regina', 'Resolute', 'Rio_Branco', 'St_Barthelemy', 'Santo_Domingo', 'St_Lucia', 'St_Johns', 'St_Thomas', 'St_Vincent', 'El_Salvador', 'Santa_Isabel', 'Santarem', 'Santiago', 'Shiprock', 'Sitka', 'Swift_Current', 'Sao_Paulo', 'Tegucigalpa', 'Thule', 'Thunder_Bay', 'Tijuana', 'Toronto', 'Tortola', 'Vancouver', 'Whitehorse', 'Winnipeg', 'Yakutat', 'Yellowknife', 'St_Kitts', 'Cayman'
		),
		
		'Antarctica' => array(
			'Casey', 'Davis', 'DumontDUrville', 'Macquarie', 'Mawson', 'McMurdo', 'Palmer', 'South_Pole', 'Rothera', 'Syowa', 'Vostok'
		),
		
		'Arctic' => array(
			'Longyearbyen'
		),
		
		'Asia' => array(
			'Ashgabat', 'Aden', 'Aqtobe', 'Almaty', 'Amman', 'Anadyr', 'Aqtau', 'Baghdad', 'Bahrain', 'Baku', 'Bangkok', 'Beirut', 'Bishkek', 'Brunei', 'Kolkata', 'Chongqing', 'Choibalsan', 'Colombo', 'Damascus', 'Dhaka', 'Dili', 'Dushanbe', 'Dubai', 'Gaza', 'Harbin', 'Hebron', 'Hong_Kong', 'Hovd', 'Ho_Chi_Minh', 'Yakutsk', 'Yekaterinburg', 'Irkutsk', 'Jakarta', 'Jayapura', 'Jerusalem', 'Kabul', 'Kashgar', 'Kamchatka', 'Karachi', 'Kathmandu', 'Kuwait', 'Krasnoyarsk', 'Krasnoyarsk', 'Kuala_Lumpur', 'Kuching', 'Qyzylorda', 'Macau', 'Magadan', 'Makassar', 'Manila', 'Muscat', 'Nicosia', 'Novokuznetsk', 'Novosibirsk', 'Omsk', 'Ulaanbaatar', 'Oral', 'Phnom_Penh', 'Pontianak', 'Pyongyang', 'Qatar', 'Rangoon', 'Riyadh', 'Sakhalin', 'Samarkand', 'Shanghai', 'Singapore', 'Seoul', 'Tashkent', 'Taipei', 'Tbilisi', 'Thimphu', 'Tehran', 'Tokyo', 'Vientiane', 'Vladivostok', 'Yerevan', 'Urumqi'
		),
		
		'Atlantic' => array(
			'Azores', 'Bermuda', 'Cape_Verde', 'Madeira', 'Reykjavik', 'St_Helena', 'Stanley', 'South_Georgia', 'Canary', 'Faroe'
		),
		
		'Australia' => array(
			'Adelaide', 'Brisbane', 'Broken_Hill', 'Currie', 'Darwin', 'Eucla', 'Hobart', 'Lindeman', 'Melbourne', 'Perth', 'Sydney', 'Lord_Howe' 
		),
		
		'Europe' => array(
			'Amsterdam', 'Andorra', 'Athens', 'Belgrade', 'Berlin', 'Bratislava', 'Brussels', 'Bucharest', 'Budapest', 'Chisinau', 'Copenhagen', 'Dublin', 'Gibraltar', 'Guernsey', 'Helsinki', 'Istanbul', 'Jersey', 'Kaliningrad', 'Kiev', 'Lisbon', 'Ljubljana', 'London', 'Luxembourg', 'Madrid', 'Malta', 'Mariehamn', 'Minsk', 'Monaco', 'Moscow', 'Oslo', 'Uzhgorod', 'Paris', 'Podgorica', 'Prague', 'Riga', 'Rome', 'San_Marino', 'Samara', 'Sarajevo', 'Simferopol', 'Skopje', 'Sofia', 'Stockholm', 'Tallinn', 'Tirane', 'Vaduz', 'Warsaw', 'Vatican', 'Vienna', 'Vilnius', 'Volgograd', 'Zagreb', 'Zaporozhye', 'Zurich', 'Isle_of_Man'
		),
		
		'Indian' => array(
			'Antananarivo', 'Chagos', 'Comoro', 'Mahe', 'Maldives', 'Mayotte', 'Christmas', 'Reunion', 'Mauritius', 'Cocos', 'Kerguelen'
		),
		
		'Pacific' => array(
			'Apia', 'Johnston', 'Midway', 'Auckland', 'Chuuk', 'Enderbury', 'Fakaofo', 'Fiji', 'Funafuti', 'Guadalcanal',  'Guam', 'Honolulu', 'Kosrae', 'Kwajalein', 'Majuro', 'Nauru', 'Niue', 'Norfolk', 'Noumea', 'Pago_Pago', 'Palau', 'Pohnpei', 'Port_Moresby', 'Rarotonga', 'Saipan', 'Tahiti', 'Tarawa', 'Tongatapu',  'Wallis', 'Efate', 'Kiritimati', 'Easter', 'Chatham', 'Galapagos', 'Gambier', 'Marquesas', 'Pitcairn', 'Wake'
		)
		
	)), true);

?>