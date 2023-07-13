<?php

namespace Omnipay\Vindicia\Message;

use stdClass;

/**
 * Creates or updates a chargeback
 *
 * For more details, refer to this Vindicia SOAP API v18 documentation: https://drive.google.com/file/d/1kPGijZrZ4PwrAmPB6RA1F0WD-sc3_Rcv/view?usp=drive_link
 * Parameters:
 * - amount: This chargeback’s settlement amount, which usually matches the amount of the original transaction. In some cases,
 *      customers charge back only part of a transaction. (Vindicia does not provide information on the items that are charged back.)
 * - caseNumber: Your bank’s case number for this Chargeback object, if any.
 * - currency: The ISO 4217 currency code (see www.xe.com/iso4217.htm) of this Chargeback object. This currency applies to the settlement
 *      amount (see the amount attribute). The default is USD.
 * - divisionNumber: The number of your division or group your payment processor used when processing the original Transaction. Chase
 *      Paymentech refers to this number as the Division Number; Litle calls it the Report Group; MeS calls it the Profile ID.
 * - merchantNumber: Your bank’s merchant number, which identifies you as the merchant.
 * - merchantTransactionId: Your unique identifier for the transaction associated with this Chargeback object. If CashBox generated the transaction,
 *      for example, for a recurring bill, CashBox created this ID for you when processing the transaction with your payment processor. If you did not
 *      process the transaction through CashBox, but only reported it to Vindicia, then this ID must match the order number you used when processing
 *      the transaction with your payment processor.
 * - merchantTransactionTimestamp: A time stamp that specifies the date and time when the original transaction occurred.
 * - merchantUserId: Your unique identifier for the account of the customer who conducted the original transaction.
 * - note: Notes on the Chargeback object. Vindicia personnel might make entries here during the dispute process.
 * - presentmentAmount: The amount charged back (in the presentment currency), which usually matches the amount of the original transaction.
 *      Specify this attribute if the original transaction was processed with Chase Paymentech in a currency other than USD.
 * - presentmentCurrency: The ISO4217 currency code(seewww.xe.com/iso4217.htm)of this transaction at presentment. The default is USD.
 * - postedTimestamp: A time stamp that specifies the date and time when the chargeback was posted in the Vindicia database. The difference in time between the chargeback, and this
 *      posted time stamp, will depend on the frequency at which Vindicia downloads chargebacks from your bank or payment processor.
 * - processorReceivedTimestamp: A time stamp that specifies the date and time when your bank received the chargeback from the customer.
 * - reasonCode: The reason code reported by your bank for this Chargeback object. Reason codes vary from bank to bank.
 * - referenceNumber: Your bank’s reference number for this Chargeback object, if any.
 * - status: The current chargeback status in ChargeGuard. A chargeback goes through a life cycle as Vindicia disputes the chargeback on your behalf.
 * - statusChangedTimestamp: A time stamp that specifies the date and time for the last status change.
 * - VID: Vindicia's Globally Unique Identifier (GUID) for this object. When creating a new Chargeback object, leave this field blank;
 *      it will be automatically populated by CashBox.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   $response = $gateway->createChargeback([
 *      "amount" => 108,
 *      "merchantTransactionId" => "VIM21519663",
 *      "referenceNumber" => "789",
 *      "processorReceivedTimestamp" => "2023-07-12T01:00:00Z",
 *      "reasonCode" => "123",
 *      "postedTimestamp" => "2023-07-12T01:00:00Z",
 *      "status" => "Won",
 *   ])->send();
 *
 *   if ($response->isSuccessful()) {
 *       echo "Customer id: " . $response->getVID() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 * </code>
 */
class CreateChargebackRequest extends AbstractRequest
{
    public function getAmount(): ?float
    {
        return $this->getParameter('amount');
    }

    public function setAmount($value): self
    {
        return $this->setParameter('amount', $value);
    }

    public function getCaseNumber(): ?string
    {
        return $this->getParameter('caseNumber');
    }

    public function setCaseNumber($value): self
    {
        return $this->setParameter('caseNumber', $value);
    }

    public function getCurrency(): ?string
    {
        return $this->getParameter('currency');
    }

    public function setCurrency($value): self
    {
        return $this->setParameter('currency', $value);
    }

    public function getDivisionNumber(): ?string
    {
        return $this->getParameter('divisionNumber');
    }

    public function setDivisionNumber($value): self
    {
        return $this->setParameter('divisionNumber', $value);
    }

    public function getMerchantNumber(): ?string
    {
        return $this->getParameter('merchantNumber');
    }

    public function setMerchantNumber($value): self
    {
        return $this->setParameter('merchantNumber', $value);
    }

    public function getMerchantTransactionId(): ?string
    {
        return $this->getParameter('merchantTransactionId');
    }

    public function setMerchantTransactionId($value): self
    {
        return $this->setParameter('merchantTransactionId', $value);
    }

    public function getMerchantTransactionTimestamp(): ?string
    {
        return $this->getParameter('merchantTransactionTimestamp');
    }

