<?php
namespace frontend\controllers;

use common\models\Video;
use darkdrim\simplehtmldom\SimpleHTMLDom;
use frontend\components\YoutubeAPI;
use frontend\models\FindVideoForm;
use frontend\models\VideoSearch;
use frontend\models\YoutubeVideo;
use Yii;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\NotFoundHttpException;

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
        return $this->render('index', [
            'lastAddedProvider' =>  new ActiveDataProvider([
                'query' =>  Video::find()->where('views > 500000'),
                'sort'  =>  [
                    'defaultOrder'  =>  [
                        'checked' =>  SORT_DESC
                    ]
                ]
            ])
        ]);
    }

    public function actionSearchVideo($hash = null){
        $video = new Video();

        if(!empty(\Yii::$app->request->post("FindVideoForm")) && empty($hash)){
            $findVideoForm = new FindVideoForm();
            $findVideoForm->load(\Yii::$app->request->post());
            
            $video = Video::findOne(['youtubeID' => $findVideoForm->videoID]);

            if(!$video){
                $video = new YoutubeVideo();

                $video->link = $findVideoForm->url;

                if(!$video->parse()){
                    throw new NotFoundHttpException("Не удалось распознать видео");
                }

                $video = $video->save();
            }

            $this->redirect('/search-video/'.$video->youtubeID);
        }

        if($hash){
            $video = Video::findOne(['youtubeID' => $hash]);

            if(!$video){
                throw new NotFoundHttpException("Видео не найдено!");
            }
        }

        if($video->isNewRecord){
            throw new NotFoundHttpException("Видео не найдено!");
        }

        if(strtotime($video->checked) < time() - 3600){
            $api = new YoutubeAPI();

            try{
                $video->applyApiData($api->getVideos($video->youtubeID));
            }catch (NotFoundHttpException $e){

            }

            $video->save(false);
        }

        return $this->render('video', [
            'video' =>  $video
        ]);
    }

    public function actionSearch(){
        if(\Yii::$app->request->isAjax){
            $results = [];

            \Yii::$app->response->format = 'json';

            foreach(Video::find()->where(['like', 'name', \Yii::$app->request->get("string")])->limit(10)->all() as $video){
                $video->name = htmlspecialchars_decode(trim($video->name));
                $results[] = [
                    'name'      =>  mb_strlen($video->name) > 60 ? mb_substr($video->name, 0, 60).'...' : $video->name,
                    'youtubeID' =>  $video->youtubeID
                ];
            }

            return $results;
        }

        $videoSearch = new VideoSearch();

        $videoSearch->load(\Yii::$app->request->get());

        return $this->render('search', [
            'searchModel'   =>  $videoSearch,
            'dataProvider'  =>  $videoSearch->search(\Yii::$app->request->get("VideoSearch"))
        ]);
    }

    public function actionRating(){
        return $this->render('rating', [
            'dataProvider'  =>  new ActiveDataProvider([
                'query' =>  Video::find(),
                'pagination'    =>  [
                    'pageSize'  =>  '50'
                ],
                'sort'  =>  [
                    'defaultOrder'   =>  [
                        'views' =>  SORT_DESC
                    ]
                ]
            ])
        ]);
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
}
