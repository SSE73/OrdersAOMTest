<?php

namespace XLiteWeb\tests;

/**
 * @author cerber
 */
class testOrdersAOM extends \XLiteWeb\AXLiteWeb
{
    protected $order_number = 5;

    public function testChangeShippingStatus()
    {

        $adminOrder = $this->openAdminOrder();

        $getPreviousFulfilmentHistory = $adminOrder->selectStatus(false,'shipping');
        $getPreviousFulfilment        = $adminOrder->selectStatus(true,'shipping');

        $adminOrder->saveChanges();

        $getStatusFulfilment = $adminOrder->selectStatus(false,'shipping');

        $this->assertEquals($getPreviousFulfilment, $getStatusFulfilment,'Shipping Not Change');

        $isShippingHistory = $adminOrder->isHistory('shipping');

        $this->assertTrue($isShippingHistory,"измененный  Shipping не записался в историю");

    }

    public function testChangePaymentStatus()
    {
        $adminOrder = $this->openAdminOrder();

        $getPreviousPaymentHistory = $adminOrder->selectStatus(false,'payment');
        $getPreviousPayment = $adminOrder->selectStatus(true,'payment');

        $adminOrder->saveChanges();

        $getStatusPayment    = $adminOrder->selectStatus(false,'payment');

        $this->assertEquals($getPreviousPayment, $getStatusPayment,'Payment Not Change');

        $isPaimentHistory = $adminOrder->isHistory('payment');

        $this->assertTrue($isPaimentHistory,"измененный  Payment не записался в историю");
    }

    public function testAddProduct()
    {

        $adminOrder = $this->openAdminOrder();

        $adminOrder->addProduct();

        $sumProductPrevious = $adminOrder->sumProductPrevious();
        $sumTotalProducts = $adminOrder->getOrderTotal();

        $this->assertEquals($sumProductPrevious, $sumTotalProducts, 'Режим Добавление продукта До нажатия клавиши Save change Общая Сумма и Суммы вместе с дополненными продуктами не верны');

        $adminOrder->saveChanges();

        $sumProducts = $adminOrder->sumCurrentStateProduct();
        $sumTotalProducts = $adminOrder->getOrderTotal();

        $this->assertEquals($sumProducts, $sumTotalProducts, 'Режим Добавление продукта После нажатия клавиши Save change Общая Сумма и Суммы продуктов не верны');

        $isAddProductHistory = $adminOrder->isHistory('addProduct');

        $this->assertTrue($isAddProductHistory,"Добавленный Товар не записался в историю");

    }

    public function testDeleteProduct()
    {

        $adminOrder = $this->openAdminOrder();

        $adminOrder->deleteProductOrder();

        $sumProductPrevious = $adminOrder->sumDelProductPrevious();
        $sumTotalProducts = $adminOrder->getOrderTotal();

        $this->assertEquals($sumProductPrevious, $sumTotalProducts, 'Режим Удаление До нажатия клавиши Save. Общая Сумма и Суммы вместе с дополненными продуктами не верны');

        $adminOrder->saveChanges();
        //$adminOrder->clickAlert();
        //$adminOrder->clickAlert();

        $sumProducts = $adminOrder->sumCurrentStateProduct();
        $sumTotalProducts     = $adminOrder->getOrderTotal();

        $this->assertEquals($sumProducts, $sumTotalProducts, 'Режим Удаление продукта После нажатия клавиши Save. Общая Сумма и Суммы продуктов не верны');

        $isDelProductHistory = $adminOrder->isHistory('deleteProduct');

        $this->assertTrue($isDelProductHistory,"Удаленный Товар не записался в историю");

    }

    public function testAddDiscount()
    {
        $adminOrder = $this->openAdminOrder();

        $adminOrder->discountProduct(3);

        $sumPreviousTotalAddDiscount = $adminOrder->sumTotalAddDiscount();
        $sumGrandTotalProducts = $adminOrder->getOrderGrandTotal();

        $this->assertEquals($sumPreviousTotalAddDiscount, $sumGrandTotalProducts, 'Режим Discount До нажатия клавиши Save. Общая Сумма (Grand Total) и Суммы (SubTotal - Discount + Courier) не верны');

        $adminOrder->saveChanges();

        $sumTotalAddDiscount = $adminOrder->sumTotalAddDiscount();
        $sumGrandTotalProducts = $adminOrder->getOrderGrandTotal();

        $this->assertEquals($sumTotalAddDiscount, $sumGrandTotalProducts, 'Режим Discount После нажатия клавиши Save. Общая Сумма (Grand Total) и Суммы (SubTotal - Discount + Courier) не верны');

        $isDiscountHistory = $adminOrder->isHistory('addDiscount');

        $this->assertTrue($isDiscountHistory,"Дисконт не записался в историю");

    }

    public function testAddTracking()
    {

        $adminOrder = $this->openAdminOrder();

        $adminOrder->addTracking("tracking 123");

        $valuePreviousTracking = "tracking 123";

        $adminOrder->saveChanges();

        $valueTracking = $adminOrder->get_txtTracking->getText();

        $this->assertEquals($valuePreviousTracking, $valueTracking, 'Режим Tracking Не верно записано в БД');

        $isTrackingHistory = $adminOrder->isHistory('addTracking');

        $this->assertTrue($isTrackingHistory,"Созданный Трекинг не записался в историю");

    }

    public function testAddStaffnote()
    {

        $adminOrder = $this->openAdminOrder();

        $adminOrder->addStaffnote("Staff Note Added");

        $valuePreviousStaffone = "Staff Note Added";

        $adminOrder->saveChanges();

        $valueStaffnote = $adminOrder->get_staffNoteEmptyTextArea->getText();

        $this->assertEquals($valuePreviousStaffone, $valueStaffnote, 'Режим Staff note Не верно записано в БД');

        $isStaffnoteHistory = $adminOrder->isHistory('addStaffnote');

        $this->assertTrue($isStaffnoteHistory, "Созданный Staffnote не записался в историю");
    }

    public function testAddCustomernotenote()
    {

        $adminOrder = $this->openAdminOrder();

        $adminOrder->addCustomernote("Customer Note Added");

        $valuePreviousCustomerNote = "Customer Note Added";

        $adminOrder->saveChanges();

        $valueCustomerNote = $adminOrder->get_customerNoteEmptyTextArea->getText();

        $this->assertEquals($valuePreviousCustomerNote, $valueCustomerNote, 'Режим Customer note Не верно записано в БД');

        $isCustomernoteHistory = $adminOrder->isHistory('addCustomernote');

        $this->assertTrue($isCustomernoteHistory,"Созданный Customernote не записался в историю");

    }

    public function testChangeEmailCustomer()
    {
        $adminOrder = $this->openAdminOrder();

        $adminOrder->changeCustomerEmail("bit-bucket@examp.com");

        $valuePreviousEmailCustomer = "bit-bucket@examp.com";

        $adminOrder->saveChanges();

        $valueEmailCustomer = $adminOrder->get_customerEmailValue->getText();

        $this->assertEquals($valuePreviousEmailCustomer, $valueEmailCustomer,'Режим Change Email Customer Не верно записано в БД');
    }

    public function openAdminOrder()
    {

        $adminOrder = $this->AdminOrder;
        $this->assertTrue($adminOrder->load(true,$this->order_number), 'Error loading Admin Order page.');
        return $adminOrder;

    }

}
