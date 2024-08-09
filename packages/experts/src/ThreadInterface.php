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

namespace ModelflowAi\Experts;

use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Response\AIChatResponse;

interface ThreadInterface
{
    public function addContext(string $key, mixed $data): self;

    public function run(): AIChatResponse;

    public function addMessage(AIChatMessage $message): self;

    /**
     * @param AIChatMessage[] $messages
     */
    public function addMessages(array $messages): self;
}
