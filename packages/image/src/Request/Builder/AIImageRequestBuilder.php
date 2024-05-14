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

use ModelflowAi\Core\Request\Criteria\AiCriteriaInterface;
use ModelflowAi\Core\Request\Criteria\AIRequestCriteriaCollection;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Task\AIImageRequestActionInterface;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Request\Value\OutputFormat;

final class AIImageRequestBuilder
{
    private AIRequestCriteriaCollection $criteria;

    private ImageFormat $imageFormat = ImageFormat::PNG;

    private OutputFormat $format = OutputFormat::STREAM;

    /**
     * @var callable
     */
    private $requestHandler;

    public function __construct(
        callable $requestHandler,
    ) {
        $this->requestHandler = $requestHandler;

        $this->criteria = new AIRequestCriteriaCollection();
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
     * @param AiCriteriaInterface|AiCriteriaInterface[] $criteria
     */
    public function addCriteria(AiCriteriaInterface|array $criteria): self
    {
        $criteria = \is_array($criteria) ? $criteria : [$criteria];

        $this->criteria = new AIRequestCriteriaCollection(
            \array_merge($this->criteria->criteria, $criteria),
        );

        return $this;
    }

    public function textToImage(string $prompt): TextToImageBuilder
    {
        return new TextToImageBuilder($this, $prompt);
    }

    public function as(OutputFormat $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function build(AIImageRequestActionInterface $task): AIImageRequest
    {
        return new AIImageRequest($task, $this->imageFormat, $this->format, $this->criteria, $this->requestHandler);
    }
}
