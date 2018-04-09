<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Payum\Mpay24Bundle\Extension;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Convert;

final class ConvertPaymentExtension implements ExtensionInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Context $context
     */
    public function onPostExecute(Context $context)
    {
        $action = $context->getAction();
        $previousActionClassName = get_class($action);

        if (false === stripos($previousActionClassName, 'ConvertPaymentAction')) {
            return;
        }

        /** @var Convert $request */
        $request = $context->getRequest();

        if (false === $request instanceof Convert) {
            return;
        }

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($payment->getOrderId());
        $invoiceAddress = $order->getInvoiceAddress() ;
        $customer = $order->getCustomer();

        $resultItems = [];
        $resultCustomer = [];

        foreach ($order->getItems() as $index => $item) {
            $product = $item->getProduct();
            $resultItem = [
                'number' => $index + 1,
                'quantity' => $item->getQuantity(),
                'itemPrice' => sprintf("%01.2f", ($item->getItemPrice(true) / 100)),
                'itemTax' => sprintf("%01.2f", ($item->getTotalTax() / 100)),
                'price' => sprintf("%01.2f", ($item->getTotal(true) / 100))
            ];

            if ($product instanceof ProductInterface) {
                $resultItem['productNr'] = $product->getSku();
                $resultItem['description'] = $product->getName($order->getOrderLanguage());
            }

            $resultItems[$index + 1] = $resultItem;
        }

        if ($customer instanceof CustomerInterface) {
            $resultCustomer['id'] = $customer->getId();
            $resultCustomer['name'] = sprintf('%s %s', $customer->getFirstname(), $customer->getLastname());

            if ($invoiceAddress instanceof AddressInterface) {
                $resultCustomer['billingAddress'] = [
                    'name' => sprintf('%s %s', $invoiceAddress->getFirstname(), $invoiceAddress->getLastname()),
                    'street' => $invoiceAddress->getStreet(),
                    'street2' => $invoiceAddress->getNumber(),
                    'zip' => $invoiceAddress->getPostcode(),
                    'city' => $invoiceAddress->getCity(),
                    'countryCode' => $invoiceAddress->getCountry() instanceof CountryInterface ? $invoiceAddress->getCountry()->getIsoCode() : '',
                    'email' => $customer->getEmail()
                ];
            }
        }

        $resultOrder = [
            'language' => strtoupper($order->getOrderLanguage()),
            'items' => $resultItems,
            'customer' => $resultCustomer
        ];


        $result = ArrayObject::ensureArrayObject($request->getResult());
        $result['order'] = $resultOrder;
        $request->setResult((array)$result);
    }

    /**
     * {@inheritdoc}
     */
    public function onPreExecute(Context $context)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onExecute(Context $context)
    {
    }
}