<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ProductSearch represents the model behind the search form about `common\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productid', 'cateid', 'num', 'createtime'], 'integer'],
            [['title', 'descr', 'cover', 'pics', 'issale', 'ishot', 'istui', 'ison'], 'safe'],
            [['price', 'saleprice'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'productid' => $this->productid,
            'cateid' => $this->cateid,
            'num' => $this->num,
            'price' => $this->price,
            'saleprice' => $this->saleprice,
            'createtime' => $this->createtime,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'descr', $this->descr])
            ->andFilterWhere(['like', 'cover', $this->cover])
            ->andFilterWhere(['like', 'pics', $this->pics])
            ->andFilterWhere(['like', 'issale', $this->issale])
            ->andFilterWhere(['like', 'ishot', $this->ishot])
            ->andFilterWhere(['like', 'istui', $this->istui])
            ->andFilterWhere(['like', 'ison', $this->ison]);

        return $dataProvider;
    }
}
