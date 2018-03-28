# CoreShop Mpay24 Payum Connector
This Bundle activates the Mpay24 PaymentGateway in CoreShop.
It requires the [coreshop/payum-mpay24](https://github.com/coreshop/payum-mpay24) repository which will be installed automatically.

## Installation

#### 1. Composer
```json
    "coreshop/payum-mpay24-bundle": "~1.0.0"
```

#### 2. Activate
Enable the Bundle in Pimcore Extension Manager

#### 3. Setup
Go to Coreshop -> PaymentProvider and add a new Provider. Choose `mpay24` from `type` and fill out the required fields.

