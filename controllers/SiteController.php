<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use app\models\Shopify;
use app\models\Jet;
use app\models\jet_FulfillmentNodes;
use yii\helpers\Json;
use app\models\jet_Taxonomy;

/**
 * Site controller
 */
class SiteController extends Controller
{
	
	
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
/***********************************************************
 * Shopify Actions
 */
	private function shopifyLogin(){
		$session = Yii::$app->session;
		//First check if we are authenticated in Shopify
		if ($session->get('shop') && Yii::$app->controller->action->id != "oauth"){
			$shopify_init = Jet::find()->where(['shop' => $session->get('shop')])->one();
		} else {
			$shop = Yii::$app->request->headers->get('forwarded-request-uri');
			$shop = explode("&", $shop);
			foreach ($shop as $value) {
				$pos = strpos( $value, 'shop' );
				if ( $pos !== false) {
					$shop = substr($value, '5');
					break;
				}
			}
			$session->set('shop', $shop);
			$shopify_init = jet::find()->where(['shop' => $shop])->one();
		}
		
		//Set session oauth variable and shop if it is not stored in the sessions (after a timeout or on initial login)
		if (!$session->get('jet_api_key') || !$session->get('jet_pass')){
			//Check DB for stored keys/pass
		
			if ($shopify_init->jet_api_key == NULL || $shopify_init->jet_pass == NULL ){
				//Keys are not stored in DB, direct user to settings page to add them.
				$message = "You do not have your Jet API and/or Jet password set.  Please add these under <a href='./settings'>Settings</a> and try again.";
				
				echo "<pre>";
				print_r($message);
				echo "</pre>";
				
		
			} else {
				//Keys are stored in DB, Connect to Jet and check credentials
				$result = $shopify_init->connectJet();
				echo "<pre>";
				print_r($result);
				echo "</pre>";
			}
		}
		
		return $shopify_init;
	}
	
    public function actionInstall()
    {
    	return $this->render('install');
    }
    
    //after a site has been authorized
    public function actionOauth()
    {
    	//Check if Shop needs to be installed / already exists
    	$var = Yii::$app->request->headers->get('forwarded-request-uri');
    	$var = explode("&", $var);
    	$variables = array();
    	foreach ($var as $value) {
			$temp = explode("=",$value);
			$variables[$temp[0]]=$temp[1];
    	}
    	if ($shopify=Shopify::find()->where(['shop'=>$variables['shop']])->one()){
    		//validate shop is authenticated
    		$message = "locale=en&protocol=https://&shop=" . $variables['shop'] . "&timestamp=" . $variables['timestamp'];
    		$calculatedHmac = hash_hmac('sha256', $message, Yii::$app->params['shopify']['secret']);

    		if ($calculatedHmac!=$variables['/oauth?hmac']){
    			$url = "./install?shop=" . $variables['shop'];
    			header( 'Location: ' . $url) ;
    		}
    	} else {
    		$url = "./install?shop=" . $variables['shop'];
    		header( 'Location: ' . $url);
    	}
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('oauth', ['shopify_init'=>$shopify_init]);
    
    }
    
