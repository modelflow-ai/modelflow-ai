<?php

declare(strict_types=1);

/*
 * This file is part of the Modelflow AI package.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ModelflowAi\Image\Request\Builder;

use ModelflowAi\DecisionTree\Criteria\CriteriaCollection;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;
use ModelflowAi\Image\Request\Action\AIImageRequestActionInterface;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Request\Value\OutputFormat;

final class AIImageRequestBuilder
{
    private CriteriaCollection $criteriaCollection;

    private ImageFormat $imageFormat = ImageFormat::PNG;

    private OutputFormat $format = OutputFormat::STREAM;

    /**
     * @var callable
     */
    private $requestHandler;

    private function __construct(
        callable $requestHandler,
    ) {
        $this->requestHandler = $requestHandler;

        $this->criteriaCollection = new CriteriaCollection();
    }

    public static function create(\Closure $handler): self
    {
        return new self($handler);
    }

    public function imageFormat(ImageFormat $imageFormat): self
    {
        $this->imageFormat = $imageFormat;

        return $this;
    }

    /**
     * @param CriteriaInterface|CriteriaInterface[] $criteria
     */
    public function addCriteria(CriteriaInterface|array $criteria): self
    {
        $criteria = \is_array($criteria) ? $criteria : [$criteria];

        $this->criteriaCollection = new CriteriaCollection(
            \array_merge($this->criteriaCollection->all, $criteria),
        );

        return $this;
    }

    public function textToImage(string $prompt): TextToImageActionBuilder
    {
        return new TextToImageActionBuilder($this, $prompt);
    }

    public function as(OutputFormat $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function build(AIImageRequestActionInterface $task): AIImageRequest
    {
        return new AIImageRequest($task, $this->imageFormat, $this->format, $this->criteriaCollection, $this->requestHandler);
    }
}
