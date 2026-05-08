<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name_ar' => 'الدفع عند الاستلام',
                'name_en' => 'Cash on Delivery',
                'code' => 'cash_on_delivery',
                'type' => 'cash',
                'description_ar' => 'ادفع نقدًا عند استلام الطلب.',
                'description_en' => 'Pay cash when you receive your order.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'حوالة بنكية',
                'name_en' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'type' => 'manual',
                'description_ar' => 'حوّل المبلغ إلى الحساب البنكي ثم ارفع إيصال التحويل.',
                'description_en' => 'Transfer the amount to the bank account then upload the receipt.',
                'is_active' => true,
                'sort_order' => 2,
                'settings' => [
                    'bank_name' => null,
                    'account_name' => null,
                    'account_number' => null,
                    'iban' => null,
                    'instructions_ar' => 'يرجى رفع إيصال التحويل بعد إتمام الدفع.',
                    'instructions_en' => 'Please upload the transfer receipt after completing payment.',
                ],
            ],
            [
                'name_ar' => 'الدفع بالبطاقة',
                'name_en' => 'Card Payment',
                'code' => 'card',
                'type' => 'online',
                'description_ar' => 'ادفع باستخدام البطاقة البنكية عبر بوابة دفع إلكترونية.',
                'description_en' => 'Pay by bank card through an online payment gateway.',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
