<?php

namespace IS\PazarYeri\Trendyol\Services;

use IS\PazarYeri\Trendyol\Helper\Request;

Class QuestionService extends Request
{

	/**
	 *
	 * Default API Url Adresi
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 * @var string
	 *
	 */

    public $baseApiUrl = 'https://apigw.trendyol.com/integration/qna/sellers/{supplierId}/questions/filter/';

	public $apiUrl = 'https://apigw.trendyol.com/integration/qna/sellers/{supplierId}/questions/filter/';

	/**
	 *
	 * Request sınıfı için gerekli ayarların yapılması
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 *
	 */
	public function __construct($supplierId, $username, $password,$testmode)
	{
		parent::__construct($this->apiUrl, $supplierId, $username, $password, $testmode);
	}


    /**
     * @param array $data
     * @return array
     * @throws \IS\PazarYeri\Trendyol\Helper\TrendyolException
     */
	public function getQuestions(array $data = array())
	{
        $this->setApiUrl($this->baseApiUrl);
        //$this->setApiUrl($this->apiUrl . 'filter');

        $query = [
            'barcode' => '',
            'page' => '',
            'size' => '',
            'startDate' => '',
            'endDate' => '',
            'status' => '', // WAITING_FOR_ANSWER, WAITING_FOR_APPROVE, ANSWERED, REPORTED, REJECTED
            'orderByField' => '',
            'orderByDirection' => ''

        ];

        return $this->getResponse($query, $data, true);
	}

    public function createAnswer($data)
    {
        /**$this->setApiUrl($this->baseApiUrl);
        $questionId = $data['questionId'] ?? 0;
        $this->setApiUrl($this->apiUrl . $questionId . '/answers');
        $this->setMethod("POST");**/

        $sellerId = $data['sellerId'] ?? 0;
        $questionId = $data['questionId'] ?? 0;

        $this->setApiUrl("https://apigw.trendyol.com/integration/qna/sellers/{$sellerId}/questions/{$questionId}/answers");
        $this->setMethod("POST");

        $query = [
            'text' => ''
        ];

        unset($data['questionId']);

        return $this->getResponse($query, $data, true);
    }

}
