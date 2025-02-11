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

namespace ModelflowAi\Completion\Adapter;

interface AICompletionAdapterFactoryInterface
{
    /**
     * @param array{
     *     model: string,
     *     image_to_text: bool,
     *     functions: bool,
     *     priority: int,
     * } $options
     */
    public function createCompletionAdapter(array $options): AICompletionAdapterInterface;
}
