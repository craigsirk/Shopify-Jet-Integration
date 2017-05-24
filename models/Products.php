<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Products is the model behind the products object.
 *  *
 * @property int $id 
 * @property string $product_title
 * @property string $vendor
 * @property string $product_type
 * @property int $MSRP
 * @property string $barcode
 * @property int $inv_quantity

 */

class Products extends Model
{
	//Shopify Values
	public $id;
	public $vendor;
	public $product_type;
	public $ShopifyPrice;
	public $barcode;
	public $inv_quantity;
	public $fullArray;
	public $jet_price; //The price offered on Jet
	public $sku;
	public $uploaded; // Flag if uploaded to Jet already
	
	//required fields - https://developer.jet.com/docs/merchant-sku#section-required-data
		public $product_title; //From Shopify
		public $multipack_quantity; //not sure what this is
		
		//either -----
			public $ASIN; //Amazon standard number
			//or
			//standard_product_codes array
				public $standard_product_code; //product code, part of standard_product_codes array
				public $standard_product_code_type; //product code type, part of the standard_product_codes array
		//---------
		
		public $brand; //Brand
		public $main_image_url;
		
		//Minimum Listing Data - https://developer.jet.com/docs/merchant-sku#section-minimum-listing-data
		public $manufacturer;
		public $mfr_part_number;
		public $product_description;
		public $product_description_source; //either shopify product or custom for jet
		public $bullets; //Merchant SKU feature description, up to 5
		public $shipping_weight_pounds;
		public $map_price; //Minimum Advertised Price (MAP)
		public $map_implementation; // can be 101, 102, 103, 101 is normal
		public $jet_browse_node_id; //JET Taxonomy node ID
		public $jet_browse_node_name; // JET Taxonomy Node name
		public $jet_tax_code_recommendation; //Info field for recommendation based on Jet Taxonomy
		public $attributes_node_specific; //array of attributes
		/* example array of attributes
		 [
			 {
				 "attribute_id": 95,
				 "attribute_value": "80",
				 "attribute_value_unit": "Count"
			 },
				 {
				 "attribute_id": 119,
				 "attribute_value": "Aqua"
			 },
			 {
				 "attribute_id": 2,
				 "attribute_value": "Blue"
			 }
		 ]
		 */
	
	//Optional fields - https://developer.jet.com/docs/merchant-sku#section-additional-fields
	public $number_units_for_ppu;
	public $type_of_unit_for_ppu; //generaly this is each
	public $amazon_item_type_keyword;
	public $category_path;
	public $package_length_inches;
	public $package_width_inches;
	public $package_height_inches;
	public $display_length_inches;
	public $display_width_inches;
	public $display_height_inches;
	public $prop_65; //  Proposition 65 requires merchants to provide California consumers with special warnings for products that contain chemicals known to cause cancer
	public $legal_disclaimer_description;
	public $cpsia_cautionary_statements; //array of cautionary like "choking hazard balloon" and "choking hazard small parts"
	public $country_of_origin;
	public $safety_warning;
	public $msrp;
	public $product_tax_code; //Generic Taxable Product
	public $alternate_images; //array of alternate images
		/* example
		 [
			 {
				 "image_slot_id": 1,
				 "image_url": "http://www.mydomain.com/images/myImage.png"
			 },
			 {
				 "image_slot_id": 2,
				 "image_url": "http://www.mydomain.com/images/myImage.png"
			 }
		 ]
		 */
	
		//This array is to declair the attribute type for the metadata Shopify upload
		public $types = array (
				
				'id'=>'integer',
				'ShopifyPrice'=>'string',
				'inv_quantity'=>'integer', 
				'shipping_weight_pounds'=>'integer', 
				'map_price'=>'string', 
				'number_units_for_ppu'=>'integer',
				'package_length_inches'=>'integer', 
				'package_width_inches'=>'integer', 
				'package_height_inches'=>'integer', 
				'display_length_inches'=>'integer', 
				'display_width_inches'=>'integer', 
				'display_height_inches'=>'integer', 
				'msrp'=>'integer',
				'multipack_quantity'=>'integer', 
				'jet_browse_node_id'=>'integer',
				'jet_price'=>'string',
				
				'ASIN'=>'string',
				'map_implementation'=>'string',
				'prop_65'=>'string',
				'$product_title'=>'string',
				'standard_product_code'=>'string',
				'standard_product_code_type'=>'string',
				'brand'=>'string',
				'main_image_url'=>'string',
				'attributes_node_specific'=>'string',
				'manufacturer'=>'string',
				'mfr_part_number'=>'string',
				'product_description'=>'string',
				'product_description_source'=>'string',
				'bullets'=>'string',
				'type_of_unit_for_ppu'=>'string',
				'amazon_item_type_keyword'=>'string',
				'category_path'=>'string',
				'legal_disclaimer_description'=>'string',
				'cpsia_cautionary_statements'=>'string',
				'country_of_origin'=>'string',
				'safety_warning'=>'string',
				'product_tax_code'=>'string',
				'alternate_images'=>'string',
				'sku'=>'string'
				
		);

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        	//the following attribute need to be integers
			[[
				'id',
				'ShopifyPrice',
				'inv_quantity', 
				'shipping_weight_pounds', 
				'map_price',
				'number_units_for_ppu', 
				'package_length_inches', 
				'package_width_inches', 
				'package_height_inches', 
				'display_length_inches', 
				'display_width_inches', 
				'display_height_inches', 
				'msrp',
				'jet_price'
			], 'integer', 'min'=>0.01 ],
        	
