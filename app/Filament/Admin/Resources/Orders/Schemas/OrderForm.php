<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات العميل')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('order_number')
                                ->label('رقم الطلب')
                                ->disabled(),
                            TextInput::make('customer_name')
                                ->label('اسم العميل')
                                ->required(),
                            TextInput::make('customer_phone')
                                ->label('الجوال'),
                        ]),
                        TextInput::make('customer_email')
                            ->label('البريد')
                            ->email(),
                    ])
                    ->columnSpanFull(),

                Section::make('حالة التشغيل')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('order_status')
                                ->label('حالة الطلب')
                                ->options([
                                    'pending_payment' => 'بانتظار الدفع',
                                    'pending_bank_review' => 'مراجعة تحويل',
                                    'confirmed' => 'مؤكد',
                                    'processing' => 'قيد التجهيز',
                                    'ready_to_ship' => 'جاهز للشحن',
                                    'shipped' => 'تم الشحن',
                                    'delivered' => 'تم التسليم',
                                    'cancelled' => 'ملغي',
                                    'returned' => 'مرتجع',
                                    'refunded' => 'مسترد',
                                ])
                                ->required(),
                            Select::make('payment_status')
                                ->label('حالة الدفع')
                                ->options([
                                    'unpaid' => 'غير مدفوع',
                                    'initiated' => 'بدأ الدفع',
                                    'paid' => 'مدفوع',
                                    'failed' => 'فشل',
                                    'cancelled' => 'ملغي',
                                    'refunded' => 'مسترد',
                                    'partially_refunded' => 'استرداد جزئي',
                                ])
                                ->required(),
                            Select::make('shipping_method_id')
                                ->label('طريقة الشحن')
                                ->relationship('shippingMethod', 'name_ar')
                                ->searchable()
                                ->preload(),
                        ]),
                        Textarea::make('admin_notes')
                            ->label('ملاحظات الإدارة')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('القيم المالية')
                    ->schema([
                        Grid::make(5)->schema([
                            TextInput::make('subtotal_base')->label('الفرعي')->disabled(),
                            TextInput::make('discount_base')->label('الخصم')->disabled(),
                            TextInput::make('shipping_base')->label('الشحن')->disabled(),
                            TextInput::make('tax_base')->label('الضريبة')->disabled(),
                            TextInput::make('total_base')->label('الإجمالي')->disabled(),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
