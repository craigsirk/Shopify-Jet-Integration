<?php

return [
	'version'=>'Version 0.1.1 (Beta)',
    'adminEmail' => 'admin@example.com',
	'homeURL'=>'https://shopify.craigsirk.com',
	'shopify'=>[
			'api_key'=>'281a1751d0ddc2daca34ff809d1da9ad',
			'secret'=>'3e24264628c971607ed54a7827dd31c4',
			'nonce'=>'1234',
			'redirect'=>'https://shopify.craigsirk.com/install',
			'products'=>'/admin/products', //https://help.shopify.com/api/reference/product#index
			'metafields'=>'/admin/metafields',
			'admin'=>'/admin'
	],
	'jet'=>[
			'api_key'=>'',
			'pass'=>'',
			'urls'=>[
				'main'=>'https://merchant-api.jet.com/api',
				'token'=>'/token',
				'merchant-skus'=>'/merchant-skus/',
				'orders'=>'/orders/',
				'returns'=>'/returns/'
			],
			'verbiage'=>[
				'updated'=>'Updated Line items at ' . date('c') . ": ",
				'created'=>'Created Via Jet'
			]
	]
	
];
