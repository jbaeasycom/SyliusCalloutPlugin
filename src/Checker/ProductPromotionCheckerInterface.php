<?php

declare(strict_types=1);

namespace Setono\SyliusCalloutsPlugin\Checker;

use Sylius\Component\Core\Model\ProductInterface;

interface ProductPromotionCheckerInterface
{
    public function isOnPromotion(ProductInterface $product): bool;
}