    public function setMerchantTransactionTimestamp($value): self
    {
        return $this->setParameter('merchantTransactionTimestamp', $value);
    }

    public function getMerchantUserId(): ?string
    {
        return $this->getParameter('merchantUserId');
    }

    public function setMerchantUserId($value): self
    {
        return $this->setParameter('merchantUserId', $value);
    }

    public function getNote(): ?string
    {
        return $this->getParameter('note');
    }

    public function setNote($value): self
    {
        return $this->setParameter('note', $value);
    }

    public function getPresentmentAmount(): ?float
    {
        return $this->getParameter('presentmentAmount');
    }

    public function setPresentmentAmount($value): self
    {
        return $this->setParameter('presentmentAmount', $value);
    }

    public function getPresentmentCurrency(): ?string
    {
        return $this->getParameter('presentmentCurrency');
    }

    public function setPresentmentCurrency($value): self
    {
        return $this->setParameter('presentmentCurrency', $value);
    }

    public function getPostedTimestamp(): ?string
    {
        return $this->getParameter('postedTimestamp');
    }

    public function setPostedTimestamp($value): self
    {
        return $this->setParameter('postedTimestamp', $value);
    }

    public function getProcessorReceivedTimestamp(): ?string
    {
        return $this->getParameter('processorReceivedTimestamp');
    }

    public function setProcessorReceivedTimestamp($value): self
    {
        return $this->setParameter('processorReceivedTimestamp', $value);
    }

    public function getReasonCode(): ?string
    {
        return $this->getParameter('reasonCode');
    }

    public function setReasonCode($value): self
    {
        return $this->setParameter('reasonCode', $value);
    }

    public function getReferenceNumber(): ?string
    {
        return $this->getParameter('referenceNumber');
    }

    public function setReferenceNumber($value): self
    {
        return $this->setParameter('referenceNumber', $value);
    }

    public function getStatus(): ?string
    {
        return $this->getParameter('status');
    }

    public function setStatus($value): self
    {
        return $this->setParameter('status', $value);
    }

    public function getStatusChangedTimestamp(): ?string
    {
        return $this->getParameter('statusChangedTimestamp');
    }

    public function setStatusChangedTimestamp($value): self
    {
        return $this->setParameter('statusChangedTimestamp', $value);
    }

    public function getVID(): ?string
    {
        return $this->getParameter('VID');
    }

    public function setVID($value): self
    {
        return $this->setParameter('VID', $value);
    }

    protected function getFunction(): string
    {
        return 'update';
    }

    protected function getObject(): string
    {
        return self::$CHARGEBACK_OBJECT;
    }

    private function createChargeback(): stdClass
    {
        $chargeback = new stdClass();
        if ($this->getAmount()) {
            $chargeback->amount = $this->getAmount();
        }

        if ($this->getCaseNumber()) {
            $chargeback->caseNumber = $this->getCaseNumber();
        }

        if ($this->getCurrency()) {
            $chargeback->currency = $this->getCurrency();
        }

        if ($this->getDivisionNumber()) {
            $chargeback->divisionNumber = $this->getDivisionNumber();
        }

        if ($this->getMerchantNumber()) {
            $chargeback->merchantNumber = $this->getMerchantNumber();
        }

        if ($this->getMerchantTransactionId()) {
            $chargeback->merchantTransactionId = $this->getMerchantTransactionId();
        }

        if ($this->getMerchantTransactionTimestamp()) {
            $chargeback->merchantTransactionTimestamp = $this->getMerchantTransactionTimestamp();
        }

        if ($this->getMerchantUserId()) {
            $chargeback->merchantUserId = $this->getMerchantUserId();
        }

        if ($this->getNote()) {
            $chargeback->note = $this->getNote();
        }

        if ($this->getPresentmentAmount()) {
            $chargeback->presentmentAmount = $this->getPresentmentAmount();
        }

        if ($this->getPresentmentCurrency()) {
            $chargeback->presentmentCurrency = $this->getPresentmentCurrency();
        }

        if ($this->getPostedTimestamp ()) {
            $chargeback->postedTimestamp = $this->getPostedTimestamp();
        }

        if ($this->getProcessorReceivedTimestamp()) {
            $chargeback->processorReceivedTimestamp = $this->getProcessorReceivedTimestamp();
        }

        if ($this->getReasonCode ()) {
            $chargeback->reasonCode = $this->getReasonCode();
        }

        if ($this->getReferenceNumber ()) {
            $chargeback->referenceNumber = $this->getReferenceNumber();
        }

        if ($this->getStatus()) {
            $chargeback->status = $this->getStatus();
        }

        if ($this->getStatusChangedTimestamp()) {
            $chargeback->statusChangedTimestamp = $this->getStatusChangedTimestamp();
        }

        if ($this->getVID ()) {
            $chargeback->VID = $this->getVID();
        }

        return $chargeback;
    }

    public function getData(): array
    {
        return  [
            'action' => $this->getFunction(),
            'chargeback' => $this->createChargeback(),
        ];
    }
}
