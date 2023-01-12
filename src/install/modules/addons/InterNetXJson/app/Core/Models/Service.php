<?php

namespace MGModule\InterNetX\Addon\Core\Models;

class Service extends \WHMCS\Service\Service
{

    public function client()
    {
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\Client', 'id', 'userid');
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getLicenseType()
    {
        if ($this->isDev()) {
            return 'Dev';
        }

        if ($this->isMagentoTrial()) {
            return 'Free Live Trial';
        }

        if ($this->isFree()) {
            return 'Lifetime';
        }

        if ($this->isOpenSource() || $this->isMagento()) {
            return 'Open Source';
        }

        if ($this->isOneTime()) {
            return 'Lifetime';
        }


        $matrix = [
            'Annually' => 'Annual'
        ];

        $billing = $this->getBillingCycle();

        return array_key_exists($billing, $matrix) ? $matrix[$billing] : $billing;
    }

    public function getBillingCycle()
    {
        if ($this->isFree() || $this->isMagentoTrial() || $this->isDev()) {
            return 'Free';
        }


        if ($this->isOneTime() || $this->isMagento() || $this->isOpenSource()) {
            return 'One Time';
        }


        /* if($this->isTrial())
          {
          return '7-Day Free';
          } */


        return $this->billingcycle;
    }

    /**
     * Nie pytaj. Zakładamy że serwis ma odnowienia ( albo będzie miał ) jeżeli mineł0 300 dni od duedate
     * Nie możemy bazować na fakturach bo może ich po prostu nie być 
     * @TODO Implement me! Sprawdzanie czy to na pewno recurring (ten nasz recurring a nie WHMCS)
     */
    public function hasRenewals()
    {
        if (in_array($this->billingcycle, array('Free Account', 'One Time'))) {
            return false;
        }

        if ($this->overideautosuspend) {
            return false;
        }

        return true;

        //return strtotime($this->duedate) - strtotime($this->regdate) > 60 * 60 * 24 * 300;
    }

    /**
     * 
     * @return boolean
     */
    public function hasUnpaidSupportInvoice()
    {
        return true;
    }

    public function isMagento()
    {
        return strpos($this->product->name, 'Magento') !== false;
    }

    public function isOldTrial()
    {
        return strpos($this->product->name, 'TRIAL') !== false;
    }

    /**
     * 
     * @return type
     * @TODO poprawić wykrywanie za pomocą custom field
     */
    public function isOneTime()
    {
        $custoFields = $this->getCustomFields();

        return \array_key_exists('OneTime', $custoFields) && $custoFields['OneTime'] == 'onetime';
    }

    /**
     * 
     * @return boolean
     */
    public function isRecurring()
    {
        if (in_array($this->billingcycle, array('Free Account', 'One Time'))) {
            return false;
        }

        if ($this->overideautosuspend) {
            return false;
        }

        if ($this->isOpenSource()) {
            return false;
        }

        if ($this->isMagento()) {
            return false;
        }

        return true;
    }

    /**
     * @TODO dodać spradzanie czy faktura jest nieopłacn
     * @return type
     */
    public function isTrial()
    {
        if (!$this->order) {
            return false;
        }

        return $this->order->isTrial();
    }

    /**
     * @TODO implement me
     */
    public function isFree()
    {
        return $this->billingcycle == 'Free Account';
    }

    /**
     * 
     * @return type
     */
    public function isOpenSource()
    {
        return stripos($this->product->name, 'Open Source') !== false;
    }

    public function isCpanel()
    {
        return stripos($this->product->name, 'For cPanel') !== false;
    }

    public function isMagentoTrial()
    {
        return stripos($this->product->name, 'Magento') !== false && stripos($this->product->name, 'Trial') !== false;
    }

    public function isDev()
    {
        $pdo = \Illuminate\Database\Capsule\Manager::connection()->getPdo();
        $q = $pdo->prepare("
            SELECT
                *
            FROM
                tblhostingconfigoptions co
            WHERE 
                    co.relid = ?
                AND
                    co.configid = ?
                AND
                    co.optionid = ?
                AND
                    co.qty = ?
        ");

        $q->execute([
            $this->id,
            1,
            1,
            1
        ]);

        return $q->rowCount() ? true : false;
    }

    /**
     * 
     * @return string
     */
    public function getType()
    {
        logActivity(\var_export((int) $this->isMagentoTrial().$this->product->name, true));

        if ($this->isMagentoTrial()) {
            return 'magento_trial';
        }

        if ($this->isFree()) {
            return 'free';
        }

        if ($this->isCpanel()) {
            return 'licensed';
        }

        if ($this->isMagento()) {
            return 'magento';
        }

        if ($this->isOpenSource()) {
            return 'open_source';
        }

        if ($this->isTrial()) {
            return 'trial';
        }

        if ($this->isOneTime()) {
            return 'onetime';
        }

        return 'licensed';
    }

    public function upgrades()
    {
        return $this->hasMany('MGModule\InterNetX\Addon\Core\Models\Product\Upgrades', 'product_id', 'packageid');
    }

    /**
     * Get order
     * @return type
     */
    public function order()
    {
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\Order', 'id', 'orderid');
    }

    public function hasActiveDownload()
    {
        return false;
    }

    public function getUpgradeInvoices($status = '')
    {
        $query = \InterNetX\Models\Invoice
            ::distinct()
            ->join('tblinvoiceitems', 'tblinvoiceitems.invoiceid', '=', 'tblinvoices.id')
            ->join('tblupgrades', 'tblinvoiceitems.relid', '=', 'tblupgrades.id')
            ->where('tblinvoiceitems.type', 'Upgrade')
            ->where('tblupgrades.type', 'package')
            ->where('tblupgrades.relid', $this->id)
            ->orderBy('datepaid', 'DESC');

        if ($status) {
            $query->where('tblinvoices.status', $status);
        }

        return $query->get(['tblinvoices.*']);
    }

    public function getInvoices($status = '')
    {
        $query = \InterNetX\Models\Invoice::join('tblinvoiceitems', 'tblinvoiceitems.invoiceid', '=', 'tblinvoices.id')
            ->distinct()
            ->where('tblinvoiceitems.relid', $this->id)
            ->where('tblinvoiceitems.type', 'Hosting')
            ->orderBy('datepaid', 'DESC');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get(['tblinvoices.*']);
    }

    /**
     * Get Service invoices
     * @return type
     */
    public function invoices()
    {
        return \InterNetX\Models\Invoice
                ::join('tblinvoiceitems', 'tblinvoiceitems.invoiceid', '=', 'tblinvoices.id')
                ->where('tblinvoiceitems.relid', $this->id)
                ->where('tblinvoiceitems.type', 'Hosting')
                ->orderBy('datepaid', 'DESC')
                ->get(['tblinvoices.*']);
    }

    public function invoiceItems()
    {
        return $this->hasMany('MGModule\InterNetX\Addon\Core\Models\Invoice\Item', 'relid', 'id')
                ->where(function($query) {
                    $query->where('type', 'Hosting')
                    ->orWhere('type', 'Setup');
                });
    }

    public function getCustomFields()
    {
        $customFields = [];

        foreach ($this->customFieldsValues as $value) {
            if (isset($value->customField->fieldname)) {
                $customFields[$value->customField->fieldname] = $value->value;
            }
        }

        return $customFields;
    }

    public function setEmptyCustomFields()
    {
        if (count($this->customFieldsValues) == 0) {
            $serviceCustomFields = CustomField::where('type', 'product')->where('relid', $this->packageid)->get();

            foreach ($serviceCustomFields as $serviceCustomField) {
                $customfieldValue = new CustomFields\Value();
                $customfieldValue->fieldid = $serviceCustomField->id;
                $customfieldValue->relid = $this->id;
                $customfieldValue->value = "";
                $customfieldValue->save();
            }

            return true;
        }

        return false;
    }

    public function setCustomFieldValue($fieldname, $newValue)
    {
        foreach ($this->customFieldsValues as $value) {
            if (isset($value->customField->fieldname) && $value->customField->fieldname == $fieldname) {
                $customFieldValue = \InterNetX\Models\CustomFields\Value::where("relid", $value->relid)->where("fieldid", $value->fieldid);
                $customFieldValue->update(["value" => $newValue]);
                return;
            }
        }
    }

    /**
     * 
     * @return type
     */
    public function customFieldsValues()
    {
        return $this->hasMany('MGModule\InterNetX\Addon\Core\Models\CustomFields\Value', 'relid', 'id');
    }

    /**
     *
     * @return type
     */
    public function serverDetails()
    {
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\Service\Server', 'id', 'server');
    }

    /**
     *
     * @return type
     */
    public function productDetails()
    {
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\Service\Product', 'id', 'packageid');
    }

    public function getPaymentMethod()
    {
        if ($this->isTrial()) {
            return '7-Day Free Access';
        }

        if ($this->isDev()) {
            return 'Free Access';
        }

        return getGatewayName($this->paymentmethod);
    }

    public function getNextDueDate($format = true)
    {
        if ($format) {
            if (!$this->nextduedate || $this->isDev()) {
                return '-';
            }

            if ($this->isMagentoTrial()) {
                return date('M jS, Y', strtotime($this->regdate." +7 days"));
            }

            if (($this->isOpenSource() || $this->isOneTime() || $this->isFree()) && !$this->isMagentoTrial()) {
                return 'Never';
            }



            return date('M jS, Y', strtotime($this->nextduedate));
        } else {
            return $this->nextduedatea;
        }
    }

    public function getDownloadStatus()
    {
        $invoicesStatus = [];

        foreach ($this->invoices() as $invoice) {
            isset($invoicesStatus[$invoice->status]) ? : $invoicesStatus[$invoice->status] = [];
            $invoicesStatus[$invoice->status][] = $invoice;
        }

        $latestPaidInvoice = isset($invoicesStatus['Paid']) ? current($invoicesStatus['Paid']) : null;
        $latestUnpaidInvoice = isset($invoicesStatus['Unpaid']) ? current($invoicesStatus['Unpaid']) : null;

        $oneTimeWithInvoice = '';
        $oneTimeWithOutInvoice = '';
        $recurringWithInitialInvoice = '';
        $recurringWithRenewalInvoice = '';
        $recurringWithOutInvoice = '';
        $opensourceNeedVerification = false;

        //Disable download form for Cancelled and Fraud
        if (in_array($this->domainstatus, ['Cancelled', 'Fraud']) || $this->isDev()) {
            return [
                'disableDownlodForm' => true
            ];
        }

        //
        if ($this->isTrial() && $this->domainstatus == 'Active') {
            return [
                'disableDownlodForm' => false
            ];
        }

        if ($this->isOpenSource() && $this->domainstatus == 'Active') {
            $customFields = $this->client->getClientCustomFields();
            $services = $this->client->getActiveServiceTypes(date('Y-m-d', strtotime('-1 month')));

            if (!$customFields['opensource'] && (!$services['licensed'] && !$services['open_source'])) {
                $opensourceNeedVerification = true;
            }
        }

        if ($this->isOneTime()) {
            $isValid = false;
            if ($latestPaidInvoice) {
                $invoiceDate = new \DateTime($latestPaidInvoice->datepaid);
                $currentDate = new \DateTime();
                $interval = $invoiceDate->diff($currentDate);

                if ($interval->y) {
                    $oneTimeWithInvoice = $latestPaidInvoice->id;
                } else {
                    $isValid = true;
                }
            }

            if (!$isValid) {
                //Has unpaid subsequent support invoice?
                if ($latestUnpaidInvoice) {
                    if (strtotime($latestUnpaidInvoice->duedate) < strtotime(date('Y-m-d'))) {
                        $oneTimeWithInvoice = $latestUnpaidInvoice->id;
                    }
                }
                //has paif invoice? We have to check last invoice date
                elseif ($latestPaidInvoice) {
                    $invoiceDate = new \DateTime($latestPaidInvoice->datepaid);
                    $currentDate = new \DateTime();
                    $interval = $invoiceDate->diff($currentDate);

                    if ($interval->y) {
                        $oneTimeWithInvoice = $latestPaidInvoice->id;
                    }
                }
                //Oh no! We have not invoices!
                else {
                    $oneTimeWithOutInvoice = true;
                }
            }
        } elseif ($this->isRecurring() && $this->domainstatus != 'Active') {
            if ($latestUnpaidInvoice) {
                if ($this->order && $this->order->invoice && $this->order->invoice->id == $latestUnpaidInvoice->id) {
                    $recurringWithInitialInvoice = $latestUnpaidInvoice->id;
                } else {
                    $recurringWithRenewalInvoice = $latestUnpaidInvoice->id;
                }
            } else {
                $recurringWithOutInvoice = true;
            }
        } elseif ($this->isOpenSource() && $this->domainstatus != 'Active') {
            if ($latestUnpaidInvoice) {
                if ($this->order && $this->order->invoice && $this->order->invoice->id == $latestUnpaidInvoice->id) {
                    $oneTimeWithInvoice = $latestUnpaidInvoice->id;
                } else {
                    $oneTimeWithInvoice = $latestUnpaidInvoice->id;
                }
            } else {
                $oneTimeWithOutInvoice = true;
            }
        }

        /* if($_SESSION['adminid'] == 3)
          {
          echo '<pre>';
          var_dump($recurringWithInitialInvoice);
          var_dump($recurringWithRenewalInvoice);
          die(print_r($invoicesStatus));
          } */


        return [
            'disableDownlodForm' => $oneTimeWithInvoice || $oneTimeWithOutInvoice || $recurringWithInitialInvoice || $recurringWithRenewalInvoice || $recurringWithOutInvoice || $opensourceNeedVerification,
            'opensourceNeedVerification' => $opensourceNeedVerification,
            'unpaidDownloadForm' => [
                'oneTimeWithInvoice' => $oneTimeWithInvoice,
                'oneTimeWithOutInvoice' => $oneTimeWithOutInvoice,
                'recurringWithInitialInvoice' => $recurringWithInitialInvoice,
                'recurringWithRenewalInvoice' => $recurringWithRenewalInvoice,
                'recurringWithOutInvoice' => $recurringWithOutInvoice,
            ]
        ];
    }
}