    public function actionProducts()
    { 
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('products', ['shopify_init'=>$shopify_init]);
    }
    public function actionOrders2()
    {
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('orders2', ['shopify_init'=>$shopify_init]);
    }
    public function actionPrice()
    {
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('price', ['shopify_init'=>$shopify_init]);
    }
    public function actionInventory()
    {
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('inventory', ['shopify_init'=>$shopify_init]);
    }
    public function actionSettings()
    {
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('settings', ['shopify_init'=>$shopify_init]);
    }
    public function actionHelp()
    {
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('help', ['shopify_init'=>$shopify_init]);
    }
    public function actionTaxonomy()
    {
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('taxonomy', ['shopify_init'=>$shopify_init]);
    }
    public function actionReturns()
    {
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('returns', ['shopify_init'=>$shopify_init]);
    }
    public function actionReturnOrder()
    {
    	$this->layout = 'shopify';
    	$shopify_init = $this->shopifyLogin();
    	return $this->render('return-order', ['shopify_init'=>$shopify_init]);
    }
   
//==================== AJAX Actions
    public function actionAjaxProductUpdate()
    {
    	return $this->renderAjax('_ajax-product-update');
    }
    public function actionAjaxFulfillmentUpdate()
    {
    	return $this->renderPartial('_ajax-fulfillment-update');
    }
    public function actionAjaxFulfillmentDelete()
    {
    	return $this->renderPartial('_ajax-fulfillment-delete');
    }
    public function actionAjaxFulfillmentAdd()
    {
    	return $this->renderPartial('_ajax-fulfillment-add');
    }
    public function actionAjaxInventorySend()
    {
    	return $this->renderPartial('_ajax-inventory-send');
    }
    public function actionAjaxReturnComplete()
    {
    	return $this->renderPartial('_ajax-return-complete');
    }
    public function actionAjaxTaxonomySave(){
    	return $this->renderPartial('_ajax-taxonomy-save');	
    }
    public function actionAjaxTaxonomyGet($q = null, $id = null) 
    {
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	$out = ['results' => ['id' => '', 'text' => '']];
    	if (!is_null($q)) {
    		$data = jet_Taxonomy::find()
    		->where(['like', 'jet_node_name', $q])
    		->asArray()
    		->select(['id'=>'jet_node_id', 'text'=>'jet_node_name', 'jet_level', 'parent_id'])
    		->all();
    		foreach ($data as $key=>$row){
    			if($row['jet_level']!='0'){
    				$parent = jet_Taxonomy::find()->where(['jet_node_id'=>$row['parent_id']])->asArray()->one();
    				$temptext = $parent['jet_node_name'] . ": <b>" . $data[$key]['text'] . "</b>";
    				$data[$key]['text'] = $temptext;
    		
    				if ($row['jet_level']=='2'){
    					$parentParent = jet_Taxonomy::find()->where(['jet_node_id'=>$parent['parent_id']])->asArray()->one();
    					$temptext = $parentParent['jet_node_name'] . ": " . $temptext;
    					$data[$key]['text'] = $temptext;
    				}
		
    			}
    		}
    		$sortName = array(); 
    		foreach ($data as $taxName) {    
    			$sortName[] = $taxName['text'];
    		} 
    		array_multisort($data, SORT_ASC, $sortName);
    		$out['results'] = $data;
    	
    	}
    	elseif ($id > 0) {
    		$out['results'] = ['id' => $id, 'text' => jet_Taxonomy::find($id)->jet_node_name];
    	}
    	return( $out);
    }
    
    
//===================Partial rederings of rows for GridView
    public function actionExpandRowProductDetails()
    {
    	return $this->renderPartial('_expand-row-product-details');
    }
    public function actionExpandRowInventoryDetails()
    {
    	return $this->renderPartial('_expand-row-inventory-details');
    }
    public function actionExpandRowReturnDetails()
    {
    	return $this->renderPartial('_expand-row-return-details');
    }
    
//======================Settings views
    public function actionSettingsJetApiKeys()
    {
    	return $this->renderPartial('_settings-jet-api-keys');
    }
    public function actionSettingsJetFulfillmentNodeDetails()
    {
    	return $this->renderPartial('_settings-jet-fulfillment-node-details');
    }  
    public function actionSettingsWebhooks()
    {
    	return $this->renderPartial('_settings-webhooks');
    }
    
//=================== CRONJOB Views
    public function actionCronjobAcknowledgeOrders()
    {
    	return $this->renderPartial('_cronjob-acknowledge-orders');
    }
    
    public function actionCronjobUpdateOrders()
    {
    	return $this->renderPartial('_cronjob-update-orders');
    }

    
//=================== Webhook Views
    public function beforeAction($action)
    {
    	if (in_array($action->id, ['webhook-update-jet-order', 'webhook-fulfill-jet-order', 'webhook-acknowledge-jet-return'])) {
    		$this->enableCsrfValidation = false;
    	}
    	return parent::beforeAction($action);
    }
    
    public function actionWebhookUpdateJetOrder()
    {
    	return $this->renderPartial('_webhook-update-jet-order');
    }
    public function actionWebhookFulfillJetOrder()
    {
    	return $this->renderPartial('_webhook-fulfill-jet-order');
    }

    
//----------------- Test page
    public function actionTest()
    {
    	// non-ajax - render the grid by default
    	return $this->render('test');
    }
    
}
