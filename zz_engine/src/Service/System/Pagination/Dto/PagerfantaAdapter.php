<?php

declare(strict_types=1);

namespace App\Service\System\Pagination\Dto;

use Pagerfanta\Adapter\AdapterInterface;

class PagerfantaAdapter implements AdapterInterface
{
    /**
     * @var int
     */
    private $nbResults;

    /**
     * @var callable
     */
    private $sliceCallback;

    public function __construct(int $nbResults, callable $sliceCallback)
    {
        $this->nbResults = $nbResults;
        $this->sliceCallback = $sliceCallback;
    }

    public function getNbResults(): int
    {
        return $this->nbResults;
    }

    /**
     * @param int|mixed $offset
     * @param int|mixed $length
     *
     * @return mixed[]
     */
    public function getSlice($offset, $length): iterable
    {
        return ($this->sliceCallback)($offset, $length);
    }
}
