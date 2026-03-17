<?php

return [
    'fetched' => 'Orders Fetched Successfully',
    'shown' => 'Order Fetched Successfully',
    'created' => 'Order Placed Successfully',
    'updated' => 'Order Updated Successfully',
    'reordered' => 'Order Reordered Successfully',
    'deleted' => 'Order Deleted Successfully',
    'canceled' => 'Order Canceled Successfully',
    'payment_failed_retry' => 'Payment failed, your cart has been restored. Please try again.',
    'error_create' => 'Failed to create order',
    'error_cancel' => 'Failed to cancel order',
    'error_update' => 'Failed to update order',
    'error_reorder' => 'Failed to reorder order',
    'error_delete' => 'Failed to delete order',
    'error_address' => 'Address is required for this delivery method',
    'error_show' => 'Failed to fetch order',
    'error_fetch' => 'Failed to fetch orders',
    'notification_title' => 'New Order',
    'notification_body' => 'You have a new order',


    'repaid' => 'Order Repaid Successfully',
    'error_repay' => 'Failed to repay order',
    // validations of store order request

    'address_id_required_with' => 'The address field is required when a delivery method is not In-Store Pickup.',
    'address_id_exists' => 'The selected address is invalid.',
    'delivery_method_id_exists' => 'The selected delivery method is invalid.',
    'payment_method_id_required' => 'The payment method field is required.',
    'payment_method_id_exists' => 'The selected payment method is invalid.',
    'promo_code_exists' => 'The entered promo code is invalid or expired.',
    'phone_phone' => 'The phone number must be a valid Saudi Arabian phone number.',
    'name_string' => 'The name must be a string.',
    'name_max' => 'The name may not be greater than 255 characters.',
    'email_string' => 'The email must be a string.',
    'email_email' => 'The email must be a valid email address.',
    'gateway_required' => 'The payment gateway field is required when the payment method is not cash.',
    'gateway_in' => 'The selected payment gateway is invalid.',
    'gateway_valid' => 'The selected gateway is not available for this payment method.',

];
