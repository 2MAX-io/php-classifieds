<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomFieldJoinCategoryRepository")
 * @ORM\Table(
 *      uniqueConstraints={
 *          @UniqueConstraint(name="unique_field_category_pair", columns={"custom_field_id", "category_id"}),
 *      }
 * )
 * @UniqueEntity(fields={"customField", "category"}, message="This custom field is already assigned to this category")
 */
class CustomFieldJoinCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var CustomField
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CustomField", inversedBy="categoriesJoin")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customField;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="customFieldsJoin")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $sort;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getCustomField(): ?CustomField
    {
        return $this->customField;
    }

    public function setCustomField(?CustomField $customField): self
    {
        $this->customField = $customField;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
