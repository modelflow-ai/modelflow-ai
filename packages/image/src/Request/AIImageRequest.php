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

namespace ModelflowAi\Image\Request;

use ModelflowAi\Core\Behaviour\CriteriaBehaviour;
use ModelflowAi\Core\Request\Criteria\AIRequestCriteriaCollection;
use ModelflowAi\Image\Request\Task\AIImageRequestActionInterface;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Request\Value\OutputFormat;
use ModelflowAi\Image\Response\AIImageResponse;

class AIImageRequest implements CriteriaBehaviour
{
    /**
     * @var callable
     */
    protected $requestHandler;

    public function __construct(
        public AIImageRequestActionInterface $task,
        public ImageFormat $imageFormat,
        public OutputFormat $format,
        private readonly AIRequestCriteriaCollection $criteria,
        callable $requestHandler,
    ) {
        $this->requestHandler = $requestHandler;
    }

    public function matches(array $criteria): bool
    {
        return $this->criteria->matches($criteria);
    }

    public function execute(): AIImageResponse
    {
        return \call_user_func($this->requestHandler, $this);
    }
}
