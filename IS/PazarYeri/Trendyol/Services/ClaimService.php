<?php

namespace IS\PazarYeri\Trendyol\Services;

use IS\PazarYeri\Trendyol\Helper\Request;
use IS\PazarYeri\Trendyol\Helper\TrendyolException;

Class ClaimService extends Request
{
    /**
	 *
	 * Default API Url Adresi
	 *
	 * @author Özer Özdaş <ozer@ozdas.org>
	 * @var string
	 *
	 */
	public $apiUrl = 'https://api.trendyol.com/sapigw/suppliers/{supplierId}/claims?claimItemStatus=Accepted';
    
	/**
	 *
	 * Request sınıfı için gerekli ayarların yapılması
	 *
	 * @author Özer Özdaş <ozer@ozdas.org>
	 *
	 */
	public function __construct($supplierId, $username, $password, $testmode)
	{
		parent::__construct($this->apiUrl, $supplierId, $username, $password, $testmode);
	}

    /**
	 *
	 * Trendyol sisteminde oluşan muhasebesel kayıtlarınızı bu servis aracılığı ile entegrasyon üzerinden çekebilirsiniz.
	 *
	 * @author Özer Özdaş <ozer@ozdas.org>
	 * @return array 
	 *
	 */
	public function getClaims($data = array())
	{
		$query = array(
            'startDate'         => '',
            'endDate'           => '',
            'page'              => '',
            'size'              => '',
            'supplierId'        => '',
            'claimIds'          => '',
            'claimItemStatus'   => '',
            'orderNumber'       => '',
		);
		$this->setApiUrl($this->apiUrl);
		return $this->getResponse($query, $data);
	}
}