<?php
declare(strict_types = 1);

namespace app\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The item is a sold entity. Items include all sorts of blocks, in-game items, permissions,
 * in-game currency, regions and so on. Since the item represents an in-game essence it can
 * not be sold independently, it must be tied to a certain {@see \app\Entity\Product}.
 *
 * <p>You can create 1 item, for example, diamond, specify for him in-game identification
 * data and then, bind to several products. Each product will be sold on a separate
 * server, so you can configure the size of the stack of items sold, as well as its
 * magnitude. This is convenient, since, as a rule, on different servers, the value
 * of the same item can vary significantly.</p>
 *
 * <p>The type of the object determines what it is in the game. As I said above, there
 * are such types of objects as a common block / common object (type is predefined in
 * a constant {@see \app\Services\Item\Type::ITEM}), a group of privileges (type is
 * predefined in a constant {@see \app\Services\Item\Type::PERMGROUP}).</p>
 *
 * <p>If the item being sold has a type {@see \app\Services\Item\Type::ITEM}, then this
 * item can be enchanted. Read more in {@see \app\Entity\Enchantment}.</p>
 *
 * Visual presentation:
 *
 *                                 +-------------------+
 *                                 |   .     '     ,   |
 *                                 |     _________     |
 *                                 |  _ /_|_____|_\ _  |
 *                                 |    '. \   / .'    |
 *                                 |      '.\ /.'      |
 *                                 |        '.'        |
 *                                 +-------------------+
 *
 * <p>Diamond is {@see \app\Entity\Item}. Package is {@see \app\Entity\Product}.
 * Just like in the store, we can not sell the jewelry without packaging, so the item can not
 * be sold without binding it to the product.</p>
 *
 * @see \app\Entity\Product
 * @see \app\Entity\Enchantment
 * @see \app\Entity\EnchantmentItem
 *
 * @ORM\Entity
 * @ORM\Table(name="items")
 */
class Item
{
    /**
     * Item identifier.
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The name of the item that will be displayed on the store pages.
     *
     * @ORM\Column(name="`name`", type="string", length=255, nullable=false, unique=false)
     */
    private $name;

    /**
     * The item description is used to indicate additional information about the item for the
     * buyer.
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * Represents an in-game type of an item. Available Values: common item / block
     * ({@see \app\Services\Item\Type::ITEM}), permission group ({@see \app\Services\Item\Type::PERMGROUP}),
     * in-game currency ({@see \app\Services\Item\Type::CURRENCY}), region owner (({@see \app\Services\Item\Type::REGION_OWNER})),
     * region member ({@see \app\Services\Item\Type::REGION_MEMBER}) and executable command ({@see \app\Services\Item\Type::COMMAND}).
     *
     * @see \app\Services\Item\Type
     *
     * @ORM\Column(name="type", type="string", length=32, nullable=false)
     */
    private $type;

    /**
     * The name of the image file (along with the extension) that the item has. If null is used,
     * a standard image is used to display the item on the store pages.
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * For {@see \app\Entity\Item::type} = {@see \app\Services\Item\Type::ITEM} this field must be an in-game item
     * identifier is used to issue purchased products to the user in the game. It can contain, as data in the form
     * ID, ID:DATA, material id or any other data that uniquely identifies the item.
     *
     * For {@see \app\Entity\Item::type} = {@see \app\Services\Item\Type::PERMGROUP} this field must be contains
     * in-game permission group identifier.
     *
     * For {@see \app\Entity\Item::type} = {@see \app\Services\Item\Type::REGION_OWNER} || {@see \app\Services\Item\Type::REGION_MEMBER}
     * this field must contains in-game region identifier.
     *
     * For {@see \app\Entity\Item::type} = {@see \app\Services\Item\Type::CURRENCY} this field
     * must be a nullable.
     *
     * @ORM\Column(name="signature", type="string", length=255, nullable=true)
     */
    private $signature;

    /**
     * Additional information that is used by means of issuing items in the game.
     * <p>The data in NBT format is stored here.</p>
     *
     * @see https://minecraft.gamepedia.com/NBT_tag
     *
     * @ORM\Column(name="extra", type="text", nullable=true)
     */
    private $extra;

    /**
     * Products to which this item is attached.
     *
     * @ORM\OneToMany(targetEntity="app\Entity\Product", mappedBy="item", cascade={"remove"})
     */
    private $products;

    /**
     * Enchantments that are superimposed on this item.
     * <p>Available only if {@see \app\Entity\Item::type} = {@see \app\Services\Item\Type::ITEM}.</p>
     *
     * @ORM\OneToMany(targetEntity="app\Entity\EnchantmentItem", mappedBy="item", cascade={"persist", "remove"})
     */
    private $enchantmentItems;

    /**
     * Item constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $signature
     */
    public function __construct(string $name, string $type, ?string $signature)
    {
        $this->setName($name);
        $this->setType($type);
        $this->setSignature($signature);
        $this->products = new ArrayCollection();
        $this->enchantmentItems = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Item
     */
    public function setName(string $name): Item
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     *
     * @return Item
     */
    public function setDescription(?string $description): Item
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Item
     */
    public function setType(string $type): Item
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param null|string $image
     *
     * @return Item
     */
    public function setImage(?string $image): Item
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSignature(): ?string
    {
        return $this->signature;
    }

    /**
     * @param string|null $signature
     *
     * @return Item
     */
    public function setSignature(?string $signature): Item
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExtra(): ?string
    {
        return $this->extra;
    }

    /**
     * @param null|string $extra
     *
     * @return Item
     */
    public function setExtra(?string $extra): Item
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @return Collection
     */
    public function getEnchantmentItems(): Collection
    {
        return $this->enchantmentItems;
    }

    /**
     * Creates string representation of object.
     * <p>For example:</p>
     * <p>app\Entity\Item(id=1, name="Diamond", type="item", signature="264")</p>
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '%s(id=%d, name="%s", type="%s", signature="%s")',
            self::class,
            $this->getId(),
            $this->getName(),
            $this->getType(),
            $this->getSignature()
        );
    }
}
