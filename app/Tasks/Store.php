<?php

declare(strict_types=1);

namespace App\Tasks;

/**
 * Табл. 4.2 №1 (варіант 6): склад із колекцією Product.
 */
final class Store
{
    /** @var array<int, Product> */
    private array $products = [];

    public function __construct(
        private string $storeId,
        private string $name,
    ) {}

    public function addProduct(Product $product): void
    {
        $this->products[$product->getId()] = $product;
    }

    public function removeProduct(int $id): bool
    {
        if (! array_key_exists($id, $this->products)) {
            return false;
        }

        unset($this->products[$id]);

        return true;
    }

    /**
     * @return array<int, Product>
     */
    public function searchProduct(string $name): array
    {
        return array_values(array_filter(
            $this->products,
            static fn (Product $product): bool => $product->matchesName($name),
        ));
    }

    /**
     * @return array<int, Product>
     */
    public function getProducts(): array
    {
        return array_values($this->products);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
