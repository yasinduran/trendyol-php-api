<?php

namespace IS\PazarYeri\Trendyol\Services;

use IS\PazarYeri\Trendyol\Helper\Request;
use IS\PazarYeri\Trendyol\Helper\TrendyolException;

Class FinanceService extends Request
{
    /**
     *
     * Default API Url Adresi
     *
     * @var string
     *
     */
	public $apiUrl = 'https://api.trendyol.com/integration/finance/che/sellers/{supplierId}/otherfinancials';
    
	/**
     *
     * Request sınıfı için gerekli ayarların yapılması
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
     * @return array
     *
     */
	public function getFinancials($data = array())
	{
		$query = array(
            'transactionType'   => array('required' => array("CashAdvance", "WireTransfer", "IncomingTransfer", "ReturnInvoice", "CommissionAgreementInvoice", "PaymentOrder", "DeductionInvoices", "Stoppage")),
            'startDate'         => '',
            'endDate'           => '',
            'page'              => '',
            'size'              => '',
            'supplierId'        => '',
		);
		$this->setApiUrl($this->apiUrl);
		return $this->getResponse($query, $data);
	}
}