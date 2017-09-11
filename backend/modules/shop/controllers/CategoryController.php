<?php
namespace backend\modules\shop\controllers;
use Yii;
use backend\modules\shop\models\Category;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
//use common\components\MyHelpers ;
use yii\filters\AccessControl;
/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view','create','delete','update', 'index'],
                        'allow' => true,
                        'roles' => ['appadmin']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $rows = $this->query_categories();
//        MyHelpers::p($rows) ;

        $rows =    \common\components\Category::unlimitedForLevel1($rows) ;



//        MyHelpers::p($rows) ;
//        die() ;

        $provider = new \yii\data\ArrayDataProvider([
            'allModels' => $rows,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'attributes' => ['id', 'name'],
            ],
        ]);

//        $dataProvider = new ActiveDataProvider([
//            'query' => Category::find(),
//        ]);




        return $this->render('index', [
            'dataProvider' => $provider,
        ]);
    }

    /**
     * Displays a single Category model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            $rows = $this->query_categories();
            $rows =    \common\components\Category::unlimitedForLevel1($rows) ;

            $rows = $this->build_drop_down_tree($rows);

//        MyHelpers::p($rows) ;
//            foreach ($rows as $item)
//            {
//                MyHelpers::p($item  ['key'] );
//                MyHelpers::p($item  ['val'] );
//            }


            return $this->render('create', [
                'model' => $model,'rows'=>$rows
            ]);
        }
    }
    private function build_drop_down_tree($rows){
      $rtn_array =array();
        foreach ($rows as $key => $value )
        {
            if ($value['level']>1)
                array_push($rtn_array,[   'key'=>$value['id']  , 'val'=>$value['html'] . $value['name']]);
            else
             array_push($rtn_array,[ 'key'=>$value['id'],'val' =>$value['name']]);

        }
        return $rtn_array;

    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return array
     */
    public function query_categories()
    {
        $rows = (new \yii\db\Query())
            ->select(['id', 'pid', 'title as name'])
            ->from('shop_category')
            ->all();
        return $rows;
    }
}
