<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Products;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\Url;

$session = Yii::$app->session;
$this->title = 'Help';
?>
<h2>Getting Started</h2>
<p> Need to write steps code to automate teh Jet setup</p>
<h3>Taxonomy</h3>
<p>If you update your taxonomy, your products will not automatically be updated.</p>

<h2>Products</h2>
<h3>Uploading Products to Jet</h3>
<h3>Updating Inventory to Jet</h3>
<h3>Updating Price to Jet</h3>

<h2>Orders</h2>
<h3>New Orders From Jet</h3>
<h4>New Orders</h4>
<h4>Updated Orders / Cancelled Line Items</h4>
<h3>Fulfilling Orders from Shopify</h3>
<h4>Updating fulfillment Info</h4>
<h2>Returns</h2>
<h3>Checking for Returns</h3>