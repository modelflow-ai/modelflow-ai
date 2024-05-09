<?php

namespace ModelflowAi\Image\Adapter;

interface ImageAdapterFactoryInterface
{
    /**
     * @param array{
     *     model: string,
     * } $options
     */
    public function createImageAdapter(array $options): AiImageAdapterInterface;
}
