<?php

namespace Inisiatif\Midtrans\Channel\Transfer;

use Webmozart\Assert\Assert;
use Inisiatif\Midtrans\Model\Customer;
use Inisiatif\Midtrans\Model\TransactionItem;
use Inisiatif\Midtrans\Response\ChargeResponse;
use Inisiatif\Midtrans\Model\TransactionDetail;
use Inisiatif\Midtrans\Contract\ChannelContract;

class Bni extends ChannelContract
{

    /**
     * @return mixed
     */
    protected function makePayloads()
    {
        Assert::isInstanceOf($this->getCustomer(), Customer::class);
        Assert::isInstanceOf($this->getTransaction(), TransactionDetail::class);
        Assert::isArray($this->getItems());

        $items = [];
        /** @var TransactionItem $item */
        foreach ($this->getItems() as $item) {
            array_push($items, $item->toArray());
        }

        $payloads = array_merge([
            'payment_type' => 'bank_transfer',
            'bank_transfer' => [
                'bank' => 'bni',
                'va_number' => $this->getTransaction()->getId()
            ],
            'customer_details' => $this->getCustomer()->toArray(),
            'item_details' => $items,
        ], $this->getTransaction()->toArray());

        $this->setPayloads($payloads);

        return $this;
    }

    /**
     * @param $rawResponse
     * @return ChargeResponse
     */
    public function makeResponse($rawResponse)
    {
        $response = new ChargeResponse;
        $response->setId($rawResponse->transaction_id);
        $response->setStatusCode($rawResponse->status_code);
        $response->setMessage($rawResponse->status_message);
        $response->setType($rawResponse->payment_type);
        $response->setDateTime($rawResponse->transaction_time);
        $response->setStatus($rawResponse->transaction_status);
        $response->setFraud($rawResponse->fraud_status);
        $response->setOrderId($rawResponse->order_id);
        $response->setOrderAmount($rawResponse->gross_amount);
        $response->setVirtualAccount(end($rawResponse->va_numbers)->va_number);
        $response->setRawResponse($rawResponse);

        return $response;
    }
}