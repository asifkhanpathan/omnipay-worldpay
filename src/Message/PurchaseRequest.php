<?php

namespace Omnipay\WorldPay\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * WorldPay Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://secure.worldpay.com/wcc/purchase';
    protected $testEndpoint = 'https://secure-test.worldpay.com/wcc/purchase';

    public function setSignatureFields($value)
    {
        return $this->setParameter('signatureFields', $value);
    }

    public function getSignatureFields()
    {
        return $this->getParameter('signatureFields');
    }

    public function getInstallationId()
    {
        return $this->getParameter('installationId');
    }

    public function setInstallationId($value)
    {
        return $this->setParameter('installationId', $value);
    }

    public function getAccountId()
    {
        return $this->getParameter('accountId');
    }

    public function setAccountId($value)
    {
        return $this->setParameter('accountId', $value);
    }

    public function getSecretWord()
    {
        return $this->getParameter('secretWord');
    }

    public function setSecretWord($value)
    {
        return $this->setParameter('secretWord', $value);
    }

    public function getCallbackPassword()
    {
        return $this->getParameter('callbackPassword');
    }

    public function setCallbackPassword($value)
    {
        return $this->setParameter('callbackPassword', $value);
    }

    /**
     * Pre-selects the card type being used and bypasses the card type selection screen.
     * Must match one of: https://support.worldpay.com/support/kb/bg/customisingadvanced/custa9102.html
     *
     * @param string
     */
    public function getPaymentType()
    {
        return $this->getParameter('paymentType');
    }

    public function setPaymentType($value)
    {
        return $this->setParameter('paymentType', $value);
    }

    /**
     * If true, hides WorldPay's language selection menu.
     *
     * @param boolean
     */
    public function getNoLanguageMenu()
    {
        return $this->getParameter('noLanguageMenu');
    }

    public function setNoLanguageMenu($value)
    {
        return $this->setParameter('noLanguageMenu', $value);
    }

    /**
     * If true, prevents editing of address details by user.
     *
     * @param boolean
     */
    public function getFixContact()
    {
        return $this->getParameter('fixContact');
    }

    public function setFixContact($value)
    {
        return $this->setParameter('fixContact', $value);
    }

    /**
     * If true, hides address details from user.
     *
     * @param boolean
     */
    public function getHideContact()
    {
        return $this->getParameter('hideContact');
    }

    public function setHideContact($value)
    {
        return $this->setParameter('hideContact', $value);
    }

    /**
     * If true, hides currency options from user.
     *
     * @param boolean
     */
    public function getHideCurrency()
    {
        return $this->getParameter('hideCurrency');
    }

    public function setHideCurrency($value)
    {
        return $this->setParameter('hideCurrency', $value);
    }

    public function getAddress3()
    {
        return $this->getParameter('address3');
    }

    public function setAddress3($value)
    {
        return $this->setParameter('address3', $value);
    }


    public function getData()
    {
        $this->validate('amount');

        // Either the nodifyUrl or the returnUrl can be provided.
        // The returnUrl is deprecated, as strictly this is a notifyUrl.
        if (!$this->getNotifyUrl()) {
            $this->validate('returnUrl');
        }

        $data = array();
        $data['instId'] = $this->getInstallationId();
        $data['accId1'] = $this->getAccountId();
        $data['cartId'] = $this->getTransactionId();
        $data['desc'] = $this->getDescription();
        $data['amount'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['testMode'] = $this->getTestMode() ? 100 : 0;
        $data['MC_callback'] = $this->getNotifyUrl() ?: $this->getReturnUrl();
        $data['successURL'] = $this->getReturnUrl();
        $data['CM_cancel_payment_url'] = $this->getCancelUrl();
        $data['paymentType'] = $this->getPaymentType();
        $data['noLanguageMenu'] = $this->getNoLanguageMenu();
        $data['fixContact'] = $this->getFixContact();
        $data['hideContact'] = $this->getHideContact();
        $data['hideCurrency'] = $this->getHideCurrency();

        if ($this->getCard()) {
            $data['name'] = $this->getCard()->getName();
            $data['address1'] = $this->getCard()->getAddress1();
            if ($this->getCard()->getAddress2()) {
                $data['address2'] = $this->getCard()->getAddress2();
            }
            // as address 3 is not in abstract creditCard class so we change key address3 
            if ($this->getAddress3()) {
                $data['address3'] = $this->getAddress3();
            }
            if ($this->getCard()->getState()) {
                $data['region'] = $this->getCard()->getState();
            }

            $data['town'] = $this->getCard()->getCity();
            $data['postcode'] = $this->getCard()->getPostcode();
            $data['country'] = $this->getCard()->getCountry();
            $data['tel'] = $this->getCard()->getPhone();
            $data['email'] = $this->getCard()->getEmail();
        }

        if ($this->getSecretWord()) {
            $data['signatureFields'] = $this->getSignatureFields();
            $signature_data = array($this->getSecretWord());
            foreach (explode(':', $data['signatureFields']) as $parameterName) {
                $signature_data[] = $data[$parameterName];
            }

            $data['signature'] = md5(implode(':', $signature_data));
        }

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
