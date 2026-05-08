<?php

namespace App\Enums;

enum DiscountTargetType: string
{
    case ALL_STORE = 'all_store';
    case PRODUCT_GROUP = 'product_group';
    case CATEGORY = 'category';
    case PRODUCT = 'product';
}
