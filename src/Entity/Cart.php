<?php

namespace App\Entity;

use App\Repository\CartRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Cart
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'cart', targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    private ?Client $client = null;

    /**
     * @var array<int, array{productId: int, quantity: int}>
     */
    #[ORM\Column(type: 'json')]
    private array $products = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts(array $products): static
    {
        $this->products = $products;

        return $this;
    }

    public function getQuantityFor(int $productId): int
    {
        foreach ($this->products as $line) {
            if ($line['productId'] === $productId) {
                return $line['quantity'];
            }
        }

        return 0;
    }

    public function setQuantityFor(int $productId, int $quantity): static
    {
        $products = $this->products;
        $found = false;

        foreach ($products as $key => $line) {
            if ($line['productId'] === $productId) {
                if ($quantity <= 0) {
                    unset($products[$key]);
                } else {
                    $products[$key]['quantity'] = $quantity;
                }
                $found = true;
                break;
            }
        }

        if (!$found && $quantity > 0) {
            $products[] = ['productId' => $productId, 'quantity' => $quantity];
        }

        $this->products = array_values($products);

        return $this;
    }

    public function removeProduct(int $productId): static
    {
        return $this->setQuantityFor($productId, 0);
    }
}