<?php

declare(strict_types=1);

namespace Setono\SyliusCalloutPlugin\Message\Handler;

use Setono\SyliusCalloutPlugin\Callout\Provider\EligibleCalloutsProviderInterface;
use Setono\SyliusCalloutPlugin\Message\Command\AssignEligibleCalloutsToProduct;
use Setono\SyliusCalloutPlugin\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AssignEligibleCalloutsToProductHandler implements MessageHandlerInterface
{
    private EligibleCalloutsProviderInterface $eligibleCalloutsProvider;

    private ProductRepositoryInterface $productRepository;

    public function __construct(
        EligibleCalloutsProviderInterface $eligibleCalloutsProvider,
        ProductRepositoryInterface $productRepository
    ) {
        $this->eligibleCalloutsProvider = $eligibleCalloutsProvider;
        $this->productRepository = $productRepository;
    }

    public function __invoke(AssignEligibleCalloutsToProduct $message): void
    {
        /** @var ProductInterface|null $product */
        $product = $this->productRepository->find($message->getProductId());
        if (!$product instanceof ProductInterface) {
            return;
        }

        $product->setCallouts(
            $this->eligibleCalloutsProvider->getEligibleCallouts($product)
        );

        // We don't want this here
        // as flushing here causing Doctrine Event Listener
        // fall to deadlock
        // $this->productManager->flush();
    }
}