        	//checks "multipack_quantity" is a number between 1-128
        	[['multipack_quantity'], 'integer', 'min'=>1, 'max'=>128],
        		
        	//checks "multipack_quantity" is a number between 1-128
        	[['ASIN'], 'integer', 'min'=>10, 'max'=>10],
        		
        	// checks if "prop_65" is of boolean type, either true or false
        	['prop_65', 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true],
        		
        	// checks if "product_title" is a string whose length is between 5 and 500
        	['product_title', 'string', 'length' => [5, 500]],
        		
        	// checks if "brand" is a string whose length is between 1 and 100
        	['brand', 'string', 'length' => [1, 100]],
        		
        	// checks if "product_description" is a string whose length is between 1 and 2000
        	['product_description', 'string', 'length' => [1, 2000]],
        		
        	// checks if "manufacturer" is a string whose length is between 1 and 100
        	['manufacturer', 'string', 'length' => [1, 100]],
        		
        	// checks if "mfr_part_number" is a string whose length is between 1 and 50
        	['mfr_part_number', 'string', 'length' => [1, 50]],
        		
        	// checks if "bullets" is a string whose length is between 1 and 500
        	['bullets', 'string', 'length' => [1, 500]],
        		
        	// checks if "country_of_origin" is a string whose length is between 1 and 50
        	['country_of_origin', 'string', 'length' => [1, 50]],
        		
        	// checks if "safety_warning" is a string whose length is between 1 and 2000
        	['safety_warning', 'string', 'length' => [1, 2000]],
        		
        	// checks if "map_implementation" is 101, 102 or 103
        	['map_implementation', 'in', 'range' => [101, 102, 103]],
        	
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Product ID',
        	'vendor' => 'Vendor',
        	'product_type' => 'Product Type',
        	'inv_quantity' => 'Inventory Quantity',
        	'barcode'=>'Barcode',
        	'ShopifyPrice'=>'Shopify Price',
        	'jet_price'=>'Jet Price',
        	'sku'=>'Sku',
        		
        	'product_title' => 'Product Title',
        	'multipack_quantity' => 'Multipack Quantity',
			'ASIN' => 'Amazon Standard Identification Number',
			'standard_product_code' => 'Standard Product Code',
			'standard_product_code_type' => 'Standard Product Code Type',
			'brand' => 'Brand',
			'main_image_url' => 'Main Image URL',
			
			'manufacturer' => 'Manufacturer',
			'mfr_part_number' => 'Manufacturer Part Number',
			'product_description' => 'Product Description',
        	'product_description_source'=>'where the description is taken from',
			'bullets' => 'Product Bullets',
			'shipping_weight_pounds' => 'Shipping Weight in Lbs',
			'map_price' => 'Minimum Advertised Price (MAP)',
			'map_implementation' => 'Map Implementation Code',
			'jet_browse_node_id' => 'Jet Node ID',
        	'jet_tax_code_recommendation'=>'Jet Tax Code recommendation',
			'attributes_node_specific' => 'Product Attributes',
			'number_units_for_ppu' => 'Number of Units for Price Per Unit',
			'type_of_unit_for_ppu' => 'Type of Unit for Price Per Unit',
			'amazon_item_type_keyword' => 'Amazon Item Keyword',
			'category_path' => 'Cateory Path',
			'package_length_inches' => 'Package Length Inches',
			'package_width_inches' => 'Package Width Inches',
			'package_height_inches' => 'Package Height Inches',
			'display_length_inches' => 'Display Lenght Inches',
			'display_width_inches' => 'Display Width Inches',
			'display_height_inches' => 'Display Height Inches',
			'prop_65' => 'Prop_65',
			'legal_disclaimer_description' => 'Legal Disclaimer Description',
			'cpsia_cautionary_statements' => 'CPSIA Cautionary Statements',
			'country_of_origin' => 'Country of Origin',
			'safety_warning' => 'Safety Warnings',
			'msrp' => 'MSRP',
			'product_tax_code' => 'Product Tax Code',
			'alternate_images' => 'Alternate Images'
        ];
    }
    public function attributeHints()
    {
    	return [
    			'id' => 'Product ID',
    			'vendor' => 'Vendor',
    			'product_type' => 'Product Type',
    			'inv_quantity' => 'Inventory Quantity',
    			'barcode'=>'Barcode',
    			'ShopifyPrice'=>'Shopify Price',
    			'jet_price'=>'Price offered on Jet',
    			'sku'=>'Shopify Provided Sku',
    
    			'product_title' => 'The title of a given merchant sku. 5 to 500 characters',
    			'multipack_quantity' => 'Number of items with the given Standard Product Code that makes up your merchant SKU (product offer). 1 to 128',
    			'ASIN' => 'Amazon standard identification number for this merchant SKU if available. Required if standard_product_codes not provided Alphanumeric length of 10',
    			'standard_product_code' => 'This is an array of product codes. Please see our product code array table below. Required if ASIN not provided.Must be one of the following values: GTIN-14, EAN ,ISBN-10, ISBN-13, UPC',
    			'standard_product_code_type' => 'The standard product code associated with the type provided. If standard product code: GTIN-14 - 14 digits; EAN - 13 digits; ISBN-10 - 10 digits; ISBN-13 - 13 digits; UPC- 12 digits',
    			'brand' => 'The brand for your merchant SKU. 1 to 100 characters',
    			'main_image_url' => 'URL location where Jet.com can access the image. The images should be 40,000 pixels or larger, There is no limit to image size. The only unsupported image file formats are multi-layer png and gif. All images areas must be at least 40000 pixels (ie 200x200); one side must be at least 100 pixels',
    				
    			'manufacturer' => 'Manufacturer of the merchant SKU. 1 to 100 characters',
    			'mfr_part_number' => 'Part number provided by the original manufacturer of the merchant SKU. 1 to 50 characters',
    			'product_description' => 'Product Description. 1 to 2000 characters',
    			'bullets' => 'Merchant SKU feature description. Jet currently allows for up to 5 bullets. 1 to 500 characters; Max 5 strings',
    			'shipping_weight_pounds' => 'Weight of the merchant SKU when in its shippable configuration. Must be >= 0.01',
    			'map_price' => 'Minimum Advertised Price (MAP) for the product. This price is used as the display or reference price when price policy value 102 is used via the map_implementation field. Must be >= 0.01',
    			'map_implementation' => 'Price policy rule that determines allowed item pricing and Jet member savings shown to the consumer. Please note that this field name is misleading as it is used for all price policies and not just a MAP policy. These settings only apply to what the consumer is shown and have no impact on what a retailer is paid for an item.',
    			'jet_browse_node_id' => 'The unique ID that defines where the product will be found in the Jet.com browse structure. This can be found using the Jet Taxonomy API. This must be a valid jet_browse_node_id',
    			'attributes_node_specific' => 'Attributes associated with the category provided. Minimum 1 attributes_node_specific object required if this field is provided Maximum 10 attributes_node_specific object required if this field is provided',
    			'number_units_for_ppu' => 'For Price Per Unit calculations, the number of units included in the merchant SKU. The unit of measure must be specified in order to indicate what is being measured by the unit-count.',
    			'type_of_unit_for_ppu' => 'The type_of_unit_for_price_per_unit attribute is a label for the number_units_for_price_per_unit. The price per unit can then be constructed by dividing the selling price by the number of units and appending the text "per unit value." For example, for a six-pack of soda, number_units_for_price_per_unit= 6, type_of_unit_for_price_per_unit= can, price per unit = price per can.',
    			'amazon_item_type_keyword' => 'ItemType allows customers to find your products as they browse to the most specific item types.',
    			'category_path' => 'The category path for where this product is located in your categorization structure; e.g. A laptop bag might belong in Electronics/Computers/Laptops/Accessories/Bags in your products category',
    			
    			'package_length_inches' => 'Length of the merchant SKU when in its shippable configuration. A number >= 0.01',
    			'package_width_inches' => 'Width of the merchant SKU when in its shippable configuration. A number >= 0.01',
    			'package_height_inches' => 'Height of the merchant SKU when in its shippable configuration. A number >= 0.01',
    			'display_length_inches' => 'Length of the merchant SKU when in its fully assembled/usable condition. A number >= 0.01',
    			'display_width_inches' => 'Width of the merchant SKU when in its fully assembled/usable condition. A number >= 0.01',
    			'display_height_inches' => 'Height of the merchant SKU when in its fully assembled/usable condition. A number >= 0.01',
    			
    			'prop_65' => 'You must tell us if your product is subject to Proposition 65 rules and regulations. Proposition 65 requires merchants to provide California consumers with special warnings for products that contain chemicals known to cause cancer, birth defects, or other reproductive harm, if those products expose consumers to such materials above certain threshold levels. The default value for this is "false," so if you do not populate this column, we will assume your product is not subject to this rule. Please view this website for more information: http://www.oehha.ca.gov/.',
    			'legal_disclaimer_description' => 'Any legal language required to be displayed with the product.',
    			'cpsia_cautionary_statements' => 'Use this field to indicate if a cautionary statement relating to the choking hazards of childrens toys and games applies to your product. These cautionary statements are defined in Section 24 of the Federal Hazardous Substances Act and Section 105 of the Consumer Product Safety Improvement Act of 2008. They must be displayed on the product packaging and in certain online and catalog advertisements. You are responsible for determining if a cautionary statement applies to the product. This can be verified by contacting the product manufacturer or checking the product packaging. Cautionary statements that you select will be displayed on the product detail page. If no cautionary statement applies to the product, select "no warning applicable".',
    			'country_of_origin' => 'The country that the item was manufactured in. Maximum 50 characters',
    			'safety_warning' => 'If applicable, use to supply any associated warnings for your product. Maximum 2000 characters',
    			'msrp' => 'The manufacturer\'s suggested retail price or list price for the product',
    			'product_tax_code' => 'Jet\'s standard code for the tax properties of a given product.',
    			'alternate_images' => 'Any additional images you would like to include for the product'
    	];
    }
    /*
     * Function to create array for JET uploading
     */
    public function formatProductUploadJSON(){
    	$bulletsArray = explode(",",$this->bullets); 	
    	if ($this->prop_65=="True"){
    		$prop_65 =true;
    	} else {
    		$prop_65 =false;
    	}
    	$jet = array (
    		//-----------Required
    			"product_title"=>$this->product_title,
    			"standard_product_codes"=>array (array( // required if ASIN not provided
    					"standard_product_code"=>$this->standard_product_code,
    					"standard_product_code_type"=>$this->standard_product_code_type
    			)),
    			"multipack_quantity"=>$this->multipack_quantity,
    			"brand"=>$this->brand,
    			"main_image_url"=>$this->main_image_url,
    			 
    		//-----------Minimum Listing Data
    			"product_description"=>$this->product_description,
    			"manufacturer"=>$this->manufacturer,
    			"mfr_part_number"=>$this->mfr_part_number,
    			"bullets"=>$bulletsArray, //should be an array up to 5 items
    			"shipping_weight_pounds"=>$this->shipping_weight_pounds,
    			"map_price"=>(int)$this->map_price, //Minimum Advertised Price (MAP)
    			"map_implementation"=>$this->map_implementation, //101 is the normal
    			"jet_browse_node_id"=>$this->jet_browse_node_id,
    			"attributes_node_specific"=>$this->attributes_node_specific, //Should be an array
    			 
    		//-----------Optional Params
    			"number_units_for_price_per_unit"=>$this->number_units_for_ppu,
    			"type_of_unit_for_price_per_unit"=>$this->type_of_unit_for_ppu,
    			"package_length_inches"=>$this->package_length_inches,
    			"package_width_inches"=>$this->package_width_inches,
    			"package_height_inches"=>$this->package_height_inches,
    			"display_length_inches"=>$this->display_length_inches,
    			"display_width_inches"=>$this->display_width_inches,
    			"display_height_inches"=>$this->display_height_inches,
    			"prop_65"=>$prop_65,
    			"legal_disclaimer_description"=>$this->legal_disclaimer_description,
    			"cpsia_cautionary_statements"=>$this->cpsia_cautionary_statements, //should be an array
    			"country_of_origin"=>$this->country_of_origin,
    			"safety_warning"=>$this->safety_warning,
    			"msrp"=>$this->msrp,
    			"product_tax_code"=>$this->product_tax_code,
    			//"swatch_image_url"=>"http://www.mydomain.com/images/myImage.png",
    			"alternate_images"=>$this->alternate_images //should be an array
    	);
    	if ($this->ASIN){
    			$jet["ASIN"]=$this->ASIN; //required if standard product codes array not provided
    	}
    	$jet = json_encode($jet);
    	return $jet;
    }
}